<?php
session_start();

if(isset($_POST['reset_password_button']))
{
    $reset_password_obj = new Password($conn, $username);
}
?>