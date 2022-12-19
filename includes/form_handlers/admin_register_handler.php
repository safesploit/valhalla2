<?php
session_start();

if(isset($_POST['register_button']))
{
	//Initialise form variables
	$fname = $lname = $em = $em2 = $password = $password2 = $date = $profile_pic = $invite_code = "";
	$error_array = array();

	//Register form values AND sanitise be database
	//First name
	$fname = strip_tags($_POST['reg_fname']); //Remove HTML tags
	$fname = str_replace(' ', '', $fname); //replaces spaces with empty char ' ' --> ''
	$fname = ucfirst(strtolower($fname)); //Changes all chars to lowercase, except first
	$_SESSION['reg_fname'] = $fname; //Stores first name into session variable
	//Last name
	$lname = strip_tags($_POST['reg_lname']); //Remove HTML tags
	$lname = str_replace(' ', '', $lname); //replaces spaces with empty char ' ' --> ''
	$lname = ucfirst(strtolower($lname)); //Changes all chars to lowercase, except first
	$_SESSION['reg_lname'] = $lname; //Stores last name into session variable
	//email
	$em = strip_tags($_POST['reg_em']); //Remove HTML tags
	$em2 = strip_tags($_POST['reg_em2']); //Remove HTML tags
	$_SESSION['reg_em'] = $em; //Stores em into session variable
	$_SESSION['reg_em2'] = $em2; //Stores em2 into session variable
	//password
	$password = strip_tags($_POST['reg_password']); //Remove HTML tags
	$password2 = strip_tags($_POST['reg_password2']); //Remove HTML tags
	$_SESSION['reg_password'] = $password; //Stores password into session variable
	$_SESSION['reg_password2'] = $password2; //Stores password2 into session variable
	//date
	$date = date("Y-m-d"); //Current date
	//invite_code
	$invite_code = strip_tags($_POST['reg_invite_code']); //Remove HTML tags
	$_SESSION['reg_invite_code'] = $invite_code; //Stores invite_code into session variable

	//Function variables
	$emailMatchReturn = $admin_reg_obj->checkEmailsMatch($em, $em2);
	$emailFormatReturn = $admin_reg_obj->checkEmailFormat($em);
	$emailExistsReturn = $admin_reg_obj->checkEmailExists($em);
	$passwordMatchReturn = $admin_reg_obj->checkPasswordsMatch($password, $password2);
	$passwordContainsReturn = $admin_reg_obj->checkPasswordContains($password, $password2);
	$checkPasswordLengthReturn = $admin_reg_obj->checkPasswordLength($password);
	$checkFirstnameLengthReturn = $admin_reg_obj->checkFirstnameLength($fname);
	$checkLastnameLengthReturn = $admin_reg_obj->checkLastnameLength($lname);

	//Error array pushes
	if($emailMatchReturn == False)
		array_push($error_array, "Emails do not match <br>");
	if($emailFormatReturn == False)
		array_push($error_array, "Invalid email format<br>");
	if($emailExistsReturn == True)
		array_push($error_array, "Email is already registered<br>");
	if ($passwordMatchReturn == False)
		array_push($error_array, "Passwords do not match <br>");
	if($passwordContainsReturn == True)
		array_push($error_array, "Your password can only contain a-z, A-Z, 0-9 as characters <br>");
	if($checkPasswordLengthReturn == True)
		array_push($error_array, "Your password must be between between 8 and 64 characters <br>");
	if($checkFirstnameLengthReturn == True)
		array_push($error_array, "Your first name must be between 2 and 50 characters <br>");
	if($checkLastnameLengthReturn == True)
		array_push($error_array, "Your last name must be between 2 and 50 characters <br>");	
		
	$username = $admin_reg_obj->generateUsername($fname, $lname);
	$profile_pic = $admin_reg_obj->profilePic();

	//Create salt and hash password
	if(empty($error_array))
	{
		$salt = $salt_obj->generateSalt();
		$salt_obj->submitSalt($salt, $username);
		$password = $salt_obj->hashPassword("", $password, $salt);
	}

	// if(empty($error_array))
	// {
	// 	if($admin_reg_obj->inviteCodeCheck($invite_code) == False)
	// 		array_push($error_array, "Your invite code is invalid or has been used <br>");
	// }

	if(empty($error_array))
	{
		$admin_reg_obj->submitRegisterQuery($fname, $lname, $username, $em, $password, $date, $profile_pic);
		array_push($error_array, "<span>Profile successfully created. <br> You can now login!<br></span>");
		session_destroy(); //Clear session variables
	}
}
?>