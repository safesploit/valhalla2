<?php
// require('../../config/config.php'); 
include("../../config/config.php");


if(isset($_GET['post_id']))
    $postId = $_GET['post_id'];

if(isset($_POST['result']))
{
    if($_POST['result'] == 'true')
    {
        $deletePost = mysqli_query($conn, "UPDATE posts SET deleted='yes' WHERE id='$postId'");

        // $posts_obj = new Post($conn, $_SESSION['userLoggedIn']);
        // $deletePost = $posts_obj->deletePost($postId);
        
        if(!$deletePost)
            echo 'Post could not be deleted!';
    }
}
?>