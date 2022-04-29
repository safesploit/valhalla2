<?php
	//Register form values AND sanitise be database

	//First name
	$fname = strip_tags($_POST['reg_fname']); //Remove HTML tags
	$fname = str_replace(' ', '', $fname); //replaces spaces with empty char ' ' --> ''
	$fname = ucfirst(strtolower($fname)); //Changes all chars to lowercase, except first
	$_SESSION['reg_fname'] = $fname; //Stores first name into session variable

	//Last name
	$lname = strip_tags($_POST['reg_lname']); //Remove HTML tags
	$lname = str_replace(' ', '', $lname); //replaces spaces with empty char ' ' --> ''
	$lname = ucfirst(strtolower($lname)); //Changes all chars to lowercase, except first
	$_SESSION['reg_lname'] = $lname; //Stores last name into session variable


	//email
	$em = strip_tags($_POST['reg_em']); //Remove HTML tags
	$em2 = strip_tags($_POST['reg_em2']); //Remove HTML tags
	$_SESSION['reg_em'] = $em; //Stores em into session variable
	$_SESSION['reg_em2'] = $em2; //Stores em2 into session variable


	//password
	$password = strip_tags($_POST['reg_password']); //Remove HTML tags
	$password2 = strip_tags($_POST['reg_password2']); //Remove HTML tags
	$_SESSION['reg_password'] = $password; //Stores password into session variable
	$_SESSION['reg_password2'] = $password2; //Stores password2 into session variable


	//date
	$date = date("Y-m-d"); //Current date

	//invite_code
	$invite_code = strip_tags($_POST['reg_invite_code']); //Remove HTML tags
	$_SESSION['reg_invite_code'] = $invite_code; //Stores invite_code into session variable

?>