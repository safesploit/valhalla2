<?php  
//include("/includes/classes/User.php");
	if(isset($_POST['update_details'])) 
	{
		$first_name = strip_tags($_POST['first_name']);
		$last_name = strip_tags($_POST['last_name']);
		$email = strip_tags($_POST['email']);
		$email_2 = strip_tags($_POST['email_2']);

		

		if($email == $email_2)
		{
			$email_check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
			$row = mysqli_fetch_array($email_check);
			$matched_user = $row['username'];

			/**
			 * UPDATE username 
			 * 
			 **/
			if($matched_user == "" || $matched_user == $userLoggedIn) 
			{
				$message = "Details updated!<br><br>";

				$query = mysqli_query($conn, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");
			}
			else 
			{
				$message = "That email is already in use!<br><br>";
			}
		}
		else
			$message = "Email addresses do not match<br><br>";
	}
	else 
	{
		$message = "";
	}

	/****************************************************
	 * UPDATE password 
	 ****************************************************/

	//echo $user_obj->hashPassword('password'); //THIS IS FOR TESTING PURPOSES

	if(isset($_POST['update_password'])) 
	{
		$old_password = strip_tags($_POST['old_password']);
		$new_password_1 = strip_tags($_POST['new_password_1']);
		$new_password_2 = strip_tags($_POST['new_password_2']);

		$password_query = mysqli_query($conn, "SELECT password FROM users WHERE username='$userLoggedIn'");
		$row = mysqli_fetch_array($password_query);
		$db_password = $row['password'];

		if($user_obj->hashPassword($old_password) == $db_password)
		{
			if($new_password_1 == $new_password_2) 
			{

				if(strlen($new_password_1) <= 4) 
				{
					$password_message = "Sorry, your password must be greater than 4 characters<br><br>";
				}	
				else 
				{
					$new_password_hashed = $user_obj->hashPassword($new_password_1);
					$password_query = mysqli_query($conn, "UPDATE users SET password='$new_password_hashed' WHERE username='$userLoggedIn'");
					$password_message = "Password has been changed!<br><br>";
				}
			}
			else 
			{
				$password_message = "Your two new passwords need to match!<br><br>";
			}
		}
		else 
		{
				$password_message = "The old password is incorrect! <br><br>";
		}
	}
	else 
	{
		$password_message = "";
	}

	/****************************************************
	 * GENERATE invite code if Admin
	 * 
	 * Displays code below if $userLoggedIn = "safesploit"
	 ****************************************************/
	if($userLoggedIn == "safesploit")
	{			
		if(isset($_POST['gen_invite_code']))
		{
			//$rand = rand(5, 10) //Generates random number between 5-10
			$code = md5(date("Y-m-d H:i:s")); //Generate the code
			$code = str_split($code, 8); //Splits to 8 char
			$code = strtoupper($code[0]); //STR to Uppercase
			$gen_invite_code = $code;
			
			$query = mysqli_query($conn, "INSERT INTO `invites` (`id`, `invite_code`, `used`) VALUES (NULL, '$gen_invite_code', 'no')");
		}
	}


if(isset($_POST['close_account'])) 
{
	header("Location: close_account.php");
}


?>