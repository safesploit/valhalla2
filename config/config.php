<?php
$server = "";
$db_user = "";
$db_pass = "";
$db_table = "valhalla2";

ob_start(); //Turns on output buffering
session_start();

$timezone = date_default_timezone_set("Europe/London"); //Timezone information for session/log data

$conn = mysqli_connect($server, $db_user, $db_pass, $db_table);

//Checks if mysqli_connect() was successful
if(mysqli_connect_errno())
{
	print("Failed to connect: " . mysqli_connect_errno());
}
?>