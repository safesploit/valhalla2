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
?>