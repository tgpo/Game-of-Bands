<div class='header'>
  <a href='index.php'>Return to song library</a> <br>
  The Game of Bands Hall of Fame - Winning Songs
</div>

<?php
  require_once('query.php');

  $db = mysqli_connect();

  // BEST SONGS
  // Orders the table by votes, then selects the first entry of each group.
  // Still needs to be modified to display equal maximums.
  $songs = $db->query('SELECT * FROM (SELECT * FROM songs WHERE votes IS NOT NULL ORDER BY votes DESC) AS s GROUP BY round DESC');
  display_songs($songs);

  // Display individual winners
  function display_single_bandit($result) {
    echo "<table>";
  	echo "<tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Winner</th><th>Votes</th></tr>";
    while ($row = mysql_fetch_array($result)) {
  		echo "<tr>";
  	  echo "<td>".a_round($row['number'],$row['round'])."</td>";
  	  echo "<td>".a_song ($row)."</td>";
  	  echo "<td>".$row['votes']."</td>";
  	  echo "<td>".a_bandit($row['winner'])."</td>";
  	  echo "<td>".$row['winnervotes']."</td>";
  	  echo "</tr>";
    }
    echo "</table>";
  }
  
  // BEST MUSICIANS
 
  // Orders the table by votes, then selects the first entry of each group.
  // Still needs to be modified to display equal maximums.
  $result = $db->query("SELECT id, name, round, votes, music AS winner, musicvote AS winnervotes FROM (SELECT * FROM songs WHERE votes IS NOT NULL ORDER BY musicvote DESC) AS s GROUP BY round DESC");
  
  echo "<div class='float'>";
    echo "<div class='header'>";
    echo "The Game of Bands Hall of Fame - Winning Musicians";
    echo "</div>";
    display_single_bandit($result);
  echo "</div>";

  // BEST LYRICISTS
  $result = $db->query("SELECT id, name, round, votes, lyrics AS winner, lyricsvote AS winnervotes FROM (SELECT * FROM songs WHERE votes IS NOT NULL ORDER BY lyricsvote DESC) AS s GROUP BY round DESC");
  echo "<div class='float'>";
    echo "<div class='header'>";
    echo "The Game of Bands Hall of Fame - Winning Lyricists";
    echo "</div>";
    display_single_bandit($result);
  echo "</div>";
  
  // BEST VOCALISTS
  $result = $db->query("SELECT id, name, round, votes, vocals AS winner, vocalsvote AS winnervotes FROM (SELECT * FROM songs WHERE votes IS NOT NULL ORDER BY vocalsvote DESC) AS s GROUP BY round DESC");
  echo "<div class='float'>";
    echo "<div class='header'>";
    echo "The Game of Bands Hall of Fame - Winning Vocalists";
    echo "</div>";
    display_single_bandit($result);
  echo "</div>";
?>