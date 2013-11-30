<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}
?>

<h1>Add New Team</h1>
<form method="post" action="admin_process.php">
	<label>Round</label>
	<input type="text" name="round" />
	<br />
	
	<label>Team Number</label>
	<input type="text" name="teamnumber" />
	<br />
	
	<label>Music Bandit</label>
	<input type="text" name="musician" />
	<br />
	
	<label>Lyrics Bandit</label>
	<input type="text" name="lyricist" />
	<br />
	
	<label>Vocals Bandit</label>
	<input type="text" name="vocalist" />
	<br />	
	
	<input type="submit" value="Add Team" name="addTeam">
</form>
