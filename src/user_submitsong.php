<?php
loggedin_check('/login_request');

require_once('query.php');
$db       = database_connect();
$username = $_SESSION['GOB']['name'];

$query = $db->query('SELECT * FROM rounds order by number desc limit 1');
$round = $query->fetch();
$currentround = $round['number'];

$query = $db->prepare('SELECT * FROM songs WHERE submitby=:username and round=:currentround');
$query->execute(array('username' => $username, 'currentround' => $currentround));
$song  = $query->fetch();
 ?>

<h2>Submit Song for Round <?php echo $currentround; ?></h2>

<?php if (empty($song['name'])) { ?>

	<form method="post" action="user_process.php">
		<input type="hidden" name="round" value="<?=$currentround ?>">
		
		<label>Team Number</label>
		<input type="text" name="teamnumber" value="" />
		<br />
		
		<label>Song Name</label>
		<input type="text" name="songname" value="" />
		<br />
		
		<label>Song URL</label>
		<input type="text" name="url" value="" />
		<br />

		<label>Lyrics Bandit</label>
		<input type="text" name="lyrics" value="" />
		<br />
		
		<label>Music Bandit</label>
		<input type="text" name="music" value="" />
		<br />
		
		<label>Vocals Bandit</label>
		<input type="text" name="vocals" value="" />
		<br />
		
		<label>Song Lyrics</label>
		<textarea rows="5" cols="20" name="lyricsheet"></textarea>
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