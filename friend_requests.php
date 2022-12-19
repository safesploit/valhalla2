<?php 
include("includes/header.php"); 

$user_obj = new User($conn, $userLoggedIn);

$friendRequests = $user_obj->friendRequests();
$query = $user_obj->friendRequestQuery();
?>

<div class="main_column column" id="main_column">
	<h4>Friend Requests</h4>
	<?php 
	if($friendRequests)
	{
		while($row = mysqli_fetch_array($query))
		{
			$userFrom = $row['user_from'];
			$user_from_obj = new User($conn, $userFrom);
			$userFromFullname = $user_from_obj->getFirstAndLastName();
			// $userFromFriendArray = $user_from_obj->getFriendArray();
			
			$friendRequestHref = "<b><a href=$userFrom>$userFromFullname</a></b> sent you a friend request!";
			$friendRequestForm = "	<form action='friend_requests.php' method='POST'>
										<input type='submit' name='accept_request$userFrom' id='accept_button' value='Accept'>
										<input type='submit' name='ignore_request$userFrom' id='ignore_button' value='Ignore'>
									</form>";

			if(isset($_POST['accept_request' . $userFrom]))
			{
				$user_obj->acceptFriendRequest($userFrom);
				echo "You are now friends!";
				header("Location: friend_requests.php");
			}

			if(isset($_POST['ignore_request' . $userFrom]))
			{
				$user_obj->ignoreFriendRequest($userFrom);
				echo "Friend request ignored!";
				header("Location: friend_requests.php");
			}

			echo $friendRequestHref;
			echo $friendRequestForm;
		}
	}
	else
	{
		echo "You have no friend requests pending!";
	}
	?>
</div>