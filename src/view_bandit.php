<?php
  $bandit = $_GET['bandit'];
  if (!$bandit) {
    header("Location: index.php"); // revert to index
    exit();
  }
?>

<div class='header'>
  <a href='index.php'>Return to song library</a> <br>
  Viewing <?php echo $bandit; ?>'s profile:
</div>

<div id='content'>
<?php
  require_once('query.php');
  
  $db    = mysqli_connect();
  $songs = $db->query("SELECT * FROM songs WHERE music='$bandit' OR lyrics='$bandit' OR vocals='$bandit'");
  display_songs($songs);
?>
</div>
