<?php
//https://stackoverflow.com/questions/4707053/php-user-class-login-logout-signup

// class UserService
// {
//     protected $_email;    // using protected so they can be accessed
//     protected $_password; // and overidden if necessary

//     protected $_db;       // stores the database handler
//     protected $_user;     // stores the user data

//     public function __construct(PDO $db, $email, $password) 
//     {
//        $this->_db = $db;
//        $this->_email = $email;
//        $this->_password = $password;
//     }

//     public function login()
//     {
//         $user = $this->_checkCredentials();
//         if ($user) {
//             $this->_user = $user; // store it so it can be accessed later
//             $_SESSION['user_id'] = $user['id'];
//             return $user['id'];
//         }
//         return false;
//     }

//     protected function _checkCredentials()
//     {
//         $stmt = $this->_db->prepare('SELECT * FROM users WHERE email=?');
//         $stmt->execute(array($this->email));
//         if ($stmt->rowCount() > 0) {
//             $user = $stmt->fetch(PDO::FETCH_ASSOC);
//             $submitted_pass = sha1($user['salt'] . $this->_password);
//             if ($submitted_pass == $user['password']) {
//                 return $user;
//             }
//         }
//         return false;
//     }

//     public function getUser()
//     {
//         return $this->_user;
//     }
// }

class LoginUser
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

        $sql = "SELECT email,password FROM users WHERE email='$email' AND password='$password'";
        $check_database_query = mysqli_query($this->conn, $sql);
        $check_login_query = mysqli_num_rows($check_database_query);

        if($check_login_query == 1)
            return True;
        else
            return False;
    }

    public function getLoginUsername($email, $password)
    {
        $sql = "SELECT username, password, email FROM users WHERE email='$email' AND password='$password'";
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
        $sql = "SELECT email,user_closed FROM users WHERE email='$email' AND user_closed='yes'";
        $user_closed_query = mysqli_query($this->conn, $sql);
        if(mysqli_num_rows($user_closed_query) == 1)
        {
            $sql = "UPDATE users SET user_closed='no' WHERE email='$email'";
            mysqli_query($this->conn, $sql);

            return ("The user account has been successfully reopened.<br>");
        }
    }
}



class RegisterUser 
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

class Password
{
	private $user_password_obj;
	private $conn;

	public function __construct($conn)
	{
		$this->conn = $conn;
	}

    public function resetPassword($email)
    {
        $token = hash('sha256',date("Y-m-d H:i:s"));
        //$email = row['email'];
        //$valid_from = date("Y-m-d H:i:s");
        $expired = date('Y-m-d H:i:s', strtotime("+1 hour"));
        $url = 'https://valhalla.sws-internal/password_reset.php?token=';

        // (id, email, is_token_expired, is_token_used)
        //$query = mysqli($conn, "INSERT INTO reset_password VALUES(NULL, '$token', '$email', '$expired', 'no'));
        $reset_url = $url . $token;
        return $reset_url;
    }
}

class ErrorArray
{
    public function errEmailsNotEqual()
    {
        return "Emails do not match <br>";
    }

    public function errEamilInvalidFormat()
    {
        return "Invalid email format<br>";
    }
    public function errEmailAlreadyReg()
    {
        return "Email is already registered<br>";
    }

    public function errPasswordsNotEqual()
    {
        return "Passwords do not match <br>";
    }

    public function errPasswordCanOnlyContain()
    {
        return "Your password can only contain a-z, A-Z, 0-9 as characters <br>";
    }

    public function errPasswordLength()
    {
        return "Your password must be between between 8 and 64 characters <br>";
    }

    public function errFirstnameLength()
    {
        return "Your first name must be between 2 and 50 characters <br>";
    }

    public function errLastnameLength()
    {
        return "Your last name must be between 2 and 50 characters <br>";
    }

    public function errInviteCodeInvalid()
    {
        return "Your invite code is invalid or has been used <br>";
    }

    public function successProfileCreated()
    {
        return "<br><span style='color: green'>Profile successfully created. <br> You can now login!<br></span>";
    }

    public function errIncorrectLoginCreds()
    {
        return "Email or Password was incorrect!<br>";
    }

}



?>