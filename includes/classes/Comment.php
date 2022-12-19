<?php
class Comment
{
	private $user_obj;
	private $conn;

	public function __construct($conn, $user)
	{
		$this->conn = $conn;
		$this->user_obj = new User($conn, $user);
	}

    /*
    Load comments
    */
    public function loadComments($getComments)
	{
		$userLoggedIn = $this->user_obj->getUsername();
		$user_obj = new User($this->conn, $userLoggedIn);

		while($comment = mysqli_fetch_array($getComments))
		{
			$commentId = $comment['id'];
			$commentBody = $comment['post_body'];
			$postedTo = $comment['posted_to'];
			$postedBy = $comment['posted_by'];
			$dateAdded = $comment['date_added'];
			$removed = $comment['removed'];

			$fullname = $user_obj->getFirstAndLastName();
			$profilePic = $user_obj->getProfilePic();

			$interval = $this->calculateTimeframeInterval($dateAdded);
			$timeMessage = $this->timeframeDiffLong($interval);

			$commentsFrameHtml .= "
			<div class='comment_section' id='$commentId'>
				<a href='$postedBy' target='_parent'>
					<img src='$profilePic' title='$postedBy ?>' style='float:left;' height='30'>
				</a>
				<a href='$postedBy' target='_parent'>
					<b>$fullname</b>
				</a>
				&nbsp;&nbsp;&nbsp;&nbsp; 
                $timeMessage
				<br> 
					$commentBody
				<hr>
			</div>
			";
            //Consider button for $timeMessage, where clicked displays $dateAdded (full date).

		}
		
		return $commentsFrameHtml;
	}

	public function loadCommentQuery($postId)
	{
		$getComments = mysqli_query($this->conn, "SELECT * FROM comments WHERE post_id='$postId' ORDER BY id ASC");
		return $getComments;
	}

	public function numComments($getCommentsQuery)
	{
		$count = mysqli_num_rows($getCommentsQuery);
		return $count;
	}

	public function noComments()
	{
		$commentsFrameHtml = "<center><br><br>No Comments to Show!</center>";
		return $commentsFrameHtml;
	}

    /*
    Submit comment
    */
    public function getPostId($postId)
	{
		$query = mysqli_query($this->conn, "SELECT added_by, user_to FROM posts WHERE id='$postId'");
		$row = mysqli_fetch_array($query);

		$postedTo = $row['added_by'];

		return $postedTo;
	}

	
    
	public function preparePostBody($postBody)
	{
		$postBody = strip_tags($postBody); //removes HTML tags
		$postBody = str_replace('\r\n', '\n', $postBody);
		$postBody = nl2br($postBody);
		//END line break code
		$postBody = mysqli_escape_string($this->conn, $postBody);

		return $postBody;
	}

	public function CheckIfEmptyPost($postBody)
	{
		$check = preg_replace('/\s+/', '', $postBody); //removes all spaces
		
		if($check_empty != "")
			return false;
		else
			return true;
	}

	public function submitComment($postBody, $userLoggedIn, $postedTo, $postId)
	{
		$dateTimeNow = date("Y-m-d H:i:s");
		$sql = "INSERT INTO `comments` (`id`, `post_body`, `posted_by`, `posted_to`, `date_added`, `removed`, `post_id`) VALUES (NULL, '$postBody', '$userLoggedIn', '$postedTo', '$dateTimeNow', 'no', '$postId')";
		$insertPost = mysqli_query($this->conn, $sql);

		return $insertPost;
	}

	public function getCommentersQuery($postId)
	{
		$getCommenters = mysqli_query($this->conn, "SELECT * FROM comments WHERE post_id='$postId'");
		return $getCommenters;
	}

	public function getCommenters($postedBy, $postId, $notifiedUsers)
	{
		
	}


    /*
    Timeframe
    */
	public function calculateTimeframeInterval($dateAdded)
	{
		$dateTimeNow = date("Y-m-d H:i:s");
		$startDate = new DateTime($dateAdded);
		$endDate = new DateTime($dateTimeNow);
		$interval = $startDate->diff($endDate); 

		return $interval;
	}

	public function timeframeDiffLong($interval)
	{
		if($interval->y >=1) //less than a year ago
		{
			if($interval->y == 1)
				$time_message = $interval->y . " year ago";
			else if ($interval->y > 1)
				$time_message = $interval->y . " years ago";
		}
		else if ($interval->m >= 1) //less than a year ago
		{
			if($interval->d == 0) //how many days old
				$days = " ago";
			else if ($interval->d == 1)
				$days = $interval->d . " day ago";
			else if ($interval->d > 1)
				$days = $interval->d . " days ago";
		}
		if($interval->m == 1) //how many months old
			$time_message = $interval->m . " month " . $days;
		else if ($interval->m > 1)
			$time_message = $interval->m . " months " . $days;
		else if ($interval->d >= 1) //at least a day old
		{
			if($interval->d == 1)
				$time_message = "Yesterday";
			else if ($interval->d > 1)
				$time_message = $interval->d . " days ago";
		}
		else if ($interval->h >= 1) //less than a day
		{
			if($interval->h == 1)
				$time_message = $interval->h . " hour ago";
			else if ($interval->h > 1)
				$time_message = $interval->h . " hours ago";
		}
		else if ($interval->i >= 1) //less than an hour
		{
			if($interval->i == 1)
				$time_message = $interval->i . " minute ago";
			else if ($interval->i > 1)
				$time_message = $interval->i . " minutes ago";
		}
		else if ($interval->s >= 0) //less than a minute
		{
			if($interval->s < 30)
				$time_message = "Just now"; //less than 30 seconds
			else if ($interval->s > 1)
				$time_message = $interval->s . " seconds ago";
		}

		return $time_message;
	}

    public function timeframeDiffShort($interval)
	{
		if($interval->y >=1) //less than a year ago
		{
			if($interval->y == 1)
				$time_message = $interval->y . "y";
			else if ($interval->y > 1)
				$time_message = $interval->y . "y";
		}
		else if ($interval->m >= 1) //less than a year ago
		{
			if($interval->d == 0) //how many days old
				$days = "";
			else if ($interval->d == 1)
				$days = $interval->d . "";
			else if ($interval->d > 1)
				$days = $interval->d . "";
		}
		if($interval->m == 1) //how many months old
			$time_message = $interval->m . "m " . $days;
		else if ($interval->m > 1)
			$time_message = $interval->m . "m " . $days;
		else if ($interval->d >= 1) //at least a day old
		{
			if($interval->d == 1)
				$time_message = "1d";
			else if ($interval->d > 1)
				$time_message = $interval->d . "d";
		}
		else if ($interval->h >= 1) //less than a day
		{
			if($interval->h == 1)
				$time_message = $interval->h . "hr";
			else if ($interval->h > 1)
				$time_message = $interval->h . "hr";
		}
		else if ($interval->i >= 1) //less than an hour
		{
			if($interval->i == 1)
				$time_message = $interval->i . "min";
			else if ($interval->i > 1)
				$time_message = $interval->i . "min";
		}
		else if ($interval->s >= 0) //less than a minute
		{
			if($interval->s < 30)
				$time_message = "Just now"; //less than 30 seconds
			else if ($interval->s > 1)
				$time_message = $interval->s . "sec";
		}

		return $time_message;
	}
	
}
?>