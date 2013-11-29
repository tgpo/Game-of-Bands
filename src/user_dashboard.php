<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}
loggedin_check();
?>

<h1>User Dashboard</h1>

<form method="post" action="/user_process.php">
	<button type="submit" value="Leave Song" name="submitSongPage">Submit Song</button>
</form>