<?php  
//include("/includes/classes/User.php");
// if(isset($_POST['update_details'])) 
// {
// 	$first_name = strip_tags($_POST['first_name']);
// 	$last_name = strip_tags($_POST['last_name']);
// 	$email = strip_tags($_POST['email']);
// 	$email_2 = strip_tags($_POST['email_2']);

// 	if($email == $email_2)
// 	{
// 		// SELECT email FROM users WHERE email='$email'
// 		//Specify the selects, less database intensive; better portability
// 		$email_check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
// 		$row = mysqli_fetch_array($email_check);
// 		$matched_user = $row['username'];

// 		/**
// 		 * UPDATE username 
// 		 * 
// 		 **/
// 		if($matched_user == "" || $matched_user == $userLoggedIn) 
// 		{
// 			$message = "Details updated!<br><br>";

// 			$query = mysqli_query($conn, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");
// 		}
// 		else 
// 		{
// 			$message = "That email is already in use!<br><br>";
// 		}
// 	}
// 	else
// 		$message = "Email addresses do not match<br><br>";
// }
// else 
// {
// 	$message = "";
// }

if(isset($_POST['update_details'])) 
{
	$firstname = strip_tags($_POST['first_name']);
	$lastname = strip_tags($_POST['last_name']);
	$email = strip_tags($_POST['email']);
	$email2 = strip_tags($_POST['email_2']);
	$password = strip_tags($_POST['password']);


	if($user_obj->checkEmailsMatch($email, $email2) == True)
	{
		if($user_obj->checkValidPassword($password))
		{
			$username = $user_obj->getUsernameFromEmail($email);

			if($user_obj->userMatch($username, $userLoggedIn) == True) 
			{
				$user_obj->updateUserDetails($firstname, $lastname, $email, $password, $userLoggedIn);
				$message = "Details updated!<br><br>";
			}
			else 
			{
				$message = "That email is already in use!<br><br>";
			}
		}
		else
			$message = "Invalid password!<br><br>";
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
if(isset($_POST['update_password'])) 
{
	// $old_password = strip_tags($_POST['old_password']);
	// $new_password_1 = strip_tags($_POST['new_password_1']);
	// $new_password_2 = strip_tags($_POST['new_password_2']);

	// $password_query = mysqli_query($conn, "SELECT password FROM users WHERE username='$userLoggedIn'");
	// $row = mysqli_fetch_array($password_query);
	// $db_password = $row['password'];

	// if($user_obj->hashPassword($old_password) == $db_password)
	// {
	// 	if($new_password_1 == $new_password_2) 
	// 	{

	// 		if(strlen($new_password_1) <= 4) 
	// 		{
	// 			$password_message = "Sorry, your password must be greater than 4 characters<br><br>";
	// 		}	
	// 		else 
	// 		{
	// 			$new_password_hashed = $user_obj->hashPassword($new_password_1);
	// 			$password_query = mysqli_query($conn, "UPDATE users SET password='$new_password_hashed' WHERE username='$userLoggedIn'");
	// 			$password_message = "Password has been changed!<br><br>";
	// 		}
	// 	}
	// 	else 
	// 	{
	// 		$password_message = "Your two new passwords need to match!<br><br>";
	// 	}
	// }
	// else 
	// {
	// 		$password_message = "The old password is incorrect! <br><br>";
	// }


	///Untested
	$oldPassword = strip_tags($_POST['old_password']);
	$newPassword1 = strip_tags($_POST['new_password_1']);
	$newPassword2 = strip_tags($_POST['new_password_2']);

	if($user_obj->checkOldPasswordMatch($oldPassword) == True)
	{
		if($user_obj->checkPasswordsMatch($newPassword1, $newPassword2) == True)
		{
			if($user_obj->checkPasswordLength($newPassword1) == True)
			{
				// $user_obj->updatePassword($newPassword1);
				$passwordMessage = "Your password has been changed!<br><br>";
			}
			else
				$passwordMessage = "Your password must be between 8-64 characters!<br><br>";
		}
		else
			$passwordMessage = "Your two new passwords need to match!<br><br>";
	}
	else
		$passwordMessage = "The old password is incorrect!<br><br>";

	///////////////////////////
}
else 
{
	$passwordMessage = "";
}

if($userLoggedIn == "safesploit")
{
	if(isset($_POST['gen_invite_code']))
	{
		$genInviteCode = $user_obj->generateInviteCode();
	}
}

if(isset($_POST['close_account'])) 
{
	header("Location: close_account.php");
}


?>