<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}

require_once('../src/query.php');
$db    = database_connect();

$query = $db->query('SELECT * FROM rounds order by number desc limit 1');
$round = $query->fetch();
$currentround = $round['number'];

function displayMessages($currentuser){
  $db = database_connect();
  $messages = $db->query("SELECT * FROM messages WHERE user_to = '$currentuser' order by date_sent desc");
  
  foreach ($messages as $message) {
    echo '<li data-id="' .$message['id']  . '"';
	
	if($message['new']) echo ' class="new"';
    
	echo ">" . $message['body'] . "<br /><small>to: </small>" . $message['user_to'] . " <small>From: </small>" . $message['user_from'] . " <small>Sent: </small>" . $message['date_sent'] . "<br /><a href='#' class='delete'>Delete</a></li>";
  }
}

function displayBanditDropdown(){
  $db = database_connect();
  $bandits = $db->query('SELECT * FROM bandits order by name asc');
  foreach ($bandits as $bandit) {
    $bandit['is_mod'] ? $moderators .= '<option value="' . $bandit['name'] . '">' . $bandit['name'] . '</option>' : $standards .= '<option value="' . $bandit['name'] . '">' . $bandit['name'] . '</option>';
  }
  
  $selectHTML = '<select id="user_to" name="user_to" />';
  $selectHTML .= '<option value="allmods">All Moderators</option>';
  $selectHTML .= '<option value="everyone">Everyone</option>';
  $selectHTML .= '<optgroup label="Moderators">' . $moderators . '</optgroup>';
  $selectHTML .= '<optgroup label="Bandits">' . $standards . '</optgroup>';
  $selectHTML .= '</select>';
  
  echo $selectHTML;
}
	
?>

<script>
$(document).ready(function(){
    $("#messages #postMessage").click(function(event){
	  event.stopPropagation();
	  
	  var user_to = $('#messages #user_to').val();
	  var user_from = "<? echo $_SESSION['GOB']['name'] ?>";
	  var body = $(this).prev('#body').val();
	  
	  $.ajax({
	    url: 'admin_process.php',
		data: {postMessage: 'postMessage', user_to: user_to, user_from: user_from, body: body},
		type: 'post',
		success: function(output) {
			if(user_to == "<? echo $_SESSION['GOB']['name'] ?>" || user_to == "allmods" || user_to == "everyone") {
				$("#messagelist").prepend(output);
				$('#messages #user_to').val("allmods");
				$('#messages #body').val("");
			};
		}
	  });
	  
	  return false;
    });
	
	$("#messagelist").on("click", "a.delete", function(event){
	  event.stopPropagation();
	  $(this).parent().fadeOut();
	  
	  var messageID = $(this).parent().attr('data-id');
	  $.ajax({
	    url: 'admin_process.php',
		data: {deleteMessage: 'deleteMessage', id: messageID},
		type: 'post',
		success: function(output) {
		}
	  });
	  
	  return false;
    });
    $("#messagelist").on("click", "li", function(){
	  $(this).removeClass('new');
	  var messageID = $(this).attr('data-id');
	  $.ajax({
	    url: 'admin_process.php',
		data: {action: 'markMessageRead', id: messageID},
		type: 'post',
		success: function(output) {

		}
	  });
    });

  $('#adminform').submit(function() {
    var c = confirm("You are about to post to reddit and change the website database!\n\n You sure you want to do that?");
    return c; 
  });
});
</script>
<style type="text/css">
#messages .new { background: #ccc; }
</style>
  <div id="messages" class="box right">
    <h2>Mod Messages</h2>
	<ul id="messagelist">
	  <?php displayMessages($_SESSION['GOB']['name']); ?>
	</ul>
	  <h5>Post New Message</h5>
	  <label>To</label>
	  <?php displayBanditDropdown(); ?>
	  <textarea id="body" rows="15" cols="25" name="body"></textarea>

	  <button id="postMessage">Post Message</button>
  </div>
<form id="adminform" method="post" action="admin_process.php">
  <div class="box left">
    <h2>Sunday</h2>
    <div class="box left">
      <h2>1. Post Song Voting Thread for Round <?php echo $currentround; ?></h2>
      <p>Posts the voting thread for all submitted songs.</p>
      <br />
      <button type="submit" value="Post Song Voting Thread" name="postvote">Post Voting Thread</button>
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