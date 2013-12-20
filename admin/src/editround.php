<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
$db = database_connect();

$id=$_GET['id'];

$query = $db->prepare('SELECT * FROM rounds WHERE number=:id');
$query->execute(array('id' => $id));
$round = $query->fetch();


?>

<h2>Edit Round <?php echo $round['number']; ?></h2>
<form method="post" action="src/game/editRoundProcess.php">
    <input type="hidden" name="id" value="<?php echo $round['number']; ?>">

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
