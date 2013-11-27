<h2>Game of Bands Song Library</h2>
<p>Select individual rounds, songs or bandits to navigate.</p>

<aside id="otherviews">
  <a href='/hall_of_fame' class="halloffame">View Hall of Fame</a> <br />
  <a href='/all_rounds' class="roundbytheme">View Rounds by Theme</a> <br />
</aside>

<section id='songtable'>
<?php
  require_once('query.php');
  
  $db    = database_connect();
  $songs = $db->query('SELECT * FROM songs WHERE approved=1 ORDER by id DESC');
  display_songs($songs);
?>
</section>
