<?php
require_once( 'includes/gob_admin.php' );
require_once( 'includes/admin_header.php' );

mod_check();
?>

<form method="post" action="admin_process.php">
	<div class="box left">
		<h2>Sunday</h2>
		<div class="box left">
			<span class="corner-banner live"> 
				<em>Live</em> 
			</span> 
			<h2>1. Post Song Voting Thread</h2>
			<br />
			<button type="submit" value="Post Song Voting Thread" name="postvote">Post Voting Thread</button>
		</div>
		<div class="box left">
			<span class="corner-banner live"> 
				<em>Live</em> 
			</span> 
			<h2>2. Post Bandit Signups</h2>
			<label>Round</label>
			<input type="text" name="Round" />
			<br />
			<button type="submit" value="Post Signups" name="postroundstart">Post Signups</button>
		</div>
	</div>
	<div class="box left">
		<h2>Wednesday</h2>
		<div class="box left">
			<span class="corner-banner live"> 
				<em>Live</em> 
			</span> 
			<h2>1. Start New Round</h2>
			<label>Round</label>
			<input type="text" name="Round2" />
			<br />
			<button type="submit" value="Post Start Threads" name="getsignups">Start Round!</button>
		</div>
	</div>
</form>

<div class="clear"></div>

<?php
require_once( 'includes/admin_footer.php' );
?>