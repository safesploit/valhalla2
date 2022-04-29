<?php
	include("../../config/config.php");
	include("../classes/User.php");

	$query =  $_POST['query'];
	$userLoggedIn = $_POST['userLoggedIn'];

	$names = explode(" ", $query);

	//Assume user is searching for username
	if(strpos($query, "_") !== false)
	{
		$usersReturned = mysqli_query($conn, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
	}
	//Assume they are searching first_name AND last_name 
	else if(count($names) == 2)
	{
		$usersReturned = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed='no' LIMIT 8");
	}
	//Assume they are search for first_name OR last_name
	else
	{
		$usersReturned = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
	}


	if($query != "")
	{
		while($row = mysqli_fetch_array($usersReturned))
		{
			$user = new User($conn, $userLoggedIn); //should the (conn, ...) be ($this->conn, ...)

			if($row['username'] != $userLoggedIn)
			{
				//Include if-statement so it reads "1 friend in common" or "X friends in common"
				//Must check if($mutual_friends == 1) { } else if($mutual_friends > 1) {}
				$mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
			}
			else
			{
				$mutual_friends = "";
			}


			if($user->isFriend($row['username']))
			{
				echo 	"	<div class='resultDisplay'>
								<a href='messages.php?u=" . $row['username'] . "' style='color: #000'>
									<div class='liveSearchProfilePic'>
										<img src='". $row['profile_pic'] . "'>
									</div>

									<div class='liveSearchText'>
										".$row['first_name'] . " " . $row['last_name']. "
										<p style='margin: 0;' id='liveSearchText_username'>". $row['username'] . "</p>
										<p id='grey'>".$mutual_friends . "</p>
									</div>
								</a>
							</div>
						";
			}
		}
	}

?>