<?php 
require_once('config/config.php'); 
require("includes/classes/Comment.php");
require("includes/classes/User.php");
require("includes/classes/Post.php");
require("includes/classes/Notification.php");

if(!isset($_SESSION['username']))
	header("Location: register.php");

$userLoggedIn = $_SESSION['username'];

$comment_obj = new Comment($conn, $userLoggedIn);
$post_obj = new Post($conn, $userLoggedIn);
$user_obj = new User($conn, $userLoggedIn);

$user = $user_obj->fetchUserDetails();

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
	// $post_obj->getComments();

	//consider using an error array to manage strings

	//Get id of post
	if(isset($_GET['post_id']))
		$postId = $_GET['post_id'];

	// $user_query = mysqli_query($conn, "SELECT added_by, user_to FROM posts WHERE id='$postId'");
	// $row = mysqli_fetch_array($user_query);

	// $postedTo = $row['added_by'];
	$postedTo = $comment_obj->getPostId($postId);

	if(isset($_POST['postComment' . $postId]))
	{
		$postBody = $_POST['post_body'];
		$userTo = $row['user_to'];

		$postBody = $comment_obj->preparePostBody($postBody);
		$checkEmpty = $comment_obj->checkIfEmptyPost($postBody);

		if($checkEmpty == true)
		{
			// $date_time_now = date("Y-m-d H:i:s");
			// $sql = "INSERT INTO `comments` (`id`, `post_body`, `posted_by`, `posted_to`, `date_added`, `removed`, `post_id`) VALUES (NULL, '$postBody', '$userLoggedIn', '$postedTo', '$date_time_now', 'no', '$postId')";
			// $insert_post = mysqli_query($conn, $sql);
			$insert_post = $comment_obj->submitComment($postBody, $userLoggedIn, $postedTo, $postId);

				//Insert notification
				if($postedTo != $userLoggedIn) 
				{
					$notification = new Notification($conn, $userLoggedIn);
					$notification->insertNotification($postId, $postedTo, "comment");
				}

				if($userTo != 'none' && $userTo != $userLoggedIn) 
				{
					$notification = new Notification($conn, $userLoggedIn);
					$notification->insertNotification($postId, $userTo, "profile_comment");
				}

				// $getCommenters = mysqli_query($conn, "SELECT * FROM comments WHERE post_id='$postId'");
				$getCommenters = $comment_obj->getCommenters($postId);
				$notifiedUsers = array(); //Create an array
				while($row = mysqli_fetch_array($getCommenters))
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
					$postedBy = $row['posted_by'];

					if($postedBy != $postedTo && $postedBy != $userTo && $postedBy != $userLoggedIn && !in_array($postedBy, $notifiedUsers))
					{
						$notification = new Notification($conn, $userLoggedIn);
						$notification->insertNotification($postId, $postedBy, "comment_non_owner");

						array_push($notifiedUsers, $postedBy);
					}
				}

			$commentStatus = "<p>Commented Posted!</p>";
		}
		else
		{
			$commentStatus = "<b><p>Cannot submit an empty comment!</p></b>";
		}

		echo $commentStatus;
	}

	?>

	<!-- Post comment $postId -->
	<!-- <form action="comment_frame.php?post_id=<?php echo $postId; ?>" id="comment_form" name="postComment<?php echo $postId; ?>" method="POST">
		<textarea name="post_body" placeholder="Add a comment"></textarea>
		<input type="submit" name="postComment<?php echo $postId; ?>" value="Post">
	</form>
	-->
	<?php 
	$postComment = "
					<form action='comment_frame.php?post_id=$postId' id='comment_form' name='postComment$postId' method='POST'>
						<textarea name='post_body' placeholder='Add a comment'></textarea>
						<input type='submit' name='postComment$postId' value='Post'>
					</form>
					";

	echo $postComment;

	//<!-- Load comments -->

	$getComments = $comment_obj->loadCommentQuery($postId);
	$count = $comment_obj->numComments($getComments);

	if($count > 0)
		$commentsFrameHtml = $comment_obj->loadComments($getComments);
	else
		$commentsFrameHtml = $comment_obj->noComments();
	
	echo $commentsFrameHtml;
	?>


</body>
</html>