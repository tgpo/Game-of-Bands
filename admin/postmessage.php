<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}
?>

<h1>Post as /u/GameofBands</h1>
<form method="post" action="admin_process.php">
	
	<label>Title</label><br />
	<input type="text" name="title" />
	<br />
	
	<label>Link</label><br />
	<span class="note"><strong>Note:</strong> If you include a link your message will be ignored.</span><br />
	<input type="text" name="link" />
	<br />

	<label>Message</label><br />
	<textarea rows="25" cols="50" name="message"></textarea>
	<br />	
	
	<input type="submit" value="Post" name="postmessage">
</form>
