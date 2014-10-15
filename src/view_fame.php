<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}
?>
<aside id="otherviews">
  <a href='/index.php' class="returnhome">Return to song library</a>
</aside>

<h2>Game of Bands Hall of Fame</h2>

<h3>Winning Songs</h3>
<section id="halloffame">
<?php
  require_once('query.php');

  $db = database_connect();

  // BEST SONGS
  // Calculate table with maximum votes from each round.
  // Join with songs that have the same number of votes.
  $query = 'select * from songs b where b.votes = (select max(b2.votes) from songs b2 where b2.round = b.round) AND b.votes > 0';

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
    echo "<table class='winnertable'>";
  	echo "<thead><tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Winner</th><th>Votes</th></tr></thead></tbody>";
    foreach ($result as $row) {
  		echo "<tr>";
  	  echo "<td class='round'>".a_round($row['round'],$row['round'])."</td>";
  	  echo "<td class='songname'>".a_song ($row)."</td>";
  	  echo "<td class='songvotes'>".$row['votes']."</td>";
  	  echo "<td class='bandit'>".a_bandit($row['winner'])."</td>";
  	  echo "<td class='banditvote'>".$row['winnervotes']."</td>";
  	  echo "</tr>";
    }
    echo "</tbody></table>";
  }
  
  // BEST MUSICIANS
  $result = query_winners($db, 'music');
  echo "<section class='float'>";
    echo "<h3>";
    echo "Winning Musicians";
    echo "</h3>";
    display_winners($result);
  echo "</section>";

  // BEST LYRICISTS
  $result = query_winners($db, 'lyrics');
  echo "<section class='float'>";
    echo "<h3>";
    echo "Winning Lyricists";
    echo "</h3>";
    display_winners($result);
  echo "</section>";
  
  // BEST VOCALISTS
  $result = query_winners($db, 'vocals');
  echo "<section class='float'>";
    echo "<h3>";
    echo "Winning Vocalists";
    echo "</h3>";
    display_winners($result);
  echo "</section>";
?>
</section>