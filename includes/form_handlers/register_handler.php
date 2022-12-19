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
	$emailMatchReturn = $user_obj->checkEmailsMatch($em, $em2);
	$emailFormatReturn = $user_obj->checkEmailFormat($em);
	$emailExistsReturn = $user_obj->checkEmailExists($em);
	$passwordMatchReturn = $user_obj->checkPasswordsMatch($password, $password2);
	$passwordContainsReturn = $user_obj->checkPasswordContains($password, $password2);
	$checkPasswordLengthReturn = $user_obj->checkPasswordLength($password);
	$checkFirstnameLengthReturn = $user_obj->checkFirstnameLength($fname);
	$checkLastnameLengthReturn = $user_obj->checkLastnameLength($lname);

	//Error array strings
	$errEmailsNotEqual = $error_obj->errEmailsNotEqual();;
	$errEamilInvalidFormat =  $error_obj->errEamilInvalidFormat();
	$errEmailAlreadyReg =  $error_obj->errEmailAlreadyReg();
	$errPasswordsNotEqual =  $error_obj->errPasswordsNotEqual();
	$errPasswordCanOnlyContain = $error_obj->errPasswordCanOnlyContain();
	$errPasswordLength = $error_obj->errPasswordLength();
	$errFirstnameLength = $error_obj->errFirstnameLength();
	$errLastnameLength = $error_obj->errLastnameLength();
	$errInviteCodeInvalid = $error_obj->errInviteCodeInvalid();
	$successProfileCreated = $error_obj->successProfileCreated();

	//Error array pushes
	if($emailMatchReturn == False)
		array_push($error_array, $errEmailsNotEqual);
	if($emailFormatReturn == False)
		array_push($error_array, $errEamilInvalidFormat);
	if($emailExistsReturn == True)
		array_push($error_array, $errEmailAlreadyReg);
	if ($passwordMatchReturn == False)
		array_push($error_array, $errPasswordsNotEqual);
	if($passwordContainsReturn == True)
		array_push($error_array, $errPasswordCanOnlyContain);
	if($checkPasswordLengthReturn == True)
		array_push($error_array, $errPasswordLength);
	if($checkFirstnameLengthReturn == True)
		array_push($error_array, $errFirstnameLength);
	if($checkLastnameLengthReturn == True)
		array_push($error_array, $errLastnameLength);	
	// if($user_obj->inviteCodeCheck($invite_code) == False)
	// 	array_push($error_array, $errInviteCodeInvalid);

	$username = $user_obj->generateUsername($fname, $lname);
	$profile_pic = $user_obj->profilePic();

	//Create salt and hash password
	if(empty($error_array))
	{
		$salt = $salt_obj->generateSalt();
		$salt_obj->submitSalt($salt, $username);
		$password = $salt_obj->hashPassword("", $password, $salt);
	}

	if(empty($error_array))
	{
		$user_obj->submitRegisterQuery($fname, $lname, $username, $em, $password, $date, $profile_pic);
		array_push($error_array, $successProfileCreated);
		session_destroy(); //Clear session variables
	}
}
?>