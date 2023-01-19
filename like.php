<?php
require_once('config/config.php'); 
require("includes/classes/User.php");
require("includes/classes/Post.php");
require("includes/classes/Notification.php");

if(isset($_SESSION['username']))
{
	$userLoggedIn = $_SESSION['username'];
	$post_obj = new Post($conn, $userLoggedIn);
	
	$user = $post_obj->fetchUserDetails();
}
else
	header("Location: register.php");
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
			$postId = $_GET['post_id'];

		$row = $post_obj->getLikesArray($postId);
		$totalLikes = $row['likes'];
		$userLiked = $row['added_by'];

		$row = $post_obj->likeUserDetails($userLiked);
		$totalUserLikes = $row['num_likes'];

		//Like button
		if(isset($_POST['like_button']))
		{
			$totalLikes++;
			$totalUserLikes++;

			$post_obj->addLike($totalLikes, $totalUserLikes, $userLiked, $postId);

			//Insert Notification
			if($userLiked != $userLoggedIn)
			{
				$notification = new Notification($conn, $userLoggedIn);
				$notification->insertNotification($postId, $userLiked, "like");
			}
		}

		//Unlike button
		if(isset($_POST['unlike_button']))
		{
			$totalLikes--;
			$totalUserLikes--;

			$post_obj->subtractLike($totalLikes, $totalUserLikes, $userLiked, $postId);
		}

		//Check for previous likes
		$numRows = $post_obj->checkForUpdatedLikes($postId);

		if($numRows > 0)
		{
			//Post has already been liked
			//Unlike button
			$buttonHtml = '	<form action="like.php?post_id=' . $postId . '" method="POST">
						<input type="submit" class="comment_like" name="unlike_button" value="Unlike">
						<div class="like_value">
							' . $totalLikes . ' Likes
						</div>
					</form>
				';
		}
		else
		{
			//Post has not been liked yet
			//Like button
			$buttonHtml = '	<form action="like.php?post_id=' . $postId . '" method="POST">
						<input type="submit" class="comment_like" name="like_button" value="Like">
						<div class="like_value">
							' . $totalLikes . ' Likes
						</div>
					</form>
				';
		}
		echo $buttonHtml;

	?>

</body>
</html>