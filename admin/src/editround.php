<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
$db = database_connect();

$id=$_GET['id']; //TODO: Sanitize

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
    <br />
    <label>Announce Start Post ID</label>
    <input type="text" name="announceID" value="<?php echo $round['announceID']; ?>" />
    <br />
    <label>Song Voting Post ID</label>
    <input type="text" name="songvotingthreadID" value="<?php echo $round['songvotingthreadID']; ?>" />
    <br />
    <label>Congrats Post ID</label>
    <input type="text" name="congratsID" value="<?php echo $round['congratsID']; ?>" />
    <br />

    <hr />
    
    <label>Delete Round</label>
    <input type="checkbox" name="delete_round" value="Yes" />
    <br />
    <label>Start Round<?php 
    $started='';
    if(has_round_started($round)){
    	$started='1'; 
    	echo ' Was started on: ' . format_date($round['start']);
    }?></label>
    <input type="checkbox" name="start_round" value="<?php echo $started;?>"/>
    <br />
    <label>End Round<?php 
    $ended='';$checked='';
    if(has_round_ended($round)){
		$ended='1'; 
		echo ' Was ended on: ' . format_date($round['end']);
	}?></label>
    <input type="checkbox" name="end_round" value="<?php echo $ended;?>" />
    <br /><br />
    
    <input type="submit" value="Edit Round" name="editRound">
</form>
