<?php
class Post
{
	private $user_obj;
	private $conn;

	public function __construct($conn, $user)
	{
		$this->conn = $conn;
		$this->user_obj = new User($conn, $user);
	}

	public function submitPost($body, $user_to, $imageName)
	{
		$body = strip_tags($body); //removes HTML tags
		$body = str_replace('\r\n', '\n', $body);
		$body = nl2br($body);
		$body = str_replace('<br />', '<br/> ', $body); //To fix embedded YouTube videos
		$body = mysqli_real_escape_string($this->conn, $body);

		$check_empty = preg_replace('/\s+/', '', $body); //removes all spaces

		//Checks if post is empty
		if($check_empty != "")
		{

			$body_array = preg_split("/\s+/", $body);

			//Code has an issue and is spliting <br /> which is causing src= to be broken
			foreach($body_array as $key => $value) 
			{
				if(strpos($value, "www.youtube.com/watch?v=") !== false) 
				{
					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe class=\'yt_video\' width=\'560\' height=\'315\' src=\'" . $value ."\' allowfullscreen ></iframe><br>";
					$body_array[$key] = $value;
				}
			}
			$body = implode(" ", $body_array);


			//Current DateTime
			$date_added = date("Y-m-d H:i:s");

			//Get username
			$added_by = $this->user_obj->getUsername();

			//If user is on their profile, user_to is 'none'
			if($user_to == $added_by)
			{
				$user_to = "none";
			}
			?><script>
				alert("Hello");
			</script><?php

			//Insert post
			$query = mysqli_query($this->conn, "INSERT INTO `posts` (`id`, `body`, `added_by`, `user_to`, `date_added`, `user_closed`, `deleted`, `likes`, `image`) VALUES (NULL, '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");
			$returned_id = mysqli_insert_id($this->conn);

			//Insert notification
			if($user_to != 'none')
			{
				$notification = new Notification($this->conn, $added_by);
				$notification->insertNotification($returned_id, $user_to, "profile_post");
			}

			//Update post count for user
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->conn, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");


			/****************************************************************
			 * Helper for calculateTrend
			 ****************************************************************/
			include("Post_stopwords.php");

			//Convert stop words into array - split at white space
			$stopWords = preg_split("/[\s,]+/", $stopWords);

			//Remove all punctionation
			$no_punctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

			//Check if user is posting a URL. If so, do not check for trending words
			if(strpos($no_punctuation, "height") === false && strpos($no_punctuation, "width") === false
			&& strpos($no_punctuation, "https") === false && strpos($no_punctuation, "youtube") === false)
			{
				//Convert users post (with punctuation removed) into array - split at white space
				$keywords = preg_split("/[\s,]+/", $no_punctuation);

				foreach($stopWords as $value) 
				{
					foreach($keywords as $key => $value2)
					{
						if(strtolower($value) == strtolower($value2))
							$keywords[$key] = "";
					}
				}

				foreach ($keywords as $value) 
				{
				    $this->calculateTrend(ucfirst($value));
				}
			}
		}
	}

	public function calculateTrend($term)
	{
		if($term != '')
		{
			$query = mysqli_query($this->conn, "SELECT title,hits FROM trends WHERE title='$term'");

			//Ammend the INSERT INTO to validate
			if(mysqli_num_rows($query) == 0)
				$insert_query = mysqli_query($this->conn, "INSERT INTO trends(title,hits) VALUES('$term','1')");
			else
				$insert_query = mysqli_query($this->conn, "UPDATE trends SET hits=hits+1 WHERE title='$term'");
		}
	}

	public function loadPostsFriends($data, $limit)
	{
		//Code for infinite scrolling (ajax)
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
		{
			$start = 0; //load 0th item of the table
		}
		else
		{
			$start = ($page - 1) * $limit;
		}


		$str = ""; //String to return
		$data_query = mysqli_query($this->conn, "SELECT id,body,added_by,user_to,date_added,image,deleted FROM posts WHERE deleted='no' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0)
		{
			$num_iterations = 0; //Numbers of results checked (not necesssarily posts)
			$count = 1;

			while($row = mysqli_fetch_array($data_query))
			{
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$imagePath = $row['image'];

				//Prepare user_to string so it can be included even if not posted to user
				if($row['user_to'] == "none")
				{
					$user_to = "";
				}
				else
				{
					$user_to_obj = new User($this->conn, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
				}

				//Check if user who posted has their account closed
				$added_by_obj = new User($conn, $added_by);
				if($added_by_obj->isClosed())
				{
					continue;
				}

				$user_logged_obj = new User($this->conn, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by))
				{

			
					//Part of the ajax code
					if($num_iterations++ < $start) //how many posts have been loads
						continue;

					//Part of the ajax code
					//As 10 posts are loaded at a time and $count++ per while loop
					//then this should result in only allowing ($limit * $count) posts (which is 100).
					if($count > $limit) 
					{
						break; // Once $limit posts have been loaded, break
					}
					else
					{
						$count++; // Otherwise keep counting
					}

					//Delete button for Post; see <script> at end of while-loop
					if($userLoggedIn == $added_by)
					{
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					}
					else 
					{
						//Empty var can be included in $str, if($userLoggedIn != $added_by)
						$delete_button = "";
					}

					$postIdUrl = "http://" . $_SERVER[HTTP_HOST] . "/post.php?id=" . $id;
					$sharePostButton = "<button class='sharePostButton' id='$postIdUrl'>Share</button>";

					$user_details_query = mysqli_query($this->conn, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


					?>
					<script>
						function toggle<?php echo $id; ?>() 
						{
							var target = $(event.target);
							if (!target.is("a")) 
							{
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}
					</script>
					<?php

					//Check how many comments
					$comments_check = mysqli_query($this->conn, "SELECT post_id FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


				//Timeframe
				include("Post_timeframe.php");
				// $this->timeframe();
					//Load posted image
					if($imagePath != "")
					{
						$imageDiv = "<div class='postedImage'>
										<img src='$imagePath'>
									</div>";
					}
					else
					{
						$imageDiv = "";
					}

					//Show more preview for body
					if(strlen($body) > 100 && strpos($body, 'https://www.youtube.com/embed/') === false)
					{
						$preview_body = str_split($body, 100)[0] . '...';

						$body = "<div id='showMore_preview$id'>
									$preview_body
									<details>
										<summary onclick='showMore($id)' style='text-decoration: underline; color:blue;'>Show More</br></summary>
									</details>
								</div>

								<div id='showMore_full$id' style='display: none;'>
								$body
								</div>";
					}

					$str .=	"	<div class='status_post' onClick='javascript:toggle$id()'>
									<div class='post_profile_pic'>
										<img src='$profile_pic' width='50'>
									</div>

									<div class='posted_by' style='color:#ACACAC;'>
										<a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
										$delete_button
										$sharePostButton
									</div>

									<div id='post_body'>
										$body
										<br>
										$imageDiv
									</div>
									<div class='newsfeedPostOptions'>
										Comments: $comments_check_num&nbsp;&nbsp;&nbsp;&nbsp;
										<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
									</div>

								</div>
								<div class='post_comment' id='toggleComment$id' style='display:none;'>
									<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
								</div>
								<hr>
							";

				} //$user_logged_obj = new User($this->conn, $userLoggedIn);

				?>
				<script>
					$(document).ready(function() 
					{
						$('#post<?php echo $id; ?>').on('click', function() 
						{
							bootbox.confirm("Are you sure you want to delete this post?", function(result) 
							{
								$.post("includes/handlers/ajax_delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();
							});
						});
					});
				</script>
				<?php

			} //End while-loop

			//This is responsible for infinite scrolling
			//an (else if()) needs to be introduced
			//at present if the remaining posts are between 0-9 then 'noMorePosts' is run despite having more posts
			if($count > $limit)  
			{
				$str.=	"	<input type='hidden' class='nextPage' value='" . ($page + 1) . "'> 
							<input type='hidden' class='noMorePosts' value='false'>
						"; //hidden value
			}
			else
			{
				$str.=	"	
							<input type='hidden' class='noMorePosts' value='true'>
							<p style='text-align: centre;'> No more posts to show! </p>
						"; 
			}
		}

		echo $str;
	}

	public function loadProfilePosts($data, $limit)
	{
		//Code for infinite scrolling (ajax)
		$page = $data['page'];
		$profileUser = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
		{
			$start = 0; //load 0th item of the table
		}
		else
		{
			$start = ($page - 1) * $limit;
		}


		$str = ""; //String to return
		$sql = "SELECT id,body,added_by,date_added,image,user_to,deleted FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser')  ORDER BY id DESC";
		$data_query = mysqli_query($this->conn, $sql);

		if(mysqli_num_rows($data_query) > 0)
		{
			$num_iterations = 0; //Numbers of results checked (not necesssarily posts)
			$count = 1;

			while($row = mysqli_fetch_array($data_query))
			{
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$imagePath = $row['image'];

				//Part of the ajax code
				if($num_iterations++ < $start) //how many posts have been loads
					continue;

				//Part of the ajax code
				//As 10 posts are loaded at a time and $count++ per while loop
				//then this should result in only allowing ($limit * $count) posts (which is 100).
				if($count > $limit) 
				{
					break; // Once $limit posts have been loaded, break
				}
				else
				{
					$count++; // Otherwise keep counting
				}

				//Delete button for Post; see <script> at end of while-loop
				if($userLoggedIn == $added_by)
				{
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				}
				else 
				{
					//Empty var can be included in $str, if($userLoggedIn != $added_by)
					$delete_button = "";
				}

				$user_details_query = mysqli_query($this->conn, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];


				?>
				<script>
					function toggle<?php echo $id; ?>() 
					{
						var target = $(event.target);
						if (!target.is("a")) 
						{
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if(element.style.display == "block") 
								element.style.display = "none";
							else 
								element.style.display = "block";
						}
					}
				</script>
				<?php

				//Check how many comments
				$comments_check = mysqli_query($this->conn, "SELECT post_id FROM comments WHERE post_id='$id'");
				$comments_check_num = mysqli_num_rows($comments_check);


				//Timeframe
				include("Post_timeframe.php");

				if($imagePath != "")
					{
						$imageDiv = "<div class='postedImage'>
										<img src='$imagePath'>
									</div>";
					}
					else
					{
						$imageDiv = "";
					}

				//Show more preview for body
				if(strlen($body) > 100 && strpos($body, 'https://www.youtube.com/embed/') === false)
				{
					$preview_body = str_split($body, 100)[0] . '...';

					$body = "<div id='showMore_preview$id'>
								$preview_body
								<details>
									<summary onclick='showMore($id)' style='text-decoration: underline; color:blue;'>Show More</br></summary>
								</details>
							</div>

							<div id='showMore_full$id' style='display: none;'>
							$body
							</div>";
				}

				$str .=	"	<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$delete_button
								</div>

								<div id='post_body'>
									$body
									<br>
									$imageDiv
								</div>
								<div class='newsfeedPostOptions'>
									Comments: $comments_check_num&nbsp;&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>
						";

				?>
				<script>
					$(document).ready(function() 
					{
						$('#post<?php echo $id; ?>').on('click', function() 
						{
							bootbox.confirm("Are you sure you want to delete this post?", function(result) 
							{
								$.post("includes/handlers/ajax_delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();
							});
						});
					});
				</script>
				<?php

			} //End while-loop

			//This is responsible for infinite scrolling
			//an (else if()) needs to be introduced
			//at present if the remaining posts are between 0-9 then 'noMorePosts' is run despite having more posts
			if($count > $limit)  
			{
				$str.=	"	<input type='hidden' class='nextPage' value='" . ($page + 1) . "'> 
							<input type='hidden' class='noMorePosts' value='false'>
						"; //hidden value
			}
			else
			{
				$str.=	"	
							<input type='hidden' class='noMorePosts' value='true'>
							<p style='text-align: centre;'> No more posts to show! </p>
						"; 
			}
		}

		echo $str;
	}

	public function getSinglePost($post_id)
	{
		$userLoggedIn = $this->user_obj->getUsername();


		$opened_query = mysqli_query($this->conn, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");


		$str = ""; //String to return
		// $data_query = mysqli_query($this->conn, "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");
		$data_query = mysqli_query($this->conn, "SELECT id,body,added_by,user_to,date_added,deleted FROM posts WHERE deleted='no' AND id='$post_id'");

		if(mysqli_num_rows($data_query) > 0)
		{
			$row = mysqli_fetch_array($data_query);
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
			$date_time = $row['date_added'];

			//Prepare user_to string so it can be included even if not posted to user
			if($row['user_to'] == "none")
			{
				$user_to = "";
			}
			else
			{
				$user_to_obj = new User($this->conn, $row['user_to']);
				$user_to_name = $user_to_obj->getFirstAndLastName();
				$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
			}

			//Check if user who posted has their account closed
			$added_by_obj = new User($conn, $added_by);
			if($added_by_obj->isClosed())
			{
				return;
			}

			$user_logged_obj = new User($this->conn, $userLoggedIn);
			if($user_logged_obj->isFriend($added_by))
			{
				//Delete button for Post; see <script> at end of while-loop
				if($userLoggedIn == $added_by)
				{
					$deleteButton = "<button class='deleteButton btn-danger' id='post$id'>X</button>";
				}
				else 
				{
					//Empty var can be included in $str, if($userLoggedIn != $added_by)
					$deleteButton = "";
				}

				$user_details_query = mysqli_query($this->conn, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];


				?>
				<script>
					function toggle<?php echo $id; ?>() 
					{
						var target = $(event.target);
						if (!target.is("a")) 
						{
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if(element.style.display == "block") 
								element.style.display = "none";
							else 
								element.style.display = "block";
						}
					}
				</script>
				<?php

				//Check how many comments
				$comments_check = mysqli_query($this->conn, "SELECT post_id FROM comments WHERE post_id='$id'");
				$comments_check_num = mysqli_num_rows($comments_check);

				$postIdUrl = "http://" . $_SERVER[HTTP_HOST] . "/post.php?id=" . $id;
				$sharePostButton = "<button class='sharePostButton' id='$postIdUrl'>Share</button>";


				//Timeframe
				include("Post_timeframe.php");
				$str .=	"	<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$sharePostButton
									$deleteButton
									$postIdButton
								</div>

								<div id='post_body'>
									$body
									<br>
								</div>
								<div class='newsfeedPostOptions'>
									Comments: $comments_check_num&nbsp;&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>
						";
				?>
					<script>
						$(document).ready(function() 
						{
							$('#post<?php echo $id; ?>').on('click', function() 
							{
								bootbox.confirm("Are you sure you want to delete this post?", function(result) 
								{
									$.post("includes/handlers/ajax_delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();
								});
							});
						});
					</script>
			<?php
			} //$user_logged_obj = new User($this->conn, $userLoggedIn);
			else
			{
				echo "<p>You cannot see this post because you are not friends with this user</p>";
				return;
			}
		}
		else
		{
			echo "<p>No post found. If you clicked a link, it may be broken.</p>";
			return;
		}

		echo $str;
	}

	public function deletePost($postId)
	{
		$query = mysqli_query($this->conn, "UPDATE posts SET deleted='yes' WHERE id='$postId'");

		if($query)
			return true;
		else
			return false;
	}

	public function fetchUserDetails()
	{
		$userLoggedIn = $this->user_obj->getUsername();
		$userDetailsQuery = mysqli_query($this->conn, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$userArray = mysqli_fetch_array($userDetailsQuery);

		return $userArray;
	}

	public function getLikesArray($postId)
	{
		$getLikes = mysqli_query($this->conn, "SELECT likes, added_by FROM posts WHERE id='$postId'");
		$row = mysqli_fetch_array($getLikes);
		return $row;
	}

	public function likeUserDetails($userLiked)
	{
		$userDetailsQuery = mysqli_query($this->conn, "SELECT * FROM users WHERE username='$userLiked'");
		$row = mysqli_fetch_array($userDetailsQuery);
		return $row;
	}

	public function addLike($totalLikes, $totalUserLikes, $userLiked, $postId)
	{
		$userLoggedIn = $this->user_obj->getUsername();
		
		mysqli_query($this->conn, "UPDATE posts SET likes='$totalLikes' WHERE id='$postId'");
		mysqli_query($this->conn, "UPDATE users SET num_likes='$totalUserLikes' WHERE username='$userLiked'");
		mysqli_query($this->conn, "INSERT INTO `likes` (`id`, `username`, `post_id`) 
									VALUES (NULL, '$userLoggedIn', '$postId')");
	}

	public function subtractLike($totalLikes, $totalUserLikes, $userLiked, $postId)
	{		
		$userLoggedIn = $this->user_obj->getUsername();

		mysqli_query($this->conn, "UPDATE posts SET likes='$totalLikes' WHERE id='$postId'");
		mysqli_query($this->conn, "UPDATE users SET num_likes='$totalUserLikes' WHERE username='$userLiked'");
		mysqli_query($this->conn, "DELETE FROM `likes` WHERE username='$userLoggedIn' AND post_id='$postId'");
	}

	public function checkForUpdatedLikes($postId)
	{
		$userLoggedIn = $this->user_obj->getUsername();

		$query = mysqli_query($this->conn, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$postId'");
		$numRows = mysqli_num_rows($query);
		
		return $numRows;
	}
}
?>