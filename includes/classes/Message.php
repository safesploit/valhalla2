<?php
	class Message
	{
		private $user_obj;
		private $conn;

		public function __construct($conn, $user)
		{
			$this->conn = $conn;
			$this->user_obj = new User($conn, $user);
		}

		public function getMostRecentUser()
		{
			$userLoggedIn = $this->user_obj->getUsername();

			$query = mysqli_query($this->conn, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1");

			if(mysqli_num_rows($query) == 0)
			{
				return false;
			}

			$row = mysqli_fetch_array($query);
			$user_to = $row['user_to'];
			$user_from = $row['user_from'];

			if($user_to != $userLoggedIn)
			{
				return $user_to;
			}
			else
			{
				return $user_from;
			}
		}

		public function sendMessage($user_to, $body, $date)
		{
			if($body != "")
			{
				$userLoggedIn = $this->user_obj->getUsername();
				$sql = "INSERT INTO `messages` (`id`, `user_to`, `user_from`, `body`, `date`, `opened`, `viewed`, `deleted`) VALUES (NULL, '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')";
				$query = mysqli_query($this->conn, $sql);
			}
		}

		public function getMessages($otherUser)
		{
			$userLoggedIn = $this->user_obj->getUsername();
			$data = "";

			$query = mysqli_query($this->conn, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");

			$get_messages_query = mysqli_query($this->conn, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')");

			while($row = mysqli_fetch_array($get_messages_query))
			{
				$user_to = $row['user_to'];
				$user_from = $row['user_from'];
				$body = $row['body'];
				$id = $row['id'];
				$date = $row['date'];
				//$new_date = ('<b>' . date("H:i", strtotime($date)) . '</b>' . ' ') . date("d M", strtotime($date));

				$div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
				$button = "<span class='deleteButton' onclick='deleteMessage($id, this)'>X</span>";
				$data = $data . $div_top . $button . $body . "</div><br><br>";

			/** DateTime for each message.
			 * 
			 * 
				$div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
				$data = $data . $div_top . $body;
					$span_date = ($user_to == $userLoggedIn) ? "<span id='message_date_green'>" : "<span id='message_date_blue'>";
					$data = $data . '<br>' . $span_date . $new_date . "</span>";
				$data = $data . "</div><br><br>";DateTime for each message
			**/

			}
			return $data;
		}

		public function getLatestMessage($userLoggedIn, $user2)
		{
			$details_array = array();

			$query = mysqli_query($this->conn, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");

			$row = mysqli_fetch_array($query);
			$sent_by = ($row['user_to'] == $userLoggedIn) ? "They: " : "You: ";

			//Timeframe
			include("Message_timeframe.php");

			array_push($details_array, $sent_by);
			array_push($details_array, $row['body']);
			array_push($details_array, $time_message);

			return $details_array;
		}

		public function getConvos()
		{
			$userLoggedIn = $this->user_obj->getUsername();
			$return_string = "";
			$convos = array();

			$query = mysqli_query($this->conn, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");

			while($row = mysqli_fetch_array($query))
			{
				$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

				//Check username is not already in convos array
				if(!in_array($user_to_push, $convos))
				{
					array_push($convos, $user_to_push);
				}
			}

			foreach($convos as $username)
			{
				$user_found_obj = new User($this->conn, $username);
				$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

				//if length of body from $latest_message_details array then
				$dots = (strlen($latest_message_details[1]) >= 42) ? "..." : "";
				$split = str_split($latest_message_details[1], 42);
				$split = $split[0] . $dots;
				
			/* Initial $return_string .=
				$return_string .= 	"	<a href='messages.php?u=$username'> <div class='user_found_messages'>
										<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
										" . $user_found_obj->getFirstAndLastName() . "
										<span class='timestamp_smaller' id='grey'> " . $latest_message_details[2] . "</span>
										<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
										</div>
										</a>
									";
			* One below includes <br>
			*/ 

				$return_string .= 	"	<a href='messages.php?u=$username'> <div class='user_found_messages'>
										<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
										" . $user_found_obj->getFirstAndLastName() . "
										<br>
										<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " 
										<span class='timestamp_smaller' id='grey'> " . ' • ' . $latest_message_details[2] . "</span>
										</p>
										
										</div>
										</a>
									";
									
			}

			return $return_string;
		}

		public function getConvosDropdown($data, $limit)
		{
			$page = $data['page'];
			$userLoggedIn = $this->user_obj->getUsername();
			$return_string = "";
			$convos = array();


			if($page == 1)
			{
				$start = 0;
			}
			else
			{
				$start = ($page - 1) * $limit;
			}

			$set_viewed_query = mysqli_query($this->conn, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn'");


			$query = mysqli_query($this->conn, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");

			while($row = mysqli_fetch_array($query))
			{
				$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

				//Check username is not already in convos array
				if(!in_array($user_to_push, $convos)) 
				{
					array_push($convos, $user_to_push);
				}
			}

			$num_iterations = 0; //Number of messages check
			$count = 1; //Number of messages posted

			foreach($convos as $username)
			{
				//Increment done inside if-statement as opposed to inside { }
				if($num_iterations++ < $start)
				{
					continue;
				}

				if($count++ > $limit)
				{
					break;
				}

				//Regarding the $style = (isset()) part refer to
				//123. Retrieving the Data for our Dropdown Window
				//which explains the revision made to PHP
				//THESE COMMENTS CAN BE DELETED
				$is_unread_query = mysqli_query($this->conn, "SELECT opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$username' ORDER BY id DESC");
				$row = mysqli_fetch_array($is_unread_query);
				$style = (isset($row['opened']) && $row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";


				$user_found_obj = new User($this->conn, $username);
				$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

				//if length of body from $latest_message_details array then
				$dots = (strlen($latest_message_details[1]) >= 42) ? "..." : "";
				$split = str_split($latest_message_details[1], 42);
				$split = $split[0] . $dots;
				
			/* Initial $return_string .=
				$return_string .= 	"	<a href='messages.php?u=$username'> <div class='user_found_messages'>
										<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
										" . $user_found_obj->getFirstAndLastName() . "
										<span class='timestamp_smaller' id='grey'> " . $latest_message_details[2] . "</span>
										<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
										</div>
										</a>
									";
			* One below includes <br>
			*/ 

				$return_string .= 	"	<a href='messages.php?u=$username'> 
										<div class='user_found_messages' style='" . $style . "'>
										<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
										" . $user_found_obj->getFirstAndLastName() . "
										<br>
										<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " 
										<span class='timestamp_smaller' id='grey'> " . ' • ' . $latest_message_details[2] . "</span>
										</p>
										
										</div>
										</a>
									";
									
			}

			//If posts were loaded
			if($count > $limit)
				{
					$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
				}
			else 
				{
					$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more messages to load!</p>";
				}

			return $return_string;
		}

		public function getUnreadNumber()
		{
			$userLoggedIn = $this->user_obj->getUsername();
			$query = mysqli_query($this->conn, "SELECT * FROM messages WHERE viewed='no' AND user_to='$userLoggedIn'");
			return mysqli_num_rows($query);
		}

	}
?>