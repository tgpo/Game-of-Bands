<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
$db    = database_connect();

$query = $db->query('SELECT * FROM rounds order by number desc limit 1');
$round = $query->fetch();
$currentround = $round['number'];

?>

<script>
$(document).ready(function(){
  $('#adminform').submit(function() {
    var c = confirm("You are about to post to reddit and change the website database!\n\n You sure you want to do that?");
    return c; 
  });
});
</script>
<form id="adminform" method="post" action="admin_process.php">
  <div class="box left">
    <h2>Sunday</h2>
    <div class="box left">
      <h2>1. Post Song Voting Thread for Round <?php echo $currentround; ?></h2>
      <p>Posts the voting thread for all submitted songs.</p>
      <br />
      <button type="submit" value="Post Song Voting Thread" name="postvote">Post Voting Thread</button>
      <button type="submit" value="Post Late Comers" name="postlate">Post Late Comers</button>
    </div>
    <div class="box left">
      <h2>2. Post Signups for Round <?php echo ($currentround + 1); ?></h2>
      <p>Posts the bandit signup and theme idea/voting threads.</p>
      <input type="hidden" name="Round" value="<?php echo ($currentround + 1); ?>" />
      <br />
      <button type="submit" value="Post Signups" name="postroundstart">Post Signups</button>
    </div>
  </div>
  <div class="box left">
    <h2>Wednesday</h2>
    <div class="box left">
      <h2>1. Start Round <?php echo $currentround; ?></h2>
      <p>Creates teams. Chooses winning theme. Posts team assignment and bandit consolidation threads.</p>
      <input type="hidden" name="Round2" value="<?php echo ($currentround); ?>" />
      <br />
      <button type="submit" value="Post Start Threads" name="getsignups">Start Round!</button>
    </div>
    <div class="box left">
      <h2>2. Post Winner Round <?php echo ($currentround - 1); ?></h2>
      <p>Chooses winning song and bandits. Posts congrats thread and updates DB.</p>
      <input type="hidden" name="Round4" value="<?php echo ($currentround - 1); ?>" />
      <br />
      <button type="submit" value="Post Winners" name="postwinners">Post Winners</button>
    </div>
  </div>
</form>

<div class="clear"></div>