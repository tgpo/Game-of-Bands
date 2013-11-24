<?php
  require_once('query.php');
  
  $db     = database_connect();
	$query = $db->query("SELECT * FROM rounds order by number desc limit 1 ") or die(mysql_error()); 
	$song  = $query->fetch();
	$currentround = $song['number'];
?>

<h1>Submit Song for Round <?php echo $currentround; ?></h1>
<h2>You must be logged in to submit a song</h2>
<p><a href="/login.php">Click Here</a> to login.</p>