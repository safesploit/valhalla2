<?php
/**
 * 	 	The purpose for this file is for to be placed in the <title> <?php include("includes/title.php"); ?> </title>
 *	 	
 * 		title.php is a dynamic title which will implement JavaScript code for unread messages.
 * 
 * 		Similiar to how YouTube has (x) for the number of notifications you have in the title.
 * 
 * 		This should be placed in the existing title tag for index.php as follows
 * 
 * 
 * 		<title><?php include("includes/title.php"); ?>Valhalla 2.0</title>
 * 		
 * 
 * 
 * 		The above will then for the index.php title when 2 unreads messages are in notifications display as:
 * 			"(2) Valhalla 2.0"
 * 
 * 
 * 		An AJAX call through JavaScript will also need to be called to ensure this notification updates in the background
 * 		otherwise the webpage will need to be refreshed for the $num_messages count in the title to update.
 * 		Refer to: /assets/js/title.js
 * 
 * 		https://stackoverflow.com/questions/4542863/jquery-ajax-call-with-timer
 * 
**/
require '../config/config.php';
include("classes/Message.php");

//Unread messages 
$messages = new Message($conn, $userLoggedIn);
$num_messages = $messages->getUnreadNumber();

//Display unread messages in <title>
if($num_messages > 0)
	echo '&#40;' . $num_messages . '&#41;' . ' ';
	//echo '<title>&#40;' . $num_messages . '&#41;</title>'; //CAN BE DELETED FOR TESTING PURPOSES
?>