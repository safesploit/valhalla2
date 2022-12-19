<?php
// require("../../config/config.php");
include("../../config/config.php");

if(isset($_POST['id'])) 
{
	$id = $_POST['id'];

	$query = mysqli_query($conn, "DELETE FROM messages WHERE id='$id'");
}

?>