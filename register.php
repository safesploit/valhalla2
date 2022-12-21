<?php 
require_once('config/config.php'); 
require('includes/classes/Salt.php');
$salt_obj = new Salt($conn);
require('includes/classes/UserService.php');
$user_obj = new RegisterUser($conn);
$user_login_obj = new LoginUser($conn);
$error_obj = new ErrorArray();
require('includes/form_handlers/register_handler.php');
require('includes/form_handlers/login_handler.php');

//In array variables
$errEmailsNotEqual = $error_obj->errEmailsNotEqual();
$errEamilInvalidFormat =  $error_obj->errEamilInvalidFormat();
$errEmailAlreadyReg =  $error_obj->errEmailAlreadyReg();
$errPasswordsNotEqual =  $error_obj->errPasswordsNotEqual();
$errPasswordCanOnlyContain = $error_obj->errPasswordCanOnlyContain();
$errPasswordLength = $error_obj->errPasswordLength();
$errFirstnameLength = $error_obj->errFirstnameLength();
$errLastnameLength = $error_obj->errLastnameLength();
$errInviteCodeInvalid = $error_obj->errInviteCodeInvalid();
$successProfileCreated = $error_obj->successProfileCreated();
$errIncorrectLoginCreds = $error_obj->errIncorrectLoginCreds();

//Session variables
$sessionLoginEmail = $_SESSION['log_email'];
$sessionLoginPassword = $_SESSION['log_password'];
$sessionRegFirstname = $_SESSION['reg_fname'];
$sessionRegLastname = $_SESSION['reg_lname'];
$sessionRegEamil = $_SESSION['reg_em'];
$sessionRegEmail2 = $_SESSION['reg_em2'];
$sessionRegPassword = $_SESSION['reg_password'];
$sessionRegPassword2 = $_SESSION['reg_password2'];

$showHideScript = '
						<script>
							$(document).ready(function()
							{
								$("#first").hide();
								$("#second").show();
							});
						</script>
						';

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Valhalla 2 | Login or sign-up</title>
	<link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
	<link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon/logo_transparent.png">
	<script src="assets/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
</head>
<body>
	<?php
		if(isset($_POST["register_button"]))
			echo $showHideScript;
	?>
	<div class="wrapper">
		<div class="login_box">	
			<div class="login_header">
				<h1>Valhalla 2</h1>
				<p>Login or sign-up below</p>
			</div>	
		<div id="first">
			<form action="register.php" method="POST">
				<input type="email" name="log_email" placeholder="Email Address" value="<?php if(isset($sessionLoginEmail)) echo $sessionLoginEmail; ?>" required>
				<br>
				<input type="password" name="log_password" placeholder="Password" value="<?php if(isset($sessionLoginPassword)) echo $sessionLoginPassword; ?>" required>
				<br>
				<input type="submit" name="login_button" value="Login">
				<br>
				<?php 
				if(in_array($errIncorrectLoginCreds, $error_array)) 
					echo $errIncorrectLoginCreds 
				?>
				<br>
				<a href="#" id="signup" class="signup">Create New Account</a>
			</form>
		</div>

		<div id="second">
			<form action="register.php" method="POST">
				<input type="text" name="reg_fname" placeholder="First name" value="<?php if(isset($sessionRegFirstname)) {echo $sessionRegFirstname;} ?>" autocomplete="off" required>
				<br>
				<?php 
				if(in_array($errFirstnameLength, $error_array)) 
					echo $errFirstnameLength; 
				?>
				<input type="text" name="reg_lname" placeholder="Last name" value="<?php if(isset($sessionRegLastname)) {echo $sessionRegLastname;} ?>" autocomplete="off" required>
				<br>
				<?php 
				if(in_array($errLastnameLength, $error_array)) 
				echo $errLastnameLength; 
				?>
				<input type="email" name="reg_em" placeholder="Email" value="<?php if(isset($sessionRegEamil)) {echo $sessionRegEamil;} ?>" autocomplete="off" required>
				<br>
				<input type="email" name="reg_em2" placeholder="Confirm Email" value="<?php if(isset($sessionRegEmail2)) {echo $sessionRegEmail2;} ?>" autocomplete="off" required>
				<br>
				<?php 
				if(in_array($errEmailsNotEqual, $error_array)) 
					echo $errEmailsNotEqual;
				else if(in_array($errEmailAlreadyReg, $error_array)) 
					echo $errEmailAlreadyReg;
				else if(in_array($errEamilInvalidFormat, $error_array)) 
					echo $errEamilInvalidFormat; 
				?>
				<input type="password" name="reg_password" placeholder="Password" value="<?php if(isset($sessionRegPassword)) {echo $sessionRegPassword;} ?>" autocomplete="off" required>
				<br>
				<input type="password" name="reg_password2" placeholder="Confirm Password" value="<?php if(isset($sessionRegPassword2)) {echo $sessionRegPassword2;} ?>" autocomplete="off" required>
				<br>
				<?php
				if(in_array($errPasswordsNotEqual, $error_array)) 
					echo $errPasswordsNotEqual;
				else if(in_array($errPasswordCanOnlyContain, $error_array)) 
					echo $errPasswordCanOnlyContain;
				else if(in_array($errPasswordLength, $error_array)) 
					echo $errPasswordLength; 
				?>
				<!-- 
				<input type="text" name="reg_invite_code" placeholder="Invite Code (optional)" value="" autocomplete="off"> 
				-->
				<br>
				<?php 	
					if(in_array($errInviteCodeInvalid, $error_array))  
						echo $errInviteCodeInvalid;
				?>
				<input type="submit" name="register_button" value="Register">
				<br>
				<?php 
				if(in_array($successProfileCreated, $error_array)) 
					echo $successProfileCreated; 
				?>
				<a href="#" id="signin" class="signup">Already have an account? Sign in here!</a>
			</form>
		</div>
	</div>
</body>
</html>