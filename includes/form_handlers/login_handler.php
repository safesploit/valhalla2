<?php
	if(isset($_POST['login_button']))
	{
		$email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //sanatise email
		$_SESSION['log_email'] = $email; //Store email into session variable

		//Hashes password
		$password = $_POST['log_password'];
		//include("includes/form_handlers/hash_password_pbkdf2.php");
		$password = $salt_obj->hashPassword($email, $password, "");

		$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
		$check_database_query = mysqli_query($conn, $sql);
		$check_login_query = mysqli_num_rows($check_database_query);

		if($check_login_query == 1)
		{
			$row = mysqli_fetch_array($check_database_query);
			$username = $row['username']; //sets $username equal to fetch_array username
			$_SESSION['username'] = $username; //creates session variable equal to fetched $username

			include('includes/form_handlers/includes/reopen_account.php'); //sets user_closed="no"

			header("Location: index.php"); //Redirects to index.php if login is successful
			exit();
		}
		else
		{
			array_push($error_array, "Email or Password was incorrect<br>");
		}
	}
?>