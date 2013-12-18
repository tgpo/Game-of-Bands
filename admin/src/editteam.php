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

$query = $db->prepare('SELECT * FROM teams WHERE id=:id');
$query->execute(array('id' => $id));
$team = $query->fetch();

?>

<h2>Edit Team</h2>
<form method="post" action="admin_process.php">
    <input type="hidden" name="id" value="<?php echo $team['id']; ?>">

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
