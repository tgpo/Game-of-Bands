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

echo '<h1>Current round: ' . $currentround . '</h1><p>Note: Number of queries required depends on number of rounds, this may take a minute.</p>';

//Reset Counts
$query = $db->query('UPDATE bandits SET TeamWins = NULL, Musicwins = NULL, Lyricswins = NULL, Vocalswins = NULL');

//Dev Overide
$currentround = $currentround-1;

$out = array();

for ($i = 1; $i <= $currentround; $i++) {

  $winningSongs = query_songs($i, $db);
  foreach ($winningSongs as $song) {	
    if (!empty($song['votes'])) {
      $banditTypes = array('music','lyrics','vocals');
      foreach ($banditTypes as $type) {
      	$out[]= 'Winning ' . $type . 'votes song = ' . $song['name'] . '<br />';
        //Make sure bandit exists in bandit table
        $query = $db->query("SELECT * FROM bandits WHERE name = '$song[$type]' LIMIT 1");
        $bandit = $query->fetch();

         if(!$bandit['name']){
         	$out[]= '<strong>Added bandit: ' . $winner[$type] . '</strong> -----------> Yay!<br />';
          	$query = $db->query("INSERT INTO bandits (name, is_mod, banned) VALUES ('$song[$type]', 0, 0)");
        }
        
        $query = $db->prepare('SELECT TeamWins FROM bandits WHERE name=:bandit LIMIT 1');
        $query->execute(array('bandit' => $song[$type]));
        $bandit  = $query->fetch();

        $TeamWins = $bandit['TeamWins']+1;
        
       $out[]= 'Setting ' . $TeamWins . ' wins for ' . $song[$type] . '<br />';

        $query = $db->prepare('UPDATE bandits SET TeamWins = :TeamWins WHERE name=:bandit LIMIT 1');
        $query->execute(array('TeamWins' => $TeamWins, 'bandit' => $song[$type]));
        
      }
    }
  }
  $out[]= 'Found: ' . count($winningSongs) . ' winning songs.<br />';

  $banditTypes = array('music','lyrics','vocals');
  foreach ($banditTypes as $type) {
      $winners = query_winners($type, $i, $db);
      foreach ($winners as $winner) {
        if (!empty($winner['votes'])) {
          //Make sure bandit exists in bandit table
          $query = $db->query("SELECT * FROM bandits WHERE name = '$winner[$type]'");
          $bandit = $query->fetch();
          
          if(!$bandit['name']){
          	$out[]= '<strong>Added bandit: ' . $winner[$type] . '</strong> -----------> Yay!<br />';
            $query = $db->query("INSERT INTO bandits (name, is_mod, banned) VALUES ('$winner[$type]', 0, 0) LIMIT 1");
          }
          
          $currentfield = ucfirst($type) . 'Wins';

          $query = $db->prepare('SELECT ' . $currentfield . ' FROM bandits WHERE name=:bandit LIMIT 1');
          $query->execute(array('bandit' => $winner[$type]));
          $bandit  = $query->fetch();

          $wins = $bandit[$currentfield]+1;
          
          $out[]= 'Setting ' . $type . ' to ' . $wins . ' for ' . $winner[$type] . '<br />';

          $query = $db->prepare('UPDATE bandits SET ' . $currentfield . ' = :Wins WHERE name=:bandit LIMIT 1');
          $query->execute(array('Wins' => $wins, 'bandit' => $winner[$type]));
        }
      }
  }
  $out[]= '<br /><h2>Resetting flair for round: ' . $i . '</h2>';
  
}
//print array, invert the output so its obvious.
foreach(array_reverse($out) as $l){echo $l;}


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

    $result = sql_to_array($query);

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

    $result = sql_to_array($query);

    return $result;
}
