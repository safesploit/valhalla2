<?php
	include("../../config/config.php");
	include("../classes/User.php");
	include("../classes/Post.php");

	$limit = 10; //Numbers of posts to be loaded per call

	$posts = new Post($conn, $_REQUEST['userLoggedIn']);
	$posts->loadProfilePosts($_REQUEST, $limit);
?>