<?php
include 'header.php';
require_once( 'includes/gob_user.php' );

loggedin_check();
?>

<h1>User Dashboard</h1>

<form method="post" action="user_process.php">
	<button type="submit" value="Leave Song" name="leaveTeam">Leave Team</button>
	
</form>

<?php
include 'footer.php';
?>