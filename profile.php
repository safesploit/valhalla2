<?php 
require("includes/header.php"); 
// include("includes/header.php");


/* Object Variables */
$message_obj = new Message($conn, $userLoggedIn);
$logged_in_user_obj = new User($conn, $userLoggedIn); 

/* If isset statements */
if(isset($_GET['profile_username']))
{
	$username = $_GET['profile_username'];
	$profile_user_obj = new User($conn, $username); 
	
	$numFriends = $logged_in_user_obj->profileFriendsNum($username);
	$userArray = $logged_in_user_obj->profileUserArray($username);

	$numPosts = $userArray['num_posts'];
	$numLikes = $userArray['num_likes'];
	$profilePic = $userArray['profile_pic'];

	if($userLoggedIn == $username)
		$postDescription = '<p>This will appear on your profile page and newsfeed for your friends to see!</p>';
	else
	{
		$fullname = $profile_user_obj->getFirstAndLastName(); //Causes error
		$postDescription = "<p>This will appear on <i>$fullname&#39;s</i> profile page and also their newsfeed for your friends to see!</p>";
	}
}

/* Profile page // friend buttons */
if(isset($_POST['remove_friend']))
{
	$user = new User($conn, $userLoggedIn);
	$user->removeFriend($username);
}

if(isset($_POST['add_friend']))
{
	$user = new User($conn, $userLoggedIn);
	$user->sendRequest($username);
	header("Location: $username");
}

if(isset($_POST['respond_request']))
{
	header("Location: friend_requests.php");
}

//Option to cancel friend request
if(isset($_POST['cancel_request'])) 
{
	$user = new User($conn, $userLoggedIn);
	$user->cancelFriendRequest($username);
}

//Form for Sending Messages from Profile page
if(isset($_POST['post_message']))
{
	if(isset($_POST['message_body']))
	{
		$body = mysqli_real_escape_string($conn, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($username, $body, $date);
	}

	$link = '#profileTabs a[href="#messages_div"]';
	echo "	
				<script> 
					$(function() 
					{
						$('" . $link ."').tab('show');
					});
				</script>
			";
}

?>

<link rel="stylesheet" type="text/css" href="assets/css/profile_style.css">

<div class="profile_left">
	<img src="<?php echo $profilePic ?>">

	<div class="profile_info">
		<p><?php echo "Posts: " . $numPosts; ?></p>
		<p><?php echo "Likes: " . $numLikes; ?></p>
		<p><?php echo "Friends: " . $numFriends; ?></p>
		<p><a href="friends.php<?php if($userLoggedIn != $username) echo "?u=" . $username; ?>">View friends</a></p><br>

		<?php
		if($userLoggedIn != $username)
		{
			$mutualFriendsNum = $logged_in_user_obj->getMutualFriendsNum($username);
			$usernameUrn = "?u=" . $username;

		echo "	<p>$mutualFriendsNum Mutual friends</p>
				<p><a href='mutual_friends.php$usernameUrn'>View mutual friends</a></p><br>
			";
		}
		?>
		
	</div>

	<form action="<?php echo $username; ?>" method="POST">
		<?php 
			// $profile_user_obj = new User($conn, $username); 

			if($profile_user_obj->isClosed())
			{
				header("Location: user_closed.php");
			}


			//Check if user is logged in
			if($userLoggedIn != $username) 
			{
				if($logged_in_user_obj->isFriend($username)) 
				{
					echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
				}
				else if ($logged_in_user_obj->didReceiveRequest($username)) 
				{
					echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
				}
				else if ($logged_in_user_obj->didSendRequest($username)) 
				{
					echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
					echo '<input type="submit" name="cancel_request" class="warning" value="Cancel Request"><br>';
				}
				else 
				{
					echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
				}
			}

		?>

		
		
	</form>

	<input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something">

	<?php
		if($userLoggedIn != $username) 
		{
			echo '<div class="profile_info_bottom">';
				//echo $logged_in_user_obj->getMutualFriendsNum($username) . " Mutual friends";
			echo '</div>';
		}
	?>
</div>
<div class="profile_main_column column">

	<ul class="nav nav-tabs" role="tablist" id="profileTabs">
		<li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a></li>
		<li role="presentation"><a href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab">Messages</a></li>
	</ul>

	<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
				<div class="posts_area"></div>
				<img id="loading" src="assets/images/icons/Spin-1s-32px.gif">
			</div>

		<div role="tabpanel" class="tab-pane fade" id="messages_div">
			<!--BEGIN Copied from messages.php -->	
				<?php

					echo "<h4>You and <a href='" . $username ."'>" . $fullname . "</a></h4><hr><br>";

					echo "<div class='loaded_messages' id='scroll_messages'>";
					echo $message_obj->getMessages($username);
					echo "</div>";
				?>

				<div class="message_post">
					<form action="" method="POST">
						<textarea name='message_body' id='message_textarea' placeholder='Write your message...'></textarea>
						<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
					</form>
				</div>

				<script>
					var div = document.getElementById("scroll_messages");
					div.scrollTop = div.scrollHeight;
				</script>
			<!--END Copied from messages.php-->
		</div>
	</div>
</div>
<!-- Modal // Profile Post Button -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="postModalLabel">Make a post on <?php echo $fullname; ?>&#39;s feed</h4>
      </div>

      <div class="modal-body">
		<p><?php echo $postDescription ?></p>
      	<form class="profile_post" action="" method="POST" enctype="multipart/form-data">
      		<div class="form-group" spellcheck="true">
				<br><br>
      			<textarea class="form-control" name="post_body"></textarea>
      			<input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
      			<input type="hidden" name="user_to" value="<?php echo $username; ?>">
      		</div>
      		<div class="modal-footer">
      			<span class="btn btn-primary upload_file_btn" style="float: left;">
					<span>Upload Image</span><input type="file" name="fileToUpload" id="fileToUpload">
				</span>
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
	      	</div>
      	</form>
      </div>
    </div>
  </div>
</div>
<!--JS Infinite scrolling v2-->
<script>

$(function(){

     var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $username; ?>';
   var inProgress = false;

     loadPosts(); //Load first posts

     $(window).scroll(function() {
         var bottomElement = $(".status_post").last();
         var noMorePosts = $('.posts_area').find('.noMorePosts').val();            
     
         // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
         if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
         loadPosts();
        }
     });

     function loadPosts() {
         if(inProgress) { //If it is already in the process of loading some posts, just return
         return;
         }
       
         inProgress = true;
         $('#loading').show();

         var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

         $.ajax({
             url: "includes/handlers/ajax_load_profile_posts.php",
             type: "POST",
             data: "page="+page+"&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
             cache:false,

             success: function(response) {
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
     function isElementInView (el) {
         if(el == null) {
             return;
         }

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
