<?php
	include("includes/header.php");

	if(isset($_GET['u'])) 
	{
		$username = $_GET['u'];
	}
	else 
	{
		$username = $userLoggedIn;
	}
?>

<div class="main_column column" id="main_column">

	<?php
		$user_obj = new User($conn, $username);
		foreach($user_obj->getFriendsList() as $friend) 
		{
			$friend_obj = new User($conn, $friend);
			echo "<a href='$friend'>
					<img class='profilePicSmall' src='" . $friend_obj->getProfilePic() ."'>"
					 . $friend_obj->getFirstAndLastName() . 
				"</a>
				<br>";
		}
	?>

</div>