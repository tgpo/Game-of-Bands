<?php
if(!defined('INDEX')) {
    header('Location: ../index.php');
    die;
}

require_once('query.php');
  
$db = database_connect();
$query = $db->query('SELECT * FROM rounds WHERE theme IS NOT NULL AND theme <> "NULL" order by number desc limit 1');
$song  = $query->fetch();
$currentround = $song['number'];

?>

<h2>Submit Song for Round <?php echo $currentround; ?></h2>
<h3 class="warning">You must be logged in to submit a song</h3>
<p class="warning">
    <a class="login" href="/login.php">Click Here</a> to login.
</p>