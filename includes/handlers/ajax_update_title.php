<?php
// require("../../config/config.php"); //Causes error 500
include("../../config/config.php");

include("../classes/User.php");

$user_obj = new User($conn, $userLoggedIn);
$title = $user_obj->titleCreator();
?>