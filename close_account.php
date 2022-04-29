<?php
	include("includes/header.php");

	if(isset($_POST['cancel'])) 
	{
		header("Location: settings.php");
	}

	if(isset($_POST['close_account'])) 
	{
		$close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
		session_destroy();
		header("Location: register.php");
	}
?>

<div class="main_column column">
	<h4>Close Account</h4>

	<p>Are you sure you want to close your account?</p><br>
	<p>Closing your account will hide your profile and all your activity from other users.</p><br>
	<p>You can re-open your account at any time by simply logging in.</p><br>

	<form action="close_account.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
		<input type="submit" name="cancel" id="update_details" value="Return to Settings" class="info settings_submit">
	</form>
</div>