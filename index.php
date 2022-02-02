<?php include("includes/header.php"); ?>

<?php
/*	if(isset($_POST['post']))
	{
		$uploadOk = 1;
		$imageName = $_FILES['fileToUpload']['name'];
		$errorMessage = ""; //To hold any error messages
		//Should sanatise filename for SQL Injection

		if($imageName != "")
		{
			$targetDir = "assets/images/posts/";
			$imageName = $targetDir . uniqid() . "_" . basename($imageName);
			$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

			if($_FILES['fileToUpload']['size'] > 1048576); //1MB = 1048576 Bytes
			{
				$errorMessage = "Upload FAILED! File is to large";
				$uploadOk = 0;
			}

			if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "png")
			{
				$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
				$uploadOk = 0;
			}

			if($uploadOk)
			{
				if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName))
				{
					echo "image uploaded";
					$uploadOk = 1;
				}
				else
				{
					echo "image did not upload";
					$uploadOk = 0;
				}
			}
		}

		if($uploadOk)
		{
			$post = new Post($conn, $userLoggedIn);
			$post->submitPost($_POST['post_text'], 'none', $imageName);
			//header("Location: index.php");
		}
		else
		{
			echo "	<div style='text-align:center;' class='danger'>
						$errorMessage
					</div>";
		}

		
	}
*/
?>

	<div class="user_details column">
		<a href="<?php echo $userLoggedIn; ?>"><img src="<?php echo $user['profile_pic']; ?>"></a>

		<div class="user_details_left_right">
			<a href="<?php echo $userLoggedIn; ?>"><?php echo $user['first_name'] . " " . $user['last_name']; ?></a><br>
			<?php 
				echo "Posts: " . $user['num_posts'] . "<br>";
				echo "Likes: " . $user['num_likes'] . "<br>";
			?>
		</div>
	</div>

	<?php $profile_user_obj = new User($conn, $username); ?>

	<!--BEGIN Modal // Profile Post Button -->
		<!--<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
		   <div class="modal-dialog">
		      <div class="modal-content">
		         <div class="modal-header">
		            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		            <h4 class="modal-title" id="postModalLabel">Make a Post</h4>
		         </div>
		         <div class="modal-body">
		            <p>This will appear on your profile page for your friends to see!</p>
		            <form class="post" action="" method="POST">
		               <div class="form-group" spellcheck="true">
		                  <input type="file" name="fileToUpload" id="fileToUpload"><br>
		                  <textarea class="form-control" name="post_body"></textarea>
		                  <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
		                  <input type="hidden" name="user_to" value="none">
		               </div>
		            </form>
		         </div>
		         <div class="modal-footer">
		            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		            <button type="button" class="btn btn-primary" name="post_button" id="submit_post">Post</button>
		         </div>
		      </div>
		   </div>
		</div>-->
	<!--END  Modal // Profile Post Button -->

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

		  	
			<!--<textarea type="button" class="" data-toggle="modal" data-target="#post_form" placeholder="Got something to say? Click Here!" ></textarea>
			<input type="submit" name="post" id="post_button" value="Post">-->
			<br><br>
			<hr>
		</form>

		<?php
			//Legacy PHP method for loading posts
			//$post = new Post($conn, $userLoggedIn);
			//$post->loadPostsFriends();
			
			//to test ajax form_handler
			//include("includes/form_handlers/ajax_load_posts.php");
		?>

		<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/Blocks-1s-64px.gif">
	</div>

	<div class="user_details column">
		<h4>Popular Words</h4>

		<div class="trends">
			<?php
				$query = mysqli_query($conn, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

				while($row = mysqli_fetch_array($query))
				{
					$word = $row['title'];
					$word_dot = strlen($word) >= 20 ? "..." : "";

					$trimmed_word = str_split($word, 20);
					$trimmed_word = $trimmed_word[0];

					echo "<div style'padding: 1px'>";
					echo $trimmed_word . $word_dot;
					echo "<br></div><br>";
				}
			?>
		</div>
	</div>


	<!-- JS Responsible for infinite scrolling 
	<script>
		

		$(document).ready(function()
		{
			var userLoggedIn = '<?php echo $userLoggedIn ?>';

			$('#loading').show();	

			//Original ajax request for loading first posts
			$.ajax
			({
				url: "includes/handlers/ajax_load_posts.php",
				type: "POST",
				data: "page=1&userLoggedIn=" + userLoggedIn,
				cache:false,

				success: function(data)
				{	
					$('#loading').hide(); //don't show the loading sign once posts have been returned
					$('.posts_area').html(data);
				}
			});

			$(window).scroll(function() 
			{
				var height = $('.posts_area').height(); //Div containing posts
				var scroll_top = $(this).scrollTop();
				var page = $('.posts_area').find('.nextPage').val();
				var noMorePosts = $('.posts_area').find('.noMorePosts').val();

				//Note that document.body.scrollTop is deprecated in Chrome
				//if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') 
				if ((document.body.scrollHeight == window.scrollY + window.innerHeight) && noMorePosts == 'false')
				{
					$('#loading').show();
					//alert("Hello, scroll!");

					var ajaxReq = $.ajax(
					{
						url: "includes/handlers/ajax_load_posts.php",
						type: "POST",
						data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
						cache:false,

						success: function(response) {
							$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
							$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

							$('#loading').hide();
							$('.posts_area').append(response);
						}
					});

				} //End if 

				return false;

			}); //End (window).scroll(function())

		});
	</script>-->

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

</script>

</div> <!-- Closing </div> which was opened in header.php -->	
</body>
</html>
