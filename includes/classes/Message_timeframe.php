<?php
//Timeframe
$date_time_now = date("Y-m-d H:i:s");
$start_date = new DateTime($row['date']); //Time of post
$end_date = new DateTime($date_time_now); //Current time
$interval = $start_date->diff($end_date); //Difference between dates

if($interval->y >=1)
{
	if($interval->y == 1)
		$time_message = $interval->y . " y"; //1 year ago
	else if ($interval->y > 1)
		$time_message = $interval->y . " y"; //1++ years ago
}
else if ($interval->m >= 1) //if message is less than a year ago
{
	if($interval->d == 0) //check how many days old
		$days = " ago";
	else if ($interval->d == 1)
		$days = $interval->d . " d";
	else if ($interval->d > 1)
		$days = $interval->d . " d";
else if($interval->m == 1) //check how many months old
	$time_message = $interval->m . " m" . $days;
else if ($interval->m > 1)
	$time_message = $interval->m . " m" . $days;

}
else if ($interval->d >= 1) //at least a day old
{
	if($interval->d == 1)
		$time_message = "Yesterday";
	else if ($interval->d > 1)
		$time_message = $interval->d . " d";
}
else if ($interval->h >= 1) //if post is less than a day ago
{
	if($interval->h == 1)
		$time_message = $interval->h . " h";
	else if ($interval->h > 1)
		$time_message = $interval->h . " h";
}
else if ($interval->i >= 1) //if post is less than an hour ago
{
	if($interval->i == 1)
		$time_message = $interval->i . " min";
	else if ($interval->i > 1)
		$time_message = $interval->i . " min";
}
else if ($interval->s >= 0) //if post is less than a minute ago
{
	if($interval->s < 30)
		$time_message = "Just now"; //if secons if < 30
	else if ($interval->s > 1)
		$time_message = $interval->s . " sec";
}
?>
