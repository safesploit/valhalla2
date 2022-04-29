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
	<link rel="stylesheet" type="text/css" href="assets/css/like_style.css">
</head>
<body>
	<style type="text/css">
		body
		{

		}
	</style>


	<?php
		//Get id of post
		if(isset($_GET['post_id']))
		{
			$post_id = $_GET['post_id'];
		}

		$get_likes = mysqli_query($conn, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
		$row = mysqli_fetch_array($get_likes);
		$total_likes = $row['likes'];
		$user_liked = $row['added_by'];

		$user_details_query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user_liked'");
		$row = mysqli_fetch_array($user_details_query);
		$total_user_likes = $row['num_likes'];

		//Like button
		if(isset($_POST['like_button']))
		{
			$total_likes++;
			$query = mysqli_query($conn, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
			$total_user_likes++;
			$user_likes = mysqli_query($conn, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
			$insert_user = mysqli_query($conn, "INSERT INTO `likes` (`id`, `username`, `post_id`) VALUES (NULL, '$userLoggedIn', '$post_id')");

			//Insert Notification
			if($user_liked != $userLoggedIn)
			{
				$notification = new Notification($conn, $userLoggedIn);
				$notification->insertNotification($post_id, $user_liked, "like");
			}
		}

		//Unlike button
		if(isset($_POST['unlike_button']))
		{
			$total_likes--;
			$query = mysqli_query($conn, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
			$total_user_likes--;
			$user_likes = mysqli_query($conn, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
			$insert_user = mysqli_query($conn, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
		}

		//Check for previous likes
		$check_query = mysqli_query($conn, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
		$num_rows = mysqli_num_rows($check_query);

		if($num_rows > 0)
		{
			//Post has already been liked
			//Unlike button
			echo'	<form action="like.php?post_id=' . $post_id . '" method="POST">
						<input type="submit" class="comment_like" name="unlike_button" value="Unlike">
						<div class="like_value">
							' . $total_likes . ' Likes
						</div>
					</form>
				';
		}
		else
		{
			//Post has not been liked yet
			//Like button
			echo'	<form action="like.php?post_id=' . $post_id . '" method="POST">
						<input type="submit" class="comment_like" name="like_button" value="Like">
						<div class="like_value">
							' . $total_likes . ' Likes
						</div>
					</form>
				';
		}

	?>

</body>
</html>