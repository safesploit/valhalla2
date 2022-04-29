<?php 
	require_once('config/config.php'); 
	include("includes/classes/User.php");
	include("includes/classes/Post.php");
	include("includes/classes/Notification.php");

	if(isset($_SESSION['username']))
	{
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($conn, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else
	{
		header("Location: register.php");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/comment_frame_style.css">
</head>
<body>

	<script>
		function toggle() 
		{
			var element = document.getElementById("comment_section");

			if(element.style.display == "block") 
				element.style.display = "none";
			else 
				element.style.display = "block";
		}
	</script>

	<?php
		//Get id of post
		if(isset($_GET['post_id']))
		{
			$post_id = $_GET['post_id'];
		}

		$user_query = mysqli_query($conn, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
		$row = mysqli_fetch_array($user_query);

		$posted_to = $row['added_by'];

		if(isset($_POST['postComment' . $post_id]))
		{
			$post_body = $_POST['post_body'];
			$user_to = $row['user_to'];

			//Start line break code
			$post_body = strip_tags($post_body); //removes HTML tags
			$post_body = str_replace('\r\n', '\n', $post_body);
			$post_body = nl2br($post_body);
			//END line break code
			$post_body = mysqli_escape_string($conn, $post_body);

			$check_empty = preg_replace('/\s+/', '', $post_body); //removes all spaces

			//Checks if post is empty
			if($check_empty != "")
			{
				$date_time_now = date("Y-m-d H:i:s");
				$sql = "INSERT INTO `comments` (`id`, `post_body`, `posted_by`, `posted_to`, `date_added`, `removed`, `post_id`) VALUES (NULL, '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')";
				$insert_post = mysqli_query($conn, $sql);

					//Insert notification
					if($posted_to != $userLoggedIn) 
					{
						$notification = new Notification($conn, $userLoggedIn);
						$notification->insertNotification($post_id, $posted_to, "comment");
					}

					if($user_to != 'none' && $user_to != $userLoggedIn) 
					{
						$notification = new Notification($conn, $userLoggedIn);
						$notification->insertNotification($post_id, $user_to, "profile_comment");
					}

					$get_commenters = mysqli_query($conn, "SELECT * FROM comments WHERE post_id='$post_id'");
					$notified_users = array();
					while($row = mysqli_fetch_array($get_commenters))
					{
						/**
						 * PREVENTS DUPLICATE NOTIFICATIONS FOR THE SAME POST
						 * 
						 * (DO NOT GIVE NOTIFICATION):
						 * 
						 * If posted_by != posted_to (not original poster)  &&
						 * If posted_by != user_to &&
						 * If posted_by != userLoggedIn && (So we don't give ourselves a notification)
						 * If !in_array() (to prevent multiple notifications)
						 * 
						 **/

						if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users))
						{
							$notification = new Notification($conn, $userLoggedIn);
							$notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

							array_push($notified_users, $row['posted_by']);
						}
					}

				echo "<p>Commented Posted! </p>";
			}
			else
			{
				echo "<b><p>Cannot submit an empty comment!</p></b>";
			}
		}

	?>

	<!-- Post comment $post_id -->
	<form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
		<textarea name="post_body" placeholder="Add a comment"></textarea>
		<input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
	</form>


	<!-- Load comments -->
	<?php
		$get_comments = mysqli_query($conn, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
		$count = mysqli_num_rows($get_comments);

		if($count != 0)
		{
			while($comment = mysqli_fetch_array($get_comments))
			{
				$comment_body = $comment['post_body'];
				$posted_to = $comment['posted_to'];
				$posted_by = $comment['posted_by'];
				$date_added = $comment['date_added'];
				$removed = $comment['removed'];

				include("includes/classes/comment_frame_timeframe.php");

				$user_obj = new User($conn, $posted_by);

				//PHP tag closed and opened again below
				//This is done to prevent having to convert the <div> below into PHP appopriate code.
				?>

				<div class="comment_section">
					<a href="<?php echo $posted_by; ?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $posted_by ?>" style="float:left;" height="30"></a>
					<a href="<?php echo $posted_by; ?>" target="_parent"><b><?php echo $user_obj->getFirstAndLastName(); ?></b></a>
					&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?>
					<hr>
				</div>

				<?php
			}
		}
		else
		{
			echo "<center><br><br>No Comments to Show!</center>";
		}
	?>


</body>
</html>