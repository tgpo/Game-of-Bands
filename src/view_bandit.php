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
  $query = $db->prepare('SELECT * FROM songs WHERE (lyrics=:lyrics OR music=:music OR vocals=:vocals)');
  $query->execute(array('music' => $bandit, 'lyrics' => $bandit, 'vocals' => $bandit));
  display_songs($query);
?>
</div>
