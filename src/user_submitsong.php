<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

loggedin_check('/login_request');

require_once('query.php');

$db       = database_connect();
$username = $_SESSION['GOB']['name'];

$query = $db->query('SELECT * FROM rounds WHERE theme IS NOT NULL AND theme <> "NULL" order by number desc limit 1');
$round = $query->fetch();
$currentround = $round['number'];

$query = $db->prepare('SELECT * FROM songs WHERE submitby=:username and round=:currentround');
$query->execute(array('username' => $username, 'currentround' => $currentround));
$song  = $query->fetch();
 ?>

<script>
    $(document).ready(function(){
        $("#submitSong").validate({
            rules: {
                teamnumber: {
                    required: true,
                    number: true
                },
                url: {
                    required: true,
                    url: true
                }
            }
        });
    });
</script>

<h2>Submit Song for Round <?php echo $currentround; ?></h2>

<?php if (empty($song['name'])) { ?>

    <form id="submitSong" method="post" action="user_process.php">
        <input type="hidden" name="round" value="<?=$currentround ?>">
        
        <label>Team Number</label>
        <input type="text" id="teamnumber" name="teamnumber" value="" type="number" required/>
        <br />
        
        <label>Song Name</label>
        <input type="text" id="songname" name="songname" value="" required/>
        <br />
        
        <label>Song URL</label>
        <input type="text" id="url" name="url" value="" type="url" required/>
        <br />

        <label>Lyrics Bandit</label>
        <input type="text" id="lyrics" name="lyrics" value="" required/>
        <br />
        
        <label>Music Bandit</label>
        <input type="text" id="music" name="music" value="" required/>
        <br />
        
        <label>Vocals Bandit</label>
        <input type="text" id="vocals" name="vocals" value="" required/>
        <br />
        
        <label>Song Lyrics</label>
        <textarea rows="5" cols="20" id="lyricsheet" name="lyricsheet" required></textarea>
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