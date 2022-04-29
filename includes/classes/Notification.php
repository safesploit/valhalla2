<?php
class Notification 
{
	private $user_obj;
	private $conn;

	public function __construct($conn, $user)
	{
		$this->conn = $conn;
		$this->user_obj = new User($conn, $user);
	}

	public function getUnreadNumber()
	{
		$userLoggedIn = $this->user_obj->getUsername();
		$query = mysqli_query($this->conn, "SELECT * FROM notifications WHERE viewed='no' AND user_to='$userLoggedIn'");
		return mysqli_num_rows($query);
	}

	public function getNotifications($data, $limit)
	{
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		if($page == 1)
		{
			$start = 0;
		}
		else
		{
			$start = ($page - 1) * $limit;
		}

		$set_viewed_query = mysqli_query($this->conn, "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");

		$query = mysqli_query($this->conn, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' ORDER BY id DESC");

		if(mysqli_num_rows($query) == 0)
		{
			echo "You have no notifications!";
			return;
		}

		$num_iterations = 0; //Number of messages check
		$count = 1; //Number of messages posted

		while($row = mysqli_fetch_array($query))
		{
			if($num_iterations++ < $start)
			{
				continue;
			}

			if($count++ > $limit)
			{
				break;
			}


			$user_from = $row['user_from'];

			$user_data_query = mysqli_query($this->conn, "SELECT * FROM users WHERE username='$user_from'");
			$user_data = mysqli_fetch_array($user_data_query);
			//Timeframe
			include("Notification_timeframe.php");


			$opened = row['opened'];
			$style = (isset($row['opened']) && $row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";


			$return_string .= 	"	<a href='" . $row['link'] . "'> 
										<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
											<div class='notificationsProfilePic'>
												<img src='" . $user_data['profile_pic'] . "'>
											</div>
											" . $row['message'] . " <p class='timestamp_smaller' id='grey'>" . $time_message . "</p>
										</div>
									</a>
								";
								
		}

		//If posts were loaded
		if($count > $limit)
			{
				$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
			}
		else 
			{
				$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more notifications to load!</p>";
			}

		return $return_string;
	}

	public function insertNotification($post_id, $user_to, $type)
	{
		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedInName = $this->user_obj->getFirstAndLastName();

		$date_time = date("Y-m-d H:i:s");

		switch($type) 
		{
			case 'comment':
				$message = $userLoggedInName . " commented on your post";
				break;
			case 'like':
				$message = $userLoggedInName . " liked your post";
				break;
			case 'profile_post':
				$message = $userLoggedInName . " posted on your profile";
				break;
			case 'comment_non_owner':
				$message = $userLoggedInName . " commented on a post you commented on";
				break;
			case 'profile_comment':
				$message = $userLoggedInName . " commented on your profile post";
				break;
		}

		$link = "post.php?id=" . $post_id;

		$sql = "INSERT INTO `notifications` (`id`, `user_to`, `user_from`, `message`, `link`, `datetime`, `opened`, `viewed`) VALUES (NULL, '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')";
		$insert_query = mysqli_query($this->conn, $sql);

	}


}
?>