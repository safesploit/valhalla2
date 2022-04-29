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
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public function isFriend($username_to_check)
		{
			$usernameComma = "," . $username_to_check . ",";

			//Check if username is in 'friend_array'
			if(strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username'])
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public function didReceiveRequest($user_from)
		{
			$user_to = $this->user['username'];
			$check_request_query = mysqli_query($this->conn, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
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
			$check_request_query = mysqli_query($this->conn, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
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
			$query = mysqli_query($this->conn, "SELECT * FROM friend_requests WHERE user_to='$username'");
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
		 * Validation functions for data inputs
		 *************************************************/
		public function checkFirstNameLen($fname)
		{
			//
		}

		public function checkLastNameLen($lname)
		{
			//
		}

		public function checkPasswordLen($password)
		{
			if(strlen($password > 64 || strlen($password) < 8))
				return false;
			else
				return true;
		}

		public function checkPasswordComplexity($password)
		{
			//
		}
		
		
	}
?>