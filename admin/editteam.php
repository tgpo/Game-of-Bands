<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db($mysql_db) or die(mysql_error());

$id=mysql_real_escape_string($_GET['id']);

$result = mysql_query("SELECT * FROM teams WHERE id='$id' ") or die(mysql_error()); 
$team = mysql_fetch_array($result); ?>

<h1>Edit Team</h1>
<form method="post" action="admin_process.php">
	<input type="hidden" name="id" value="<?=$id ?>">

	<label>Round</label>
	<input type="text" name="round" value="<?php echo $team['round']; ?>" />
	<br />
	
	<label>Team Number</label>
	<input type="text" name="teamnumber" value="<?php echo $team['teamnumber']; ?>" />
	<br />
	
	<label>Music Bandit</label>
	<input type="text" name="musician" value="<?php echo $team['musician']; ?>" />
	<br />
	
	<label>Lyrics Bandit</label>
	<input type="text" name="lyricist" value="<?php echo $team['lyricist']; ?>" />
	<br />
	
	<label>Vocals Bandit</label>
	<input type="text" name="vocalist" value="<?php echo $team['vocalist']; ?>" />
	<br />
	
	<hr />
	
	<label>Delete Team</label>
	<input type="checkbox" name="delete_team" value="Yes" />
	<br /><br />
	
	<input type="submit" value="Edit Team" name="editTeam">
</form>
