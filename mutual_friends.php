<?php
require("includes/header.php");

if(isset($_GET['u'])) 
	$username = $_GET['u'];
else 
	$username = $userLoggedIn;
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
	$mutualFriendsNum = $user_logged_in_obj->getMutualFriendsNum($username);
	$mutualFriendsArrayStr = $user_logged_in_obj->getMutualFriendsList($username);

	$mutualFriendsArrayStr = trim($mutualFriendsArrayStr, ","); //Remove first and last comma

	if($mutualFriendsNum == 1)
		echo $mutualFriendsNum . " Mutual Friend" . '<br><br>';
	else
		echo $mutualFriendsNum . " Mutual Friends" . '<br><br>';


	foreach(explode(",", $mutualFriendsArrayStr) as $friend) 
	{
		$friend_obj = new User($conn, $friend);
		$profilePic = $friend_obj->getProfilePic();
		$fullname = $friend_obj->getFirstAndLastName();
		
		$mutualFriendsHtml .= "<a href='$friend'>
				<img class='profilePicSmall' src='" . $profilePic ."'>"
					. $fullname . 
			"</a>
			</br>";
	}
}
else
{
	$mutualFriendsHtml = "<p>You are probably viewing this page by mistake.</p>
			<a href=''>Home</a>
			</br>";
}

echo $mutualFriendsHtml;
?>
</div>