<?php 
require("includes/header.php");
// include("includes/header.php");

//$user[] array is defined in header.php

$profile_user_obj = new User($conn, $username); 

$firstname = $user['first_name'];
$lastname = $user['last_name'];
$profilePic = $user['profile_pic'];
$numLikes = $user['num_posts'];
$numPosts = $user['num_posts'];

$fullname = $firstname . " " . $lastname;

?>
<div class="user_details column">
	<a href="<?php echo $userLoggedIn; ?>"><img src="<?php echo $profilePic; ?>"></a>
	<div class="user_details_left_right">
		<a href="<?php echo $userLoggedIn; ?>"><?php echo $fullname; ?></a><br>
		<?php 
			echo "Posts: $numPosts <br>";
			echo "Likes: $numLikes <br>";
		?>
	</div>
</div>
<div class="main_column column">
	<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
		<h4>Make a Post</h4>
		<textarea class="form-control" name="post_body" id="post_body" placeholder="This will appear on your profile page for your friends to see!"></textarea>
		<br>

		<span class="btn btn-primary upload_file_btn" style="float: left;">
			<span>Upload Image</span><input type="file" name="fileToUpload" id="fileToUpload">
		</span>
		<span style="float: right;">
			<button type="button" class="btn btn-primary" name="post_button" id="submit_post">Post</button>
		</span>
		<br><br>
		<hr>
	</form>
	<div class="posts_area"></div>
	<img id="loading" src="assets/images/icons/Blocks-1s-64px.gif">
</div>
<div class="user_details column">
	<h4>Trending Words</h4>
	<div class="trends">
		<?php
		$profile_user_obj->trendingWords();
		?>
	</div>
</div>

<!--Infinite Scrolling v2-->
<script>
$(function()
{

	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	var inProgress = false;

	loadPosts(); //Load first posts

    $(window).scroll(function() 
    {
    	var bottomElement = $(".status_post").last();
    	var noMorePosts = $('.posts_area').find('.noMorePosts').val();

        // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
        if (isElementInView(bottomElement[0]) && noMorePosts == 'false') 
        {
            loadPosts();
        }
    });

    function loadPosts() 
    {
        if(inProgress) 
        { //If it is already in the process of loading some posts, just return
			return;
		}
		
		inProgress = true;
		$('#loading').show();

		var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

		$.ajax(
		{
			url: "includes/handlers/ajax_load_posts.php",
			type: "POST",
			data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
			cache:false,

			success: function(response) 
			{
				$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
				$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
				$('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

				$('#loading').hide();
				$(".posts_area").append(response);

				inProgress = false;
			}
		});
    }

    //Check if the element is in view
    function isElementInView (el) 
    {
        var rect = el.getBoundingClientRect();

        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
            rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
        );
    }
});
// infiniteScrolling();
</script>

</div> <!-- Closing </div> which was opened in header.php -->	
</body>
</html>
