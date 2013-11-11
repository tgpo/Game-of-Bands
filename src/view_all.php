<div class='header'>
  Select individual rounds, songs or bandits to navigate. <br />
  <a href='index.php?view=fame'>View Hall of Fame</a> <br />
  <a href='index.php?view=rounds'>View Rounds by Theme</a> <br />

  Viewing Complete Song Library:
</div>

<div id='content'>
<?php
  require_once('query.php');
  
  $db    = mysqli_connect();
  $songs = $db->query('SELECT * FROM songs ORDER by id DESC');
  display_songs($songs);
?>
</div>
