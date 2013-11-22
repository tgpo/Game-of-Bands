<div class='header'>
  <a href='index.php'>Return to song library</a> <br>
  The Game of Bands Hall of Fame - Winning Songs
</div>

<?php
  require_once('query.php');

  $db = database_connect();

  // BEST SONGS
  // Calculate table with maximum votes from each round.
  // Join with songs that have the same number of votes.
  $query = 'SELECT * FROM songs
    JOIN
      (SELECT round, MAX(votes) AS vote FROM songs
       WHERE votes > 0
       GROUP BY round
      ) AS themax
    ON songs.round = themax.round AND songs.votes = themax.vote
    ORDER BY songs.round DESC';  
  $songs = $db->query($query);
  display_songs($songs);


  // Query individual winners
  function query_winners($db, $type) {
    // Calculate table with maximum votes from each round.
    // Join with bandits that have the same number of votes.
    $vote  = $type.'vote';
    $query = "SELECT id, name, songs.round AS round, votes, $type AS winner, $vote AS winnervotes
      FROM songs
      JOIN
        (SELECT round, MAX($vote) AS vote FROM songs
         WHERE $vote > 0
         GROUP BY round
        ) AS themax
      ON songs.round = themax.round AND songs.$vote = themax.vote
      ORDER BY songs.round DESC";
    return $db->query($query);
  }
  
  // Display individual winners
  function display_winners($result) {
    echo "<table>";
  	echo "<tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Winner</th><th>Votes</th></tr>";
    foreach ($result as $row) {
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
  $result = query_winners($db, 'music');
  echo "<div class='float'>";
    echo "<div class='header'>";
    echo "The Game of Bands Hall of Fame - Winning Musicians";
    echo "</div>";
    display_winners($result);
  echo "</div>";

  // BEST LYRICISTS
  $result = query_winners($db, 'lyrics');
  echo "<div class='float'>";
    echo "<div class='header'>";
    echo "The Game of Bands Hall of Fame - Winning Lyricists";
    echo "</div>";
    display_winners($result);
  echo "</div>";
  
  // BEST VOCALISTS
  $result = query_winners($db, 'vocals');
  echo "<div class='float'>";
    echo "<div class='header'>";
    echo "The Game of Bands Hall of Fame - Winning Vocalists";
    echo "</div>";
    display_winners($result);
  echo "</div>";
?>