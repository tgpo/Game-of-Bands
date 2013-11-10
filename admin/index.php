<?php
require_once( 'includes/gob_admin.php' );
require_once( 'includes/admin_header.php' );

mod_check();
?>

<form method="post" action="admin_process.php">
	<div class="box left">
		<h2>Post Round Start Threads</h2>
		<label>Round</label>
		<input type="text" name="Round" />
		<br />
		<button type="submit" value="Post Consolidation" name="postroundstart">Post Round Start Threads</button>
	</div>
	<!--  In development -->
	<div class="box left">
		<span class="corner-banner"> 
            <em>Beta</em> 
        </span> 
		<h2>Get Signups</h2>
		<label>Round</label>
		<input type="text" name="Round2" />
		<br />
		<button type="submit" value="Post Consolidation" name="getsignups">Get Signups</button>
	</div>
</form>

<div class="clear"></div>

<?php
require_once( 'includes/admin_footer.php' );
?>
