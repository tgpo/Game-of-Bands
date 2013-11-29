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

$result = mysql_query("SELECT * FROM rounds WHERE number='$id' ") or die(mysql_error()); 
$round = mysql_fetch_array($result);
?>

<h1>Edit Round <?php echo $round['number']; ?></h1>
<form method="post" action="admin_process.php">
	<input type="hidden" name="id" value="<?=$id ?>">

	<label>Theme</label>
	<input type="text" name="theme" value="<?php echo $round['theme']; ?>" />
	<br />
	
	<hr />

	<h3>Admin Settings</h3>
	<label>Signup Post ID</label>
	<input type="text" name="signupID" value="<?php echo $round['signupID']; ?>" />
	<br />
	<label>Muscian Signup Comment ID</label>
	<input type="text" name="musiciansSignupID" value="<?php echo $round['musiciansSignupID']; ?>" />
	<br />
	<label>Lyricist Signup Comment ID</label>
	<input type="text" name="lyricistsSignupID" value="<?php echo $round['lyricistsSignupID']; ?>" />
	<br />
	<label>Vocalist Signup Comment ID</label>
	<input type="text" name="vocalistSignupID" value="<?php echo $round['vocalistSignupID']; ?>" />
	<br />
	<label>Consolidation Post ID</label>
	<input type="text" name="consolidationID" value="<?php echo $round['consolidationID']; ?>" />
	<br />
	<label>Theme Voting Post ID</label>
	<input type="text" name="themeID" value="<?php echo $round['themeID']; ?>" />
	<br />
	<label>Song Voting Post ID</label>
	<input type="text" name="songvotingthreadID" value="<?php echo $round['songvotingthreadID']; ?>" />
	<br />

	<hr />
	
	<label>Delete Round</label>
	<input type="checkbox" name="delete_round" value="Yes" />
	<br /><br />
	
	<input type="submit" value="Edit Round" name="editRound">
</form>
