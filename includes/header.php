<?php 
require_once("config/config.php"); 
require("includes/classes/User.php");
require("includes/classes/Post.php");
require("includes/classes/Message.php");
require("includes/classes/Notification.php");
require("includes/classes/Salt.php");
require("includes/classes/UserService.php");

if(!isset($_SESSION['username']))
	header("Location: register.php");

$userLoggedIn = $_SESSION['username'];

$message_obj= new Message($conn, $userLoggedIn);
$notification_obj = new Notification($conn, $userLoggedIn);
$user_obj = new User($conn, $userLoggedIn);

$user = $user_obj->fetchUserDetails();

$firstname = $user['first_name'];
$profilePic = $user['profile_pic'];

include("includes/handlers/ajax_update_title.php");
?>
<script>
//<!-- Preparation for AJAX update title -- called above -->
// $(function()
// {
// 	$.ajax(
// 	{
// 		url: "includes/handlers/ajax_update_title.php",
// 		type: "POST",
// 		data: title,
// 		cache:false,

// 		success: function(response) 
// 		{
			
// 		}
// 	});
// });
</script>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon/logo_transparent.png">

	<!-- JavaScript Local -->
	<!-- 
	<script src="assets/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="assets/js/bootstrap/3.3.2/bootstrap.js"></script>
	<script src="assets/js/bootbox/4.4.0/bootbox.min.js"></script>
	<script src="assets/js/jQuery.jCrop/0.9.12/jquery.Jcrop.js"></script> -->
	<!-- Local -->
	<script src="assets/js/jcrop_bits.js"></script>
	<script src="assets/js/valhalla2.main.js"></script>

	<!-- CSS Local -->
	<!-- 
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap/3.0.2/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="assets/css/font-awesome-4.7.css">
	<link rel="stylesheet" href="assets/css/jQuery.jCrop/0.9.12/jquery.Jcrop.css" type="text/css" /> 
	-->
	<!-- Local -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/style_mobile.css">

<!-- CDNs/SRI -->
	<!-- JavaScript CDN/SRI -->
	<script src="https://code.jquery.com/jquery-2.1.3.min.js" integrity="sha256-ivk71nXhz9nsyFDoYoGf2sbjrR9ddh+XDkCcfZxjvcM=" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js" integrity="sha384-4Kp4aQ6UNeqsJ/ithPcxYnnIGt/QJJ64J9QtfDAJZUTaePAIPm9aaBdu7Gw84oGs" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js" integrity="sha512-8NOmlkzoskIBni4iy5onHC57Mndt17mZgWkYJvxe5jwBJu3spYIRSjTkYJ9OLNS9Min+bsSqbDfGaoejWxyFiw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.12/js/jquery.Jcrop.js" integrity="sha512-llCA7D4bXVU97t9Sm/Q4qpnOHqcfJW8iAn06y0PeVxWOBE/L/tj5Cd+gQYEbDFjUC8gz9B/KQOZE+J11myl93Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<!-- CSS CDN/SRI -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Tfj13fqQQqqzQFuaZ81WDzmmOU610WeS08VMuHmElK5oI2f7NwojuL6VupYXR/jK" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.12/css/jquery.Jcrop.css" integrity="sha512-FrVwldDw9zOnYSTLWjvimJIDizKDw0nZ1NmJ+MDgdLCvckhdYgunUUMZInklNQ48Y3yBhxSdpHfaW44H/NNYpQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
	<div class="top_bar">
		<div class="logo">
			<a href="index.php"><img src="assets/images/favicon/logo_transparent.png"> Valhalla 2.0</a>
		</div>
		<div class="search">
			<form action="search.php" method="GET" name="search_form">
				<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">

				<div class="button_holder">
					<img src="assets/images/icons/Magnify-2.2s-64px-Animated.svg">
				</div>
			</form>

			<div class="search_results">
			</div>
			<div class="search_results_footer_empty">
			</div>
		</div>
		<nav>
			<a href="/"><i class="fa fa-home" aria-hidden="true"></i></a>
			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
				<i class="fa fa-envelope-open-o" aria-hidden="true"></i><span class="dropdown_data_window_message"><?php 
					if($numMessages > 0 && $numMessages <= 99)
						echo '<span class="notification_badge" id="unread_message">' . $numMessages . '</span>';
					else if($numMessages > 99)
						echo '<span class="notification_badge" id="unread_message">' . "99+" . '</span>';
			?></span><span class="dropdown_data_window_message_empty"></span></a>
			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
				<i class="fa fa-bell-o" aria-hidden="true"></i><span class="dropdown_data_window_notification"><?php
					if($numNotifications > 0 && $numNotifications <= 99)
						echo '<span class="notification_badge" id="unread_notification">' . $numNotifications . '</span>';
					else if($numNotifications > 99)
						echo '<span class="notification_badge" id="unread_notification">' . "99+" . '</span>';
			?></span><span class="dropdown_data_window_notification_empty"></span></a>
			<a href="friend_requests.php">
				<i class="fa fa-users" aria-hidden="true"></i><?php
					if($numRequests > 0 && $numRequests <= 99)
						echo '<span class="notification_badge" id="unread_requests">' . $numRequests . '</span>';
					else if($numRequests > 99)
						echo '<span class="notification_badge" id="unread_requests">' . "99+" . '</span>';
			?></a>
			
			<!--Add a dropdown menu for username with account_settings/logout/etc... -->
			<span class="username">
				<button>
					<img src="<?php if(isset($user)) echo $profilePic; ?>">
					<!-- <i class="fa fa-user-circle-o" aria-hidden="true"></i> -->
					<?php if(isset($user)) echo $firstname; ?>
					<i class="fa fa-caret-down" aria-hidden="true"></i>
				</button>
				<div class="dropdown-content">
					<a href="settings.php"><i class="fa fa-cog" aria-hidden="true"> Settings</i></a><br>
					<a href="includes/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a><br>
				</div>
			</span>
		</nav>

		<div class="dropdown_data_window" style="height: 0px; border:none;">
			<input type="hidden" id="dropdown_data_type" value="">
		</div>
	</div>
	<!-- JS Responsible for infinite scrolling -->
	<!-- Infinfite scrolling for notification dropdown menu -->
	<script>
		var userLoggedIn = '<?php echo $userLoggedIn ?>';

		$(document).ready(function()
		{	
			$('.dropdown_data_window').scroll(function() 
			{
				var inner_height = $('.dropdown_data_window').innerHeight(); //Div containing data
				var scroll_top = $('.dropdown_data_window').scrollTop();
				var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
				var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

				if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false')
				{
					var pageName; //Holds name of page to send ajax request to
					var type = $('#dropdown_data_type').val();

					if(type == 'notification')
					{
						pageName = "ajax_load_notifications.php";
					}
					else if(type = 'message')
					{
						pageName = "ajax_load_messages.php"
					}

					var ajaxReq = $.ajax(
					{
						url: "includes/handlers/" + pageName,
						type: "POST",
						data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
						cache:false,

						success: function(response) {
							$('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextPageDropdownData 
							$('.dropdown_data_window').find('.noMoreDropdownData').remove(); //Removes current .noMoreDropdownData 

							$('.dropdown_data_window').append(response);
						}
					});

				} //End if 

				return false;

			}); //End (window).scroll(function())

		});
	</script>
	<div class="wrapper"> <!-- wrapper <div> is closed in index.php -->