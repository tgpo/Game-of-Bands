<?php
include 'header.php';

loggedin_check();
?>

<h1>User Dashboard</h1>

<form method="post" action="user_process.php">
	<button type="submit" value="Leave Song" name="submitSongPage">Submit Song</button>
</form>

<?php
include 'footer.php';
?>