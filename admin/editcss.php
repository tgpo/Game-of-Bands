<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once('../lib/reddit.php');
$reddit = new reddit($reddit_user, $reddit_password);

$strStylesheet = $reddit->getStylesheet('gameofbands');
$regex = '/(.author\[href\$="\/tgpo.*})/';
preg_match_all($regex, $strStylesheet,$authorCSS);
$cssARRAY = array_filter($authorCSS[0], function ($var) { return (stripos($var, 'epoch') === false); });


?>

<h1>Edit CSS</h1>
<form method="post" action="admin_process.php">
	<textarea rows="30" cols="150" name="stylesheet"><?php print_r($cssARRAY); ?></textarea>

	
	<input type="submit" value="Edit Stylesheet" name="editstylesheet">
</form>
