<?php
require_once( 'includes/gob_admin.php' );
require_once( 'includes/admin_header.php' );
require_once( '../src/secrets.php' );
mod_check();
?>
<?php
mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db("xxxdatabasexxx") or die(mysql_error());

$id=mysql_real_escape_string($_GET['id']);

$result = mysql_query("SELECT * FROM songs WHERE id='$id' ") or die(mysql_error()); 
$song = mysql_fetch_array($result); ?>

<h1>Edit Song</h1>
<form method="post" action="admin_process.php">
	<input type="hidden" name="id" value="<?=$id ?>">

	<label>Round</label>
	<input type="text" name="round" value="<?php echo $song['round']; ?>" />
	<br />
	
	<label>Song Name</label>
	<input type="text" name="name" value="<?php echo $song['name']; ?>" />
	<br />
	
	<label>Song URL</label>
	<input type="text" name="url" value="<?php echo $song['url']; ?>" />
	<br />
	
	<label>Lyrics</label>
	<input type="text" name="lyrics" value="<?php echo $song['lyrics']; ?>" />
	<br />
	
	<label>Music</label>
	<input type="text" name="music" value="<?php echo $song['music']; ?>" />
	<br />
	
	<label>Vocals</label>
	<input type="text" name="vocals" value="<?php echo $song['vocals']; ?>" />
	<br />
	
	<label>Song Lyrics</label>
	<textarea rows="5" cols="20" name="lyricsheet"><?php echo $song['lyricsheet']; ?></textarea>
	<br />
	
	<label>Votes - Song</label>
	<input type="text" name="votes" value="<?php echo $song['votes']; ?>" />
	<br />
	
	<label>Votes - Lyrics</label>
	<input type="text" name="lyricsvote" value="<?php echo $song['lyricsvote']; ?>" />
	<br />
	
	<label>Votes - Music</label>
	<input type="text" name="musicvote" value="<?php echo $song['musicvote']; ?>" />
	<br />
	
	<label>Votes - Vocals</label>
	<input type="text" name="vocalsvote" value="<?php echo $song['vocalsvote']; ?>" />
	<br />
	
	<label>Winner</label>
	<input type="checkbox" name="winner" value="Yes" <?php if($song['winner']) echo "checked"; ?> />
	<br />
	
	<hr />
	
	<label>Delete Song</label>
	<input type="checkbox" name="delete_song" value="Yes" />
	<br /><br />
	
	<input type="submit" value="Edit Song" name="editSong">
</form>
<?php
require_once( 'includes/admin_footer.php' );
?>