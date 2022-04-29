<?php
	$sql = "SELECT * FROM users WHERE email='$email' AND user_closed='yes'";
	$user_closed_query = mysqli_query($conn, $sql);
	if(mysqli_num_rows($user_closed_query) == 1)
	{
		$sql = "UPDATE users SET user_closed='no' WHERE email='$email'";
		$reopen_account = mysqli_query($conn, $sql);

		echo ("User account was set to closed.<br>");
		echo ("The user account has been successfully reopened.<br>");
	}
?>