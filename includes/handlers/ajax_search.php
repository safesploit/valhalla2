<?php
	include("../../config/config.php");
	include("../classes/User.php");

	$query = $_POST['query'];
	$userLoggedIn = $_POST['userLoggedIn'];

	$names = explode(" ", $query);

	//Assumes user is searching for usernames
		/** 
		 * Concerning check if same data type !== // ===
		 * (refer to PHP: strpos() function)
		**/
	if(strpos($query, '_') !== false)
	{
		$usersReturnedQuery = mysqli_query($conn, "SELECT first_name, last_name, username, profile_pic, user_closed, friend_array FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
	}
	//Assumes they are searching for first_name AND last_name
	else if(count($names) == 2)
	{
		$usersReturnedQuery = mysqli_query($conn, "SELECT first_name, last_name, username, profile_pic, user_closed, friend_array FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");
	}
	//Assumes they are searching for first_name OR last_name
	else if(count($names) == 1)
	{
		$usersReturnedQuery = mysqli_query($conn, "SELECT first_name, last_name, username, profile_pic, user_closed, friend_array FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");
	}

	if($query != "")
	{
		while($row = mysqli_fetch_array($usersReturnedQuery)) 
		{
			$user = new User($conn, $userLoggedIn);

			if($row['username'] != $userLoggedIn)
				$mutual_friends = $user->getMutualFriendsNum($row['username']) . " friends in common";
			else 
				$mutual_friends = "";

			//Prevents userLoggedIn from showing in search results
			if($userLoggedIn != $row['username']) 
			{
				echo 	"	<div class='resultDisplay'>
								<a href='" . $row['username'] ."' style='color:#1485BD'>
									<div class='liveSearchProfilePic'>
										<img src='" . $row['profile_pic'] ."'>
									</div>

									<div class='liveSearchText'>
										" . $row['first_name'] . " " . $row['last_name'] . "
										<p>" . $row['username'] ."</p>
										<p id='grey'>" . $mutual_friends ."</p>
									</div>
								</a>
							</div>
						";
			}
		}
	}

?>