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
	/***
	 * Should fetch mutual friends like in friends.php
	 * Edit User.php so it creates an array/string of users in common
	 * 
	 * https://valhalla.sws-internal/mutual_friends.php?u=homer_simpson
	 ***/
	if($userLoggedIn != $username)
	{
		$user_logged_in_obj = new User($conn, $userLoggedIn);
		$mutual_friends_num = $user_logged_in_obj->getMutualFriendsNum($username);
		$mutual_friends_array_string = $user_logged_in_obj->getMutualFriendsList($username);

		$mutual_friends_array_string = trim($mutual_friends_array_string, ","); //Remove first and last comma

		if($userLoggedIn != $username) echo $mutual_friends_num . " Mutual Friends" . '<br><br>';

		foreach(explode(",", $mutual_friends_array_string) as $friend) 
		{
			$friend_obj = new User($conn, $friend);
			echo "<a href='$friend'>
					<img class='profilePicSmall' src='" . $friend_obj->getProfilePic() ."'>"
					 . $friend_obj->getFirstAndLastName() . 
				"</a>
				<br>";
		}
	}
	else
	{
		echo "<p>You are probably viewing this page by mistake.</p>
				<a href=''>Home</a>
				<br>";
	}
	?>

</div>