<?php
session_start();

//Initialises the form variables
$fname = "";
$lname = "";
$em = "";
$em2 = "";
$password = "";
$password2 = "";
$date = "";
$profile_pic = "";
$error_array = array(); //Holds error messages (e.g. if email is already in use)
$invite_code = "";

if(isset($_POST['register_button']))
{
	//include form variables
	include("includes/form_handlers/includes/sanitise_register_form_variables.php");
	//Can be rewritten into a function

	//Check if emails match
	if ($em == $em2)
	{
		//Checks if $em==$em2
		if(filter_var($em, FILTER_VALIDATE_EMAIL))
		{
			//Check if email already exists
			$query = "SELECT email FROM users WHERE email='$em'";
			$e_check = mysqli_query($conn, $query);

			//Count number of rows returned
			$num_rows = mysqli_num_rows($e_check);

			if($num_rows > 0)
			{
				array_push($error_array, "Email is already registered <br>");
			}
		}
		else
		{
			array_push($error_array, "Invalid email format<br>");
		}
	}
	else
	{
		array_push($error_array, "Emails do not match <br>");
	}

	//Check if passwords match
	if ($password != $password2)
	{
		array_push($error_array, "Passwords do not match <br>");
	}
	else
	{
		if(preg_match('/[^A-Za-z0-9]/', $password))
		{
			array_push($error_array, "Your password can only contain a-z, A-Z, 0-9 as characters <br>");
		}
	}

	//Check password length
	//include('includes/classes/User.php'); //place this in /register.php
	//Then $user_obj = new User($conn, )
	//if($user_obj->checkPasswordLen($password) == true) //
	if(strlen($password > 64 || strlen($password) < 8))
	{
		array_push($error_array, "Your password must be between between 8 and 64 characters <br>");
	}

	if(strlen($fname) > 50 || strlen($fname) < 2)
	{
		array_push($error_array, "Your first name must be between 2 and 50 characters <br>");
	}

	if(strlen($lname) > 50 || strlen($lname) < 2)
	{
		array_push($error_array, "Your last name must be between 2 and 50 characters <br>");
	}

	//Check invite code is valid
	$invite_query = mysqli_query($conn, "SELECT * FROM invites WHERE invite_code='$invite_code' AND used='no'");
	if(mysqli_num_rows($invite_query))
	{
		$invite_query = mysqli_query($conn, "UPDATE invites SET used='yes' WHERE invites.invite_code='$invite_code'");
	}
	else
	{
		array_push($error_array, "Your invite code is invalid or has been used <br>");
	}

	//Creates a unique username
	include("includes/form_handlers/includes/generate_username.php");

	//Generates salt
	//Submit salt into Salts table
	//Hash password
	if(empty($error_array))
	{
		//include("includes/form_handlers/hash_password_pbkdf2.php");
		$salt = $salt_obj->generateSalt();
		$salt_obj->submitSalt($salt, $username);
		$password = $salt_obj->hashPassword("", $password, $salt);
	}

	//Profile picture assignment
	include("includes/form_handlers/includes/profile_pic.php");

	//if $error_array is empty then submit query
	if(empty($error_array))
	{
	$sql = "INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `signup_date`, `profile_pic`, `num_posts`, `num_likes`, `user_closed`, `friend_array`) VALUES (NULL, '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')";
	$query = mysqli_query($conn, $sql);

	array_push($error_array, "<span>Profile successfully created. <br> You can now login!<br></span>");

	//Clear session variables
	include("includes/form_handlers/includes/clear_session_variables.php");
	}
}
?>