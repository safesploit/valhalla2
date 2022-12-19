<?php
include("includes/header.php");

if(isset($_GET['u'])) 
	$username = $_GET['u'];
else 
	$username = $userLoggedIn;

$user_obj = new User($conn, $username);
?>

<div class="main_column column" id="main_column">
	<?php
	$friendsList = $user_obj->getFriendsList();

	foreach($user_obj->getFriendsList() as $friend) 
	{
		$friend_obj = new User($conn, $friend);
		$friendProfilePic = $friend_obj->getProfilePic();
		$friendFullname = $friend_obj->getFirstAndLastName();

		echo "<a href='$friend'>
				<img class='profilePicSmall' src='" . $friendProfilePic ."'>"
					. $friendFullname . 
			"</a>
			<br>";
	}
	?>
</div>