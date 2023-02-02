<?php
require("includes/header.php");
// include("includes/header.php");

$message_obj = new Message($conn, $userLoggedIn);

if(isset($_GET['u']))
	$userTo = $_GET['u'];
else
{
	$userTo = $message_obj->getMostRecentUser();

	if($userTo == false)
		$userTo = 'new';
}

if($userTo != "new")
	$user_to_obj = new User($conn, $userTo);

if(isset($_POST['post_message']))
{
	if(isset($_POST['message_body']))
	{
		$messageBody = $_POST['message_body'];

		// $messageBody = $message_obj->sanatiseMessageBody($messageBody);
		// $date = date("Y-m-d H:i:s");
		$date = $message_obj->getDate();
		$message_obj->sendMessage($userTo, $messageBody, $date);
	}
}

$firstname = $user['first_name'];
$lastname = $user['last_name'];
$profilePic = $user['profile_pic'];
?>


<div class="user_details column">
	<a href="<?php echo $userLoggedIn; ?>"><img src="<?php echo $profilePic; ?>"></a>

	<div class="user_details_left_right">
		<a href="<?php echo $userLoggedIn; ?>"><?php echo $firstname . " " . $lastname; ?></a><br>
		<?php 
			echo "Posts: " . $user['num_posts'] . "<br>";
			echo "Likes: " . $user['num_likes'] . "<br>";
		?>
	</div>
</div>


<div class="main_column column" id="main_column">
	<?php
		if($userTo != "new")
		{
			echo "<h4>You and <a href='$userTo'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr><br>";
			echo "<div class='loaded_messages' id='scroll_messages'>";
				echo $message_obj->getMessages($userTo);
			echo "</div>";
		}
		else
		{
			echo "<h4>New Message</h4>";
		}
	?>

	<div class="message_post">
		<form action="" method="POST">
			<?php
				if($userTo == "new") 
				{
					echo "Select the friend you would like to message <br><br>";
			?> 
					To: <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'>

					<?php
					echo "<div class='results'></div>";
			}
				else 
				{
					echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
					echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
				}

			?>
		</form>
	</div>


	<script>
		var div = document.getElementById("scroll_messages");
		div.scrollTop = div.scrollHeight;
	</script>

</div>

<div class="user_details column" id="conversations">
	<h4>Conversations</h4>

	<div class="loaded_conversations">
		<?php echo $message_obj->getConvos(); ?>
	</div>
	<br>
	<a href="messages.php?u=new">New Message</a>
</div>