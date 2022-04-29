<form class="post_form" action="test.php" method="POST" enctype="multipart/form-data">
			<input type="file" name="fileToUpload" id="fileToUpload"><br><br>
			<input type="submit" name="post" id="post_button" value="Post">
</form>

<?php 
include("includes/handlers/test.php");
?>