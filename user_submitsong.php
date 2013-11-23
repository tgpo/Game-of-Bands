<?php
include 'header.php';
loggedin_check('login_request');
require_once( 'src/secrets.php' );

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db('xxxdatabasexxx') or die(mysql_error());

$result = mysql_query("SELECT * FROM rounds order by number desc limit 1 ") or die(mysql_error()); 
$round = mysql_fetch_array($result);
$currentround = $round['number'];

$username = $_SESSION['GOB']['name'];
$result = mysql_query("SELECT * FROM songs where submitby='$username' AND round='$currentround' ") or die(mysql_error()); 
$song = mysql_fetch_array($result);
 ?>

<h1>Submit Song for Round <?php echo $currentround; ?></h1>

<?php if (empty($song['name'])) { ?>

	<form method="post" action="user_process.php">
		<input type="hidden" name="round" value="<?=$currentround ?>">
		
		<label>Team Number</label>
		<input type="text" name="teamnumber" value="<?php echo $song['teamnumber']; ?>" />
		<br />
		
		<label>Song Name</label>
		<input type="text" name="songname" value="<?php echo $song['name']; ?>" />
		<br />
		
		<label>Song URL</label>
		<input type="text" name="url" value="<?php echo $song['url']; ?>" />
		<br />

		<label>Lyrics Bandit</label>
		<input type="text" name="lyrics" value="<?php echo $song['lyrics']; ?>" />
		<br />
		
		<label>Music Bandit</label>
		<input type="text" name="music" value="<?php echo $song['music']; ?>" />
		<br />
		
		<label>Vocals Bandit</label>
		<input type="text" name="vocals" value="<?php echo $song['vocals']; ?>" />
		<br />
		
		<label>Song Lyrics</label>
		<textarea rows="5" cols="20" name="lyricsheet"><?php echo $song['lyricsheet']; ?></textarea>
		<br />	
		
		<input type="submit" value="Submit Song" name="submitSong">
	</form>
<?php
	} else {
?>
	<p><strong><?php echo $song['name']; ?></strong> has already been submitted for team <?php echo $song['teamnumber']; ?>. Best of luck this round!</p>
<?php
	}
?>

<?php
include 'footer.php';
?>