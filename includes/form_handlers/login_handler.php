<?php
if(isset($_POST['login_button']))
{
	//Initialise form variables
	$error_array = array();

	//Register form values AND sanitise be database
	$email = $_POST['log_email'];
	$email = strip_tags($email);
	$password = $_POST['log_password'];
	$password = strip_tags($password);
	$_SESSION['log_email'] = $email;

	//Function variables
	$email = $user_login_obj->sanitiseEmail($email);
	$password = $salt_obj->hashPassword($email, $password, "");
	$checkLoginQuery = $user_login_obj->checkLoginQuery($email, $password);

	//Error array strings
	$errIncorrectLoginCreds = $error_obj->errIncorrectLoginCreds();
	//Error array pushes
	if($checkLoginQuery == False)
		array_push($error_array, $errIncorrectLoginCreds);

	if(empty($error_array))
	{
		$username = $user_login_obj->getLoginUsername($email, $password);
		$user_login_obj->setSessionUsername($username);
		$user_login_obj->reactivateAccount($email);

		header("Location: index.php");
	}
}
?>