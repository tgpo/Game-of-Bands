<?php
include 'header.php';

require_once( 'src/secrets.php' );

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db('xxxdatabasexxx') or die(mysql_error());

$result = mysql_query("SELECT * FROM rounds order by number desc limit 1 ") or die(mysql_error()); 
$round = mysql_fetch_array($result);
$currentround = $round['number'];
 ?>

<h1>Submit Song for Round <?php echo $currentround; ?></h1>
<h2>You must be logged in to submit a song</h2>
<p><a href="/login.php">Click Here</a> to login.</p>

<?php
include 'footer.php';
?>