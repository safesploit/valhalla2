<?php
// require("../../config/config.php");
include("../../config/config.php");

include("../classes/User.php");
include("../classes/Message.php");

$limit = 7; //Number of messages to load

$message = new Message($conn, $_REQUEST['userLoggedIn']);
echo $message->getConvosDropdown($_REQUEST, $limit);
?>