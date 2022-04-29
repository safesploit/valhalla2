<?php 
	require_once('config/config.php'); 
	require('includes/classes/Salt.php');
	$salt_obj = new Salt($conn);
	require('includes/form_handlers/register_handler.php');
	require('includes/form_handlers/login_handler.php'); 
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Valhalla 2.0 | Login or sign-up</title>
	<link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
	<link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon/logo_transparent.png">
	<script src="assets/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
</head>
<body>
	<?php
		if(isset($_POST['register_button']))
		{
			echo '
				<script>
					$(document).ready(function()
					{
						$("#first").hide();
						$("#second").show();
					});
				</script>
			';
		}
	?>

	<?php

	
	//$user = new User($conn, $userLoggedIn);

	//echo $salt_obj->submitSalt($salt_obj->generateSalt(), $_POST['username']);
	?>

	<div class="wrapper">
		<div class="login_box">	
			<div class="login_header">
				<h1>Valhalla 2.0</h1>
				<p>Login or sign-up below</p>
			</div>	
		<div id="first">
			<form action="register.php" method="POST">
				<input type="email" name="log_email" placeholder="Email Address" value="<?php if(isset($_SESSION['log_email'])) echo $_SESSION['log_email']; ?>" required>
				<br>
				<input type="password" name="log_password" placeholder="Password" value="<?php if(isset($_SESSION['log_password'])) echo $_SESSION['log_password']; ?>" required>
				<br>
				<input type="submit" name="login_button" value="Login">
				<br>
				<?php if(in_array("Email or Password was incorrect<br>", $error_array)) echo "Email or Password was incorrect<br>" ?>
				<br>
				<a href="#" id="signup" class="signup">Create New Account</a>
			</form>
		</div>

		<div id="second">
			<form action="register.php" method="POST">
				<input type="text" name="reg_fname" placeholder="First name" value="<?php if(isset($_SESSION['reg_fname'])) {echo $_SESSION['reg_fname'];} ?>" autocomplete="off" required>
				<br>
				<?php if(in_array("Your first name must be between 2 and 50 characters <br>", $error_array)) echo "Your first name must be between 2 and 50 characters <br>"; ?>
				<input type="text" name="reg_lname" placeholder="Last name" value="<?php if(isset($_SESSION['reg_lname'])) {echo $_SESSION['reg_lname'];} ?>" autocomplete="off" required>
				<br>
				<?php if(in_array("Your last name must be between 2 and 50 characters <br>", $error_array)) echo "Your last name must be between 2 and 50 characters <br>"; ?>
				<input type="email" name="reg_em" placeholder="Email" value="<?php if(isset($_SESSION['reg_em'])) {echo $_SESSION['reg_em'];} ?>" autocomplete="off" required>
				<br>
				<input type="email" name="reg_em2" placeholder="Confirm Email" value="<?php if(isset($_SESSION['reg_em2'])) {echo $_SESSION['reg_em2'];} ?>" autocomplete="off" required>
				<br>
				<?php 
					if(in_array("Emails do not match <br>", $error_array)) echo "Emails do not match <br>";
				 	else if(in_array("Email is already registered <br>", $error_array)) echo "Email is already registered <br>";
				 	else if(in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>"; 
				?>
				<input type="password" name="reg_password" placeholder="Password" value="<?php if(isset($_SESSION['reg_password'])) {echo $_SESSION['reg_password'];} ?>" autocomplete="off" required>
				<br>
				<input type="password" name="reg_password2" placeholder="Confirm Password" value="<?php if(isset($_SESSION['reg_password2'])) {echo $_SESSION['reg_password2'];} ?>" autocomplete="off" required>
				<br>
				<input type="text" name="reg_invite_code" placeholder="Invite Code" value="" autocomplete="off">
				<br>
				<?php 	
					if(in_array("Passwords do not match <br>", $error_array)) echo "Passwords do not match <br>";
					else if(in_array("Your password can only contain a-z, A-Z, 0-9 as characters <br>", $error_array)) echo "Your password can only contain a-z, A-Z, 0-9 as characters <br>";
					else if(in_array("Your password must be between between 8 and 64 characters <br>", $error_array)) echo "Your password must be between between 8 and 64 characters <br>"; 
					else if(in_array("Your invite code is invalid or has been used <br>", $error_array))  echo "Your invite code is invalid or has been used <br>";
				?>

				<input type="submit" name="register_button" value="Register">
				<br>
				<?php if(in_array("<span>Profile successfully created. <br> You can now login!<br></span>", $error_array)) echo "<br><span style='color: green'>Profile successfully created. <br> You can now login!<br></span>"; ?>
				<a href="#" id="signin" class="signup">Already have an account? Sign in here!</a>
			</form>
		</div>
		</div>
	</div>
</body>
</html>