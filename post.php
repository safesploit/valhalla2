<?php  
// View individual post //Share post
require("includes/header.php");
// include("includes/header.php");


if(isset($_GET['id'])) 
	$id = $_GET['id'];
else 
	$id = 0;

$userFromFirstname = $user['first_name'];
$userFromLastname = $user['last_name'];
$userFromNumPosts = $user['num_posts'];
$userFromNumLikes = $user['num_likes'];
$userFromProfilePic = $user['profile_pic'];
?>

<!-- Single post -->
<div class="main_column column" id="main_column">
	<div class="posts_area">
		<?php 
			$post = new Post($conn, $userLoggedIn);
			$post->getSinglePost($id);
		?>
	</div>
</div>

<div class="user_details column">
	<a href="<?php echo $userLoggedIn; ?>">
		<img src="<?php echo $userFromProfilePic; ?>"> 
	</a>
	<div class="user_details_left_right">
		<a href="<?php echo $userLoggedIn; ?>">
		<?php 
			echo $userFromFirstname . " " . $userFromLastname;
		 ?>
		</a>
		<br>
		<?php 
			echo "Posts: " . $userFromNumPosts. "<br>"; 
			echo "Likes: " . $userFromNumLikes; "<br>";
		?>
	</div>
</div>


