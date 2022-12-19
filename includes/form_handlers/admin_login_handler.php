<?php
if(isset($_POST['login_button']))
{
	$error_array = array();

	$email = $_POST['log_email'];
	$email = strip_tags($email);
	$password = $_POST['log_password'];
	$password = strip_tags($password);
	$_SESSION['log_email'] = $email;

	$email = $admin_login_obj->sanitiseEmail($email);
	$password = $admin_pass_obj->hashPassword($email, $password, "");

	$checkLoginQuery = $admin_login_obj->checkLoginQuery($email, $password);
    
	if($checkLoginQuery == True)
	{
		$username = $admin_login_obj->getLoginUsername($email, $password);
		$admin_login_obj->setSessionUsername($username);
		$admin_login_obj->reactivateAccount($email);

		header("Location: vl-admin-panel.php");
	}
	else
	{
		array_push($error_array, "Email or Password was incorrect!<br>");
	}
}
?>