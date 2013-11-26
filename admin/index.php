<?php
require_once( 'includes/gob_admin.php' );
require_once( 'includes/admin_header.php' );

mod_check();

require_once('../src/query.php');
$db    = database_connect();

$query = $db->query('SELECT * FROM rounds order by number desc limit 1');
$round  = $query->fetch();
$currentround = $round['number'];
?>

<form method="post" action="admin_process.php">
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
			<h2>1. Start New Round</h2>
			<p>Creates teams. Chooses winning theme. Posts team assignment and bandit consolidation threads.</p>
			<label>Round</label>
			<input type="text" name="Round2" />
			<br />
			<button type="submit" value="Post Start Threads" name="getsignups">Start Round!</button>
		</div>
		<div class="box left">
			<h2>2. Post Winner of ending round</h2>
			<p>Creates teams. Chooses winning theme. Posts team assignment and bandit consolidation threads.</p>
			<label>Round</label>
			<input type="text" name="Round4" />
			<br />
			<button type="submit" value="Post Winners" name="postwinners">Post Winners</button>
		</div>
	</div>
</form>

<div class="clear"></div>

<?php
require_once( 'includes/admin_footer.php' );
?>