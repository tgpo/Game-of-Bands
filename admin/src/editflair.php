<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
require_once('../lib/reddit.php');
$db = database_connect();

$reddit = new reddit($reddit_user, $reddit_password);

$query = $db->query('SELECT * FROM rounds WHERE theme IS NOT NULL AND theme <> "NULL" order by number desc limit 1');
$round = $query->fetch();
$currentround = $round['number'];

//Dev Overide
$currentround = $currentround-1;

for ($i = 1; $i <= $currentround; $i++) {

  echo "<h2>Round " . $i . "</h2>";
  
  $winningSongs = query_songs($i, $db);
  foreach ($winningSongs as $song) {
    if (!empty($song['votes'])) {
      echo "Winning Song: " . $song['name'] . "<br>";
      echo "Team Winners: " . $song['music'] . ', ' . $song['lyrics'] . ', ' . $song['vocals'] . "<br>";
      $banditTypes = array('music','lyrics','vocals');
      foreach ($banditTypes as $type) {
        $query = $db->prepare('SELECT TeamWins FROM bandits WHERE name=:bandit');
        $query->execute(array('bandit' => $song[$type]));
        $bandit  = $query->fetch();
        
        $TeamWins = $bandit['TeamWins']+1;
        
        //$query = $db->prepare('UPDATE bandits SET TeamWins = :TeamWins WHERE name=:bandit');
        //$query->execute(array('TeamWins' => $TeamWins, 'bandit' => $song[$type]));

      }
    }
  }

  $banditTypes = array('music','lyrics','vocals');
  foreach ($banditTypes as $type) {
      $winners = query_winners($type, $i, $db);
      foreach ($winners as $winner) {
        if (!empty($winner['votes'])) {
          echo $type . " Winner: " . $winner[$type] . "<br />";
          $currentfield = ucfirst($type) . 'Wins';
          
          $query = $db->prepare('SELECT ' . $currentfield . ' FROM bandits WHERE name=:bandit');
          $query->execute(array('bandit' => $winner[$type]));
          $bandit  = $query->fetch();

          $wins = $bandit[$currentfield]+1;

          //$query = $db->prepare('UPDATE bandits SET ' . $currentfield . ' = :Wins WHERE name=:bandit');
          //$query->execute(array('Wins' => $wins, 'bandit' => $winner[$type]));
        }
      }
  }
  
  echo "<br /><br />";
  
}

// Query winning songs
function query_songs($round, $db) {
    // Calculate table with maximum votes from each round.
    // Join with bandits that have the same number of votes.
    $votetype  = 'votes';
    $query = "SELECT  *
    FROM    songs 
    WHERE   round = '$round' 
    HAVING  $votetype =
    (
        SELECT  $votetype
        FROM    songs 
        WHERE   round = '$round'
        ORDER BY $votetype DESC
        LIMIT 1  
    )";

    $result = $db->query($query);

    return $result;
}

// Query individual winners
function query_winners($type, $round, $db) {
    // Calculate table with maximum votes from each round.
    // Join with bandits that have the same number of votes.
    $votetype  = $type.'vote';
    $query = "SELECT  *
    FROM    songs 
    WHERE   round = '$round' 
    HAVING  $votetype =
    (
        SELECT  $votetype
        FROM    songs 
        WHERE   round = '$round'
        ORDER BY $votetype DESC
        LIMIT 1  
    )";

    $result = $db->query($query);

    return $result;
}
?>
<?php

$strStylesheet = $reddit->getStylesheet('gameofbands');
?>

<h2>Edit CSS</h2>
<pre>
<?php echo $strStylesheet; ?>
</pre>