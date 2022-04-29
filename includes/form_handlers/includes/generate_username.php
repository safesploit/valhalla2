<?php
	//Creates a unique username
	if(empty($error_array))
	{
		//Generate username by concatenating first name and last name
		$username = strtolower($fname . "_" . $lname);
		$check_username_query = mysqli_query($conn, "SELECT username FROM users WHERE username='$username'");

		$i = 0;
		//if username exists add number to username
		while(mysqli_num_rows($check_username_query) != 0)
		{
			$i++; //Increment $i
			$username = $username . "_" . $i;
			$check_username_query = mysqli_query($conn, "SELECT username FROM users WHERE username='$username'");
		}

	}
?>