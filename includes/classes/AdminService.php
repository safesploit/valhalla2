<?php
class LoginAdminUser
{
	private $user_login_obj;
	private $conn;

	public function __construct($conn)
	{
		$this->conn = $conn;
	}

    public function sanitiseEmail($email)
    {
        return filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL);
    }


    public function checkLoginQuery($email, $password)
    {
        // $sql = $conn->prepare("SELECT username, password FROM users WHERE email='$email' AND password='$password'");
        // $sql->bind_param($email, $email);
        // $sql->bind_param($password, $password);
        // $sql->execute();

        // $sql = "SELECT admin_users.email,admin_passwords.password FROM admin_users admin_passwords WHERE email='$email' AND password='$password'";
        $sql = "SELECT admin_users.email,admin_passwords.password FROM admin_users INNER JOIN admin_passwords WHERE admin_users.email='$email' AND admin_passwords.password = '$password'";
        $checkDatabaseQuery = mysqli_query($this->conn, $sql);
        $checkLoginQuery = mysqli_num_rows($checkDatabaseQuery);

        if($checkLoginQuery == 1)
            return True;
        else
            return False;
    }

    public function getLoginUsername($email, $password)
    {
        // $sql = "SELECT username, password, email FROM users WHERE email='$email' AND password='$password'";
        $sql = "SELECT admin_users.email, admin_users.username, admin_passwords.password 
                FROM admin_users INNER JOIN admin_passwords 
                WHERE admin_users.email='$email' AND admin_passwords.password = '$password'";
        $checkDatabaseQuery = mysqli_query($this->conn, $sql);
        $checkLoginQuery = mysqli_num_rows($checkDatabaseQuery);

        if($checkLoginQuery == 1)
        {
            $row = mysqli_fetch_array($checkDatabaseQuery);
            $username = $row['username']; //sets $username equal to fetch_array username
        }

        return $username;
    }

    public function setSessionUsername($username)
    {
		$_SESSION['username'] = $username; //creates session variable equal to fetched $username
    }
    

    public function reactivateAccount($email)
    {
        $sql = "SELECT email,user_closed FROM admin_users WHERE email='$email' AND user_closed='yes'";
        $user_closed_query = mysqli_query($this->conn, $sql);
        if(mysqli_num_rows($user_closed_query) == 1)
        {
            $sql = "UPDATE admin_users SET user_closed='no' WHERE email='$email'";
            mysqli_query($this->conn, $sql);

            return ("The user account has been successfully reopened.<br>");
        }
    }


}



class RegisterAdminUser 
{
	private $user_obj;
	private $conn;

	public function __construct($conn)
	{
		$this->conn = $conn;
	}

    public function profilePic()
    {
        $rand = rand(1, 2); //PRNG between 1 and 2

        if ($rand = 1)
            $profile_pic = "assets/images/profile_pics/default/default_pic.jpg";
        else if ($rand = 2)
            $profile_pic = "assets/images/profile_pics/default/default_pic.jpg";

        return $profile_pic;
    }

    public function inviteCodeCheck($invite_code)
    {
        //Check invite code is valid
        $invite_query = mysqli_query($this->conn, "SELECT invite_code,used FROM invites WHERE invite_code='$invite_code' AND used='no'");
        if(mysqli_num_rows($invite_query))
        {
            // mysqli_query($this->conn, "UPDATE invites SET used='yes' WHERE invites.invite_code='$invite_code'");
            return true;
        }
        else
        {
            return false;
        }
    }

    public function generateUsername($fname, $lname)
    {
        //Generate username by concatenating first name and last name
        $username = strtolower($fname . "_" . $lname);
        $check_username_query = mysqli_query($this->conn, "SELECT username FROM users WHERE username='$username'");

        $i = 0;
        //if username exists add number to username
        while(mysqli_num_rows($check_username_query) != 0)
        {
            $i++; //Increment $i
            $username = $username . "_" . $i;
            $check_username_query = mysqli_query($this->conn, "SELECT username FROM users WHERE username='$username'");
        }

        return $username;
    }

    public function checkEmailsMatch($em, $em2)
    {
        if ($em == $em2)
            return True;
        else
            return False;
    }

    public function checkEmailFormat($em)
    {
        if(filter_var($em, FILTER_VALIDATE_EMAIL))
            return True;
        else
            return False;
    }

    public function checkEmailExists($em)
    {
        $query = "SELECT email FROM users WHERE email='$em'";
        $e_check = mysqli_query($this->conn, $query);

        //Count number of rows returned
        $num_rows = mysqli_num_rows($e_check);

        if($num_rows > 0)
            return True;
    }

    public function checkPasswordsMatch($password, $password2)
    {
        if ($password == $password2)
            return True;
        else
            return False;
    }

    public function checkPasswordContains($password)
    {
        /*
        * preg_match returns False if specified characters are found
        * returns True if unspecified characters are found
        */

        $characterCheck = preg_match('/[^A-Za-z0-9]/', $password);

        if($characterCheck)
            return True; //Contains unallowed characters
    }

    public function checkPasswordLength($password)
    {
        if(strlen($password > 64 || strlen($password) < 8))
            return True;
    }

    public function checkFirstnameLength($fname)
    {
        if(strlen($fname) > 50 || strlen($fname) < 2)
            return True;
    }

    public function checkLastnameLength($lname)
    {
        if(strlen($lname) > 50 || strlen($lname) < 2)
            return True;
    }
    
    public function submitRegisterQuery($fname, $lname, $username, $em, $password, $date, $profile_pic)
    {
        $sql = "INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `signup_date`, `profile_pic`, `num_posts`, `num_likes`, `user_closed`, `friend_array`) VALUES (NULL, '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')";
		$query = mysqli_query($this->conn, $sql);
		// echo $sql; //debugging
        //Consider a try-catch for the query
    }
}

class AdminPassword
{
	private $password_obj;
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
		$password_obj = new Salt($conn);
		//PBKDF2 Hash Function
		if($email != "")
			$salt = $this->getSalt($email);
		$iterations = 100000;
		$length = 32;

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

	public function getUsernameFromEmail($email)
	{
		$queryUsers = mysqli_query($this->conn, "SELECT username email FROM users WHERE email='$email'");
		$row = mysqli_fetch_array($queryUsers);
		$getUsername = $row['0'];
		return $getUsername;
	}

	private function getSalt($email)
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
		$password_obj = new Salt($conn);
		//Not needed as both ($salt, $username) are generated on server-side
		//$salt = mysqli_escape_string($this->conn, $salt);
		//$username = mysqli_escape_string($this->conn, $username);

		$sql = "INSERT INTO `salts` (`id`, `username`, `salt`) VALUES (NULL, '$username', '$salt')";
		$query = mysqli_query($this->conn, $sql);
	}

	public function checkIfSaltExists($username)
	{
		return 1;
	}
}

?>