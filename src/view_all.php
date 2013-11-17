<div class='header'>
  Select individual rounds, songs or bandits to navigate. <br />
  <a href='/hall_of_fame'>View Hall of Fame</a> <br />
  <a href='/all_rounds'>View Rounds by Theme</a> <br />

  Viewing Complete Song Library:
</div>

<div id='content'>
<?php
  require_once('query.php');
  
  $db    = database_connect();
  $songs = $db->query('SELECT * FROM songs ORDER by id DESC');
  display_songs($songs);
?>
</div>
