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
  
  $db    = database_connect();
  $query = $db->prepare('SELECT * FROM songs WHERE music=? OR lyrics=? OR vocals=?');
  $query->bind_param('sss',$bandit,$bandit,$bandit);
  $query->execute();
  $songs = $query->get_result();
  display_songs($songs);
?>
</div>
