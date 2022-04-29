<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>

<div class="main_column column">
	<h4>Account Settings</h4>
	<?php
		echo "<img src='" . $user['profile_pic'] ."' class='small_profile_pic'>";
	?>
	<br>
	<a href="upload.php">Upload new profile picture</a><br><br><br>

	<p>Modify the values and click 'Update Details'</p>

	<?php
		$user_data_query = mysqli_query($conn, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
		$row = mysqli_fetch_array($user_data_query);

		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$email = $row['email'];
	?>

	<form action="settings.php" method="POST">
		<span>First Name: </span><input type="text" name="first_name" value="<?php echo $first_name; ?>" class="settings_input" placeholder="First Name" required><br>
		<span>Last Name: </span><input type="text" name="last_name" value="<?php echo $last_name; ?>" class="settings_input" placeholder="Last Name" required><br>
		<span>Email: </span><input type="email" name="email" value="<?php echo $email; ?>" class="settings_input" placeholder="Email" required><br>
		<span>Email (Confirm): </span><input type="email" name="email_2" value="" class="settings_input" placeholder="Confirm Email" required><br>

		<?php echo $message; ?>

		<input type="submit" name="update_details" value="Update Details" class="info settings_submit save_details"><br>
	</form>
	<br>

	<h4>Change Password</h4>
	<form action="settings.php" method="POST">
		<span>Old Password: </span><input type="password" name="old_password" class="settings_input" placeholder="Old Password" required><br>
		<span>New Password: </span><input type="password" name="new_password_1" class="settings_input" placeholder="New Password" required><br>
		<span>New Password (Confirm): </span><input type="password" name="new_password_2" class="settings_input" placeholder="Confirm New Password" required><br>

		<?php echo $password_message; ?>

		<input type="submit" name="update_password" value="Update Password" class="info settings_submit save_details"><br>
	</form>
	<br>

	<?php
		if($userLoggedIn == "safesploit")
		{
			echo "<h4>Generate Invite Code</h4>";
			echo "<p style='color: red;'>WARNING: You are able to generate an invite code!</p>";
			echo 	"	<form action='settings.php' method='POST'>
							<span>Generate Code: </span><input type='text' name='gen_invite_code' class='settings_input' placeholder='Generated Code HERE' value='$gen_invite_code'>
							<input type='submit' name='gen_invite_code' value='Generate' class='info settings_submit save_details'><br>
						</form>
						<br>
					";

			/*
			echo "<h4>All Invite Codes</h4>";
			echo 	"	<form action='settings.php' method='POST'>
							<textarea class='invite_codes'></textarea>
						</form>
						<br>
					";
			*/
		}
	?>
	
	<h4>Close Account</h4>
	<form action="settings.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
	</form>
</div>