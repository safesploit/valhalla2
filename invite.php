<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
</head>
<body>
	<div>
		<form action="invite.php" method="POST">
			<label for="invite_code">Enter Invite Code Below:</label><br>
			<input type="text" name="invite_code" value=""><br><br>
			<input type="submit" value="Submit">
		</form>
	</div>
</body>
</html>

<?php
/***
 * This file is for handling the invite codes
 * 
 **/
include("config/config.php");

$invite_code = $_POST['invite_code'];
$get_invite_code_query = mysqli_query($conn, "SELECT * FROM invites WHERE invite_code='$invite_code' AND used='no'");
$check_valid_query = mysqli_num_rows($get_invite_code_query);

if(isset($check_valid_query))
	if($check_valid_query == 1)
		{
			$query = mysqli_query($conn, "UPDATE invites SET used='yes'");
			echo 'Your account has been created.<br>';
			$invite_code = "";
			destroy_session();
		}
		else if($check_valid_query != 1)
		{
			echo 'Invite code has been used or is invalid';
			destroy_session();
		}
?>