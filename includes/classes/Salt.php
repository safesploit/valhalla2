<?php
	class Salt
	{
		private $salt_obj;
		private $conn;

		public function __construct($conn)
		{
			$this->conn = $conn;
		}

		/*************************************************
		 * Password related functions
		 *************************************************/
		public function hashPassword($email, $password, $salt)
		{
			$salt_obj = new Salt($conn);
			//PBKDF2 Hash Function
			if($email != "")
				$salt = $this->getSalt($email);
			$iterations = 100000;
			$length = 32;

			//Temporary while users migrate
			//If no salt was returned from Salts table
			if($salt == "")
				$salt = "ac3478d69a3c81fa62e60f5c3696165a4e5e6ac4";

			$hash = hash_pbkdf2("sha256", $password, $salt, $iterations, $length);
			return $hash;
		}

		public function generateSalt()
		{
			// Generate a random IV using openssl_random_pseudo_bytes()
			// random_bytes() or another suitable source of randomness
			//$salt = openssl_random_pseudo_bytes(16);
			$salt = md5(date("Y-m-d H:i:s"));
			return $salt;
		}

		public function getSalt($email)
		{
			//getUsername from email
			$queryUsers = mysqli_query($this->conn, "SELECT username email FROM users WHERE email='$email'");
			$row = mysqli_fetch_array($queryUsers);
			$getUsername = $row['0'];

			//getSalt from row where username matches
			$querySalts = mysqli_query($this->conn, "SELECT * FROM salts WHERE username='$getUsername'");
			$row = mysqli_fetch_array($querySalts);
			$salt = $row['salt'];
			return $salt;
		}

		public function submitSalt($salt, $username)
		{
			$salt_obj = new Salt($conn);
			//Not needed as both ($salt, $username) are generated on server-side
			//$salt = mysqli_escape_string($this->conn, $salt);
			//$username = mysqli_escape_string($this->conn, $username);

			$sql = "INSERT INTO `salts` (`id`, `username`, `salt`) VALUES (NULL, '$username', '$salt')";
			$query = mysqli_query($this->conn, $sql);
		}


	}
?>