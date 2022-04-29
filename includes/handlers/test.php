<?php
//THIS FILE IS FOR TESTING FILE UPLOADS
//IT CAN BE DELETED ONCE CONFIRMED WORKING
//CODE CONFIRMED WORKING FROM DOCUMENT_ROOT //So a directory issue

	$uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";

	if($imageName != "") 
	{
		$targetDir = "assets/images/posts/";
		$imageName = $targetDir . uniqid() . "_" . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if($_FILES['fileToUpload']['size'] > 10000000) 
		{
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") 
		{
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			$uploadOk = 0;
		}

		if($uploadOk) 
		{
			$uploadDir =  __DIR__ . "/../../" . $imageName;
			if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadDir)) 
			{
				//echo "image uploaded okay";
				echo "NAME OF UPLOADED IMAGE: " . $imageName;
			}
			else 
			{
				echo "FAILED AT: move_uploaded_file() function";
				$uploadOk = 0;
			}
		}

	}
	else 
	{
		echo "NO UPLOAD";
	}

	if($uploadOk) 
	{
		echo '<br><br><h4>' . "submitPost() function is working" . '</h4><br><br>';
		//$post = new Post($conn, $_POST['user_from']);
		//$post->submitPost($_POST['post_body'], $_POST['user_to'], $imageName);
	}
	else 
	{
		echo "<div style='text-align:center;' class='alert alert-danger'>
				$errorMessage
			</div>";
	}



/* below is FOR TESTING OUTPUT IS WORKING */
//echo '<br><br>' . "PWD:  " . "" . dirname(__FILE__) . "" . '<br><br><hr>';
echo '<hr>' . $uploadDir . '<br><br>';
echo "<img src='$imageName'>";

?>