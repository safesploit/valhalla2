<?php
$server = "127.0.0.1";
$user = "valhalla";
$pass = "PASSWORD_HERE";
$database = "valhalla2";

ob_start(); //Turns on output buffering
session_start();

$timezone = date_default_timezone_set("Europe/London"); //Timezone information for session/log data

$conn = mysqli_connect($server, $user, $pass, $database);

//Checks if mysqli_connect() was successful
if(mysqli_connect_errno())
{
	print("Failed to connect: " . mysqli_connect_errno());
}

// $dbname = "valhalla2";
// // $dbhost = "192.168.5.102";
// $dbhost = "127.0.0.1";
// $dbuser = "valhalla";
// $dbpass = "PASSWORD_HERE";

// try 
// {
// 	$conn = new PDO("mysql:dbname=$dbname;host=$dbhost", "$dbuser", "$dbpass");
// 	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
// }
// catch(PDOExeption $e) 
// {
// 	echo "Connection failed: " . $e->getMessage();
// }

?>