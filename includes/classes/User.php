<?php
class User
{
	private $user;
	private $conn;

	public function __construct($conn, $user)
	{
		$this->conn = $conn;
		$user_details_query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
		$this->user = mysqli_fetch_array($user_details_query);
	}

	public function getUsername()
	{
		return $this->user['username'];
	}

	public function getNumPosts()
	{
		$username = $this->user['username'];
		$query = mysqli_query($this->conn, "SELECT num_posts FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['num_posts'];
	}

	public function getFirstAndLastName()
	{
		$username = $this->user['username'];
		$query = mysqli_query($this->conn, "SELECT first_name, last_name FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['first_name'] . " " . $row['last_name'];
	}

	public function getProfilePic()
	{
		$username = $this->user['username'];
		$query = mysqli_query($this->conn, "SELECT profile_pic FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['profile_pic'];
	}

	public function getFriendArray()
	{
		$username = $this->user['username'];
		$query = mysqli_query($this->conn, "SELECT friend_array FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['friend_array'];
	}

	public function isClosed()
	{
		$username = $this->user['username'];
		$query = mysqli_query($this->conn, "SELECT user_closed FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);

		if($row['user_closed'] == 'yes')
			return true;
		else
			return false;
	}

	public function closeAccount()
	{
		$username = $this->user['username'];
		$closeQuery = mysqli_query($this->conn, "UPDATE users SET user_closed='yes' WHERE username='$username'");

		if($closeQuery)
			return true;
		else
			return false;
	}

	public function isFriend($usernameToCheck)
	{
		$usernameComma = "," . $usernameToCheck . ",";

		//Check if username is in 'friend_array'
		if(strstr($this->user['friend_array'], $usernameComma) || $usernameToCheck == $this->user['username'])
			return true;
		else
			return false;
	}

	public function didReceiveRequest($user_from)
	{
		$user_to = $this->user['username'];
		$check_request_query = mysqli_query($this->conn, "SELECT user_to,user_from FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function didSendRequest($user_to) 
	{
		$user_from = $this->user['username'];
		$check_request_query = mysqli_query($this->conn, "SELECT user_to,user_from FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}


	public function removeFriend($user_to_remove)
	{
		$logged_in_user = $this->user['username'];

		$query = mysqli_query($this->conn, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
		$row = mysqli_fetch_array($query);
		$friend_array_username = $row['friend_array'];

		//Removes from our friend_array
		$new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']);
		$remove_friend = mysqli_query($this->conn, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'");

		//Removes from the other person's friend_array
		$new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
		$remove_friend = mysqli_query($this->conn, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove'");
	}

	public function cancelFriendRequest($user_to)
	{
		$logged_in_user = $this->user['username'];
		$delete_query = mysqli_query($this->conn, "DELETE FROM friend_requests WHERE user_to='$user_to' AND user_from='$logged_in_user'");
	}

	public function sendRequest($user_to) 
	{
		$user_from = $this->user['username'];
		$sql = "INSERT INTO `friend_requests` (`id`, `user_to`, `user_from`) VALUES (NULL, '$user_to', '$user_from')";
		$query = mysqli_query($this->conn, $sql);
	}

	public function getUsernameUrn($username)
	{
		$usernameUrn = "?u=" . $username;

		return $usernameUrn;
	}

	public function getFriendsHtml($numFriends, $usernameUrn)
	{
		if($numFriends == 1)
			$friendsHtmlStr = "<p><a href='friends.php$usernameUrn'>View friend</a></p>
			</br>";
		else
			$friendsHtmlStr = "<p><a href='friends.php$usernameUrn'>View friends</a></p>
			</br>";
		
		return $friendsHtmlStr;
	}

	public function getMutualFriendsNum($user_to_check)
	{
		$mutualFriends = 0;
		$user_array = $this->user['friend_array'];
		$user_array_explode = explode(",", $user_array);

		$query = mysqli_query($this->conn, "SELECT friend_array FROM users WHERE username='$user_to_check'");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friend_array'];
		$user_to_check_array_explode = explode(",", $user_to_check_array);

		/* 2-dimensional array */
		foreach($user_array_explode as $i)
		{
			foreach($user_to_check_array_explode as $j)
			{
				if(($i == $j) && ($i != ""))
				{
					$mutualFriends++;
				}
			}
		}
		return $mutualFriends;
	}


	public function getMutualFriendsList($user_to_check)
	{
		$logged_in_user = $this->user['username'];
		$mutualFriends = 0;
		$mutualFriendsString = "";
		//$mutualFriendsArray = array(); //Array of mutual friends to return
		$user_array = $this->user['friend_array'];
		$user_array_explode = explode(",", $user_array);

		$query = mysqli_query($this->conn, "SELECT friend_array FROM users WHERE username='$user_to_check'");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friend_array'];
		$user_to_check_array_explode = explode(",", $user_to_check_array);
		//$user_to_check_array_explode = sort($user_to_check_array_explode);

		/* 2-dimensional array */
		foreach($user_array_explode as $i)
		{
			foreach($user_to_check_array_explode as $j)
			{
				if(($i == $j) && ($i != ""))
				{
					if($j != $logged_in_user)
					{
						//Reprogram from STR to Array, then do same in mutual_friends.php
						$mutualFriendsString .= "," . $j;
					}
				}
			}
		}
		return $mutualFriendsString;
	}



	public function getNumberOfFriendRequests()
	{
		$username = $this->user['username'];
		$query = mysqli_query($this->conn, "SELECT user_to,user_from FROM friend_requests WHERE user_to='$username'");
		return mysqli_num_rows($query);
	}

	public function getFriendsList()
	{
		$friend_array_string = $this->user['friend_array']; //Get friend array string from table
		$friend_array_string = trim($friend_array_string, ","); //Remove first and last comma

		return explode(",", $friend_array_string); //Split to array at each comma
	}

	public function notifyUserFriendRequestAccepted()
	{
		//To be written
		//May require that the Friend_Request table be edited
		//Or another table create called Friends_Since:
		// | id | user_to | user_from | date_added (DateTime) |
	}

	/*************************************************
	 * functions for friend_requests.php
	 *************************************************/

	public function friendRequests()
	{
		$userLoggedIn = $this->user['username'];
		$query = mysqli_query($this->conn, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
		$row = mysqli_fetch_array($query);
		if(mysqli_num_rows($query) > 0)
			return true;
		else
			return false;
	}
	
	public function friendRequestQuery()
	{
		$userLoggedIn = $this->user['username'];
		$query = mysqli_query($this->conn, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
		return $query;
	}

	public function acceptFriendRequest($userFrom)
	{
		$userLoggedIn = $this->user['username'];
		$add_friend_query = mysqli_query($this->conn, "UPDATE users SET friend_array=CONCAT(friend_array, '$userFrom,') WHERE username='$userLoggedIn'");
		$add_friend_query = mysqli_query($this->conn, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$userFrom'");
		$delete_query = mysqli_query($this->conn, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$userFrom'");
	}

	public function ignoreFriendRequest($userFrom)
	{
		$userLoggedIn = $this->user['username'];
		$delete_query = mysqli_query($this->conn, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$userFrom'");
	}


	/*************************************************
	 * Validation functions for data inputs
	 * Part of settings_handler.php
	 *************************************************/
	public function checkFirstNameLength($fname)
	{
		//
	}

	public function checkLastNameLength($lname)
	{
		//
	}

	public function checkPasswordLength($password)
	{
		if(strlen(strlen($password) < 8 || $password > 64))
			return False;
		else
			return True;
	}

	public function checkPasswordComplexity($password, &$errorArray) {
		$errors_init = $errorArray;
	
		// if (strlen($password) < 8) {
		// 	$errorArray[] = "Password too short!";
		// }
	
		if (!preg_match("#[0-9]+#", $password)) {
			$errorArray[] = "Password must include at least one number!";
		}
	
		if (!preg_match("#[a-zA-Z]+#", $password)) {
			$errorArray[] = "Password must include at least one letter!";
		}     
	
		return ($errorArray == $errors_init);
	}

	public function checkPasswordsMatch($password, $password2)
    {
        if ($password == $password2)
            return True;
        else
            return False;
    }

	public function checkOldPasswordMatch($oldPassword)
	{
		$username = $this->user['username'];
		$passwordQuery = mysqli_query($this->conn, "SELECT password FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($passwordQuery);
		$dbHash = $row['password'];
		$oldPasswordHash = $user_obj->hashPassword($oldPassword);

		if($oldPasswordHash == $dbHash)
			return True;
		else
			return False;

	}

	public function updatePassword($newPassword)
	{
		$username = $this->user['username'];
		$newPasswordHash = $this->hashPassword($newPassword);
		$password_query = mysqli_query($this->conn, "UPDATE users SET password='$newPasswordHash' WHERE username='$username'");
	}

	public function checkEmailsMatch($email, $email2)
    {
        if ($email == $email2)
            return True;
        else
            return False;
    }

    public function checkEmailFormat($email)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
            return True;
        else
            return False;
    }

    public function checkEmailExists($email)
    {
        $query = "SELECT email FROM users WHERE email='$email'";
        $e_check = mysqli_query($this->conn, $query);

        //Count number of rows returned
        $num_rows = mysqli_num_rows($e_check);

        if($num_rows > 0)
            return True;
		else
			return False;
    }

	public function checkValidPassword($password, $userLoggedIn)
	{
		// $password = $salt_obj->hashPassword($email, $password, "");
		// This needs refactoring, currently hashPassword relies on $email, it should rely on $username
		// as a username never changes, email can be changed.

		/*
		* - get email for $userLoggedIn
		* - hash password for $user specified
		* - compare hashed password to dbHash
		* - return True/False;

		*/
		
	}

	public function getUsernameFromEmail($email)
	{
		$emailCheck = mysqli_query($this->conn, "SELECT username, email FROM users WHERE email='$email'");
		$row = mysqli_fetch_array($emailCheck);
		$username = $row['username'];

		return $username;
	}

	public function userMatch($username, $userLoggedIn)
	{
		if($matchedUser == $userLoggedIn)
			return True;
		else
			return False;
	}

	public function updateUserDetails($firstname, $lastname, $email, $password, $userLoggedIn)
	{
		$query = mysqli_query($this->conn, "UPDATE users SET first_name='$firstname', last_name='$lastname', email='$email' WHERE username='$userLoggedIn'");
		//Perform validation check with $password
	}

	public function fetchUserDetails()
	{
		//Omitted email and password from SELECT
		$username = $this->user['username'];
		$userDetailsQuery = mysqli_query($this->conn, "SELECT id,first_name,last_name,username,signup_date,profile_pic,num_posts,num_likes,user_closed,friend_array FROM users WHERE username='$username'");
		$userDetails = mysqli_fetch_array($userDetailsQuery);

		return $userDetails;
	}

	public function profileFriendsNum($username)
	{
		// $username = $this->user['username'];
		$userDetailsQuery = mysqli_query($this->conn, "SELECT friend_array FROM users WHERE username='$username'");
		$userArray = mysqli_fetch_array($userDetailsQuery);
		$numFriends = (substr_count($userArray['friend_array'], ",")) -1;

		return $numFriends;
	}

	public function profileUserArray($username)
	{
		$userDetailsQuery = mysqli_query($this->conn, "SELECT username,num_posts,num_likes,profile_pic FROM users WHERE username='$username'");
		$userDetails = mysqli_fetch_array($userDetailsQuery);
		return $userDetails;
	}

	public function settingsUserArray()
	{
		$userLoggedIn = $this->user['username'];

		$userDataQuery = mysqli_query($this->conn, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
		$row = mysqli_fetch_array($userDataQuery);

		return $row;
	}

	public function generateInviteCode()
	{
		//$rand = rand(5, 10) //Generates random number between 5-10
		$code = md5(date("Y-m-d H:i:s")); //Generate the code
		$code = str_split($code, 8); //Splits to 8 char
		$code = strtoupper($code[0]); //STR to Uppercase
		$genInviteCode = $code;
		
		$this->submitInviteCode($genInviteCode);

		return $genInviteCode;
	}

	public function submitInviteCode($genInviteCode)
	{
		mysqli_query($this->conn, "INSERT INTO `invites` (`id`, `invite_code`, `used`) 
		VALUES (NULL, '$genInviteCode', 'no')");
	}

	/*
	* Search functions
	*/
	public function usernameTypeSearch($query)
	{
		$usersReturnedQuery = mysqli_query($this->conn, "SELECT first_name, last_name, username, profile_pic, user_closed, friend_array FROM users WHERE username LIKE '$query%' AND user_closed='no'");
		return $usersReturnedQuery;
	}

	public function nameTypeSearch($names)
	{
		if(count($names) == 3)
			$usersReturnedQuery = mysqli_query($this->conn, "SELECT first_name, last_name, username, profile_pic, user_closed, friend_array FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");
		//If query has one word only, search first names or last names 
		else if(count($names) == 2)
			$usersReturnedQuery = mysqli_query($this->conn, "SELECT first_name, last_name, username, profile_pic, user_closed, friend_array FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
		else if(count($names) == 1)
			$usersReturnedQuery = mysqli_query($this->conn, "SELECT first_name, last_name, username, profile_pic, user_closed, friend_array FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");

		return $usersReturnedQuery;
	}

	public function getFriendshipStatusButton($searchedUsername)
	{
		//Buttons depending on friendship status 
		if($this->isFriend($searchedUsername))
			// $button = "<input type='submit' name='" . $searchedUsername. "' class='danger' value='Remove Friend'>";
			$button = "";
		else if($this->didReceiveRequest($searchedUsername))
			$button = "<input type='submit' name='" . $searchedUsername. "' class='warning' value='Respond to request'>";
		else if($this->didSendRequest($searchedUsername))
			$button = "<input type='submit' class='default' value='Request Sent'><br><br><br><input type='submit' name='" . $searchedUsername. "' class='warning' value='Cancel Request'>";
		else if($this->isFriend($searchedUsername) == false)
			$button = "<input type='submit' name='" . $searchedUsername. "' class='success' value='Add Friend'>";
		
		return $button;
	}

	public function friendButtonForms($searchedUsername)
	{
		// header is because page is static
		//consider rewrite to update input submit via AJAX
		if($this->isFriend($searchedUsername)) 
		{
			$this->removeFriend($searchedUsername);
			header("Location: https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		}
		else if($this->didReceiveRequest($searchedUsername)) 
		{
			header("Location: requests.php");
		}
		else if($this->didSendRequest($searchedUsername)) 
		{
			//Option to cancel friend request
			$this->cancelFriendRequest($searchedUsername);
			header("Location: https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		}
		else if($this->isFriend($searchedUsername) == false)
		{
			$this->sendRequest($searchedUsername);
			header("Location: https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		}
	}


	/*
	*	Profile image upload resizing
	*
	*/
	public function removeTempImage()
	{
		$temppath = $symDir.$profile_id.'_temp.jpeg';
		if (file_exists ($temppath))
		{ 
			@unlink($temppath); 
		}
	}

	public function uploadOriginalImage()
	{
			//Get Name | Size | Temp Location		    
			$ImageName = $_FILES['image']['name'];
			$ImageSize = $_FILES['image']['size'];
			$ImageTempName = $_FILES['image']['tmp_name'];
		//Get File Ext   
			$ImageType = @explode('/', $_FILES['image']['type']);
			$type = $ImageType[1]; //file type	
		//Set Upload directory    
			$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/'.$symDir;
		//Set File name	
			$file_temp_name = $profile_id.'_original.'.md5(time()).'n'.$type; //the temp file name
			$fullpath = $uploaddir."/".$file_temp_name; // the temp file path
			$file_name = $profile_id.'_temp.jpeg'; //$profile_id.'_temp.'.$type; // for the final resized image
			$fullpath_2 = $uploaddir."/".$file_name; //for the final resized image
		//Move the file to correct location
			$move = move_uploaded_file($ImageTempName ,$fullpath) ; 
			chmod($fullpath, 0777);  
			//Check for valid upload
			if (!$move) 
			{ 
				die ('File did NOT upload');
			} 
			else 
			{ 
				$imgSrc= $symDir.$file_name; // the image to display in crop area
				$msg= "Upload Complete!";  	//message to page
				$src = $file_name;	 		//the file name to post from cropping form to the resize		
			} 
	}

	public function resizeImage($fullpath)
	{
		//get the uploaded image size	
		clearstatcache();				
		$original_size = getimagesize($fullpath);
		$original_width = $original_size[0];
		$original_height = $original_size[1];	
		// Specify The new size
		$main_width = 500; // set the width of the image
		$main_height = $original_height / ($original_width / $main_width);	// this sets the height in ratio									
		//create new image using correct php func	
		if($_FILES["image"]["type"] == "image/gif"){
			$src2 = imagecreatefromgif($fullpath);
		}elseif($_FILES["image"]["type"] == "image/jpeg" || $_FILES["image"]["type"] == "image/pjpeg"){
			$src2 = imagecreatefromjpeg($fullpath);
		}elseif($_FILES["image"]["type"] == "image/png"){ 
			$src2 = imagecreatefrompng($fullpath);
		}else{ 
			$msg .= "There was an error uploading the file. Please upload a .jpg, .gif or .png file. <br />";
		}
		//create the new resized image
		$main = imagecreatetruecolor($main_width,$main_height);
		imagecopyresampled($main,$src2,0, 0, 0, 0,$main_width,$main_height,$original_width,$original_height);
		//upload new version
		$main_temp = $fullpath_2;
		imagejpeg($main, $main_temp, 90);
		chmod($main_temp,0777);
		//free up memory
		imagedestroy($src2);
		imagedestroy($main);
		//imagedestroy($fullpath);
		@ unlink($fullpath); // delete the original upload	
	}

	public function convertJpgToJpeg()
	{
		//
	}

	public function convertPngToJpeg()
	{
		//
	}

	public function convertGifToJpeg()
	{
		//
	}

	public function freeUpMemory()
	{
		//
	}

	public function insertImageIntoDatabase($resultPath, $userLoggedIn)
	{
		$insertPicQuery = mysqli_query($this->conn, "UPDATE users SET profile_pic='$resultPath' WHERE username='$userLoggedIn'");
		return $insertPicQuery;
	}

	public function trendingWords()
	{
		$query = mysqli_query($this->conn, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");
	
		while($row = mysqli_fetch_array($query))
		{
			$word = $row['title'];
			$word = $this->truncateWord($word);

			echo "	<div class='trendingWordsDiv'>
						$word
					</div>
					<br>
				";
		}
	}

	public function truncateWord($word)
	{
		// $word_dot = strlen($word) >= 20 ? "..." : "";
		if(strlen($word) >= 20)
			$ellipsis = "...";
		else
			$ellipsis = ""; 

		$trimmedWord = str_split($word, 20);
		$trimmedWord = $trimmedWord[0];
		$finalWord = $trimmedWord . $ellipsis;

		return $finalWord;
	}

	public function numTitleNotifications()
	{
		$userLoggedIn = $this->user['username'];

		//Unread messages
		$messages_obj = new Message($this->conn, $userLoggedIn);
		$notifications_obj = new Notification($this->conn, $userLoggedIn);

		$numMessages = $messages_obj->getUnreadNumber();
		$numNotifications = $notifications_obj->getUnreadNumber();
		$numFriendRequests = $this->getNumberOfFriendRequests();
		$numSum = $numMessages + $numNotifications + $numFriendRequests;

		return $numSum;
	}

	public function titleCreator()
	{
		$numSum = $this->numTitleNotifications();
		$title = "";

		if($numSum > 99)
			$numSumStr = "99+";
		else
			$numSumStr = $numSum;

		if($numSum > 0)
			$title = "(" . $numSumStr . ")" . " ";

		$title .= "Valhalla 2";

		return $title;
	}
	
}
?>