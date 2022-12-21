<?php
require("includes/header.php");
// include("includes/header.php");


if(isset($_GET['q'])) 
	$query = $_GET['q'];
else 
	$query = "";

if(isset($_GET['type'])) 
	$type = $_GET['type'];
else 
	$type = "name";

$search_user_obj = new User($conn, $username);

?>

<div class="main_column column" id="main_column">

	<?php 
		if($query == "")
			echo "You must enter something in the search box.";
		else 
		{
			//Assumes user is searching for usernames
			if($type == "username") 
			{
				$usersReturnedQuery = $search_user_obj->usernameTypeSearch($query);
			}
			else if ($type == "name" || $type == "")
			{
				$names = explode(" ", $query);
				$usersReturnedQuery = $search_user_obj->nameTypeSearch($names);
			}

			//Check if results were found 
			if(mysqli_num_rows($usersReturnedQuery) == 0)
				echo "We can't find anyone with a " . $type . " like: " .$query;
			else 
				echo mysqli_num_rows($usersReturnedQuery) . " results found: <br> <br>";


			echo "<p id='grey'>Try searching for by:</p>";
			echo "<a href='search.php?q=" . $query ."&type=name'>Names</a>, <a href='search.php?q=" . $query ."&type=username'>Usernames</a><br><br><hr id='search_hr'>";

			while($row = mysqli_fetch_array($usersReturnedQuery)) 
			{
				$search_user_obj = new User($conn, $user['username']);

				$button = "";
				$mutualFriends = "";

				$firstname = $row['first_name'];
				$lastname = $row['last_name'];
				$profilePic = $row['profile_pic'];
				$searchedUsername = $row['username'];

				if($user['username'] != $searchedUsername) 
				{
					$button = $search_user_obj->getFriendshipStatusButton($searchedUsername);
					$mutualFriendsNum = $search_user_obj->getMutualFriendsNum($searchedUsername);
					$mutualFriendsStr = $mutualFriendsNum . " friends in common";

					//Button forms
					if(isset($_POST[$searchedUsername])) 
					{
						$search_user_obj->friendButtonForms($searchedUsername);
					}
				}

				if($userLoggedIn == $searchedUsername)
					$userLoggedInTag = '<span id="userLoggedInTag">You</span>';
				else
					$userLoggedInTag = "";

				// if($userLoggedIn != $searchedUsername) 
				// {
					echo "	<div class='search_result'>
								<div class='searchPageFriendButtons'>
									<form action='' method='POST'>
										" . $button . "
										<br>
									</form>
								</div>

								<div class='result_profile_pic'>
									<a href='" . $searchedUsername ."'><img src='". $profilePic ."' style='height: 100px;'></a>
								</div>

									<a href='" . $searchedUsername ."'> " . $firstname . " " . $lastname . "
									<p id='grey'> " . $searchedUsername ."</p>
									</a>
									$userLoggedInTag
									<br>
									" . $mutualFriendsStr ."<br>

							</div>
							<hr id='search_hr'>
						";
				// }
			} //End while()
		}


	?>



</div>