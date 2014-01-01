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
$currentround = 34;

echo "Round " . $currentround . "<br /><br />";

$banditTypes = array('music','lyrics','vocals');
foreach ($banditTypes as $type) {
    $winners = query_winners($type, $currentround, $db);
    foreach ($winners as $winner) {
        echo $type . " Winner: " . $winner[$type] . "<br />";
    }
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

<h2>Give Flair for Last Round</h2>
<?php 
    $liveFlair = $reddit->getFlairList('waitingforgobot');
    echo "<table>";
        echo "<thead>";
            echo "<tr>";
                echo "<th>Bandit</th>";
                echo "<th>Flair Class</th>";
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($liveFlair->users as $bandit) {
            echo "<tr>";
                echo "<td>" . $bandit->user . "</td>";
                echo "<td>" . $bandit->flair_css_class . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
    echo "</table>";
?>
