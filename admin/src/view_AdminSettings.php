<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;
}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
require_once( '../lib/reddit.php' );
$db = database_connect();

$reddit = new reddit($reddit_user, $reddit_password);
$subredditID = "t5_2tywd";

$query = $db->query('SELECT * FROM rounds WHERE theme IS NOT NULL AND theme <> "NULL" order by number desc limit 1');
$round = $query->fetch();
$currentround = $round['number']-1;

//Dev Overide
$oldRound = $currentround-1;

echo "Current Round " . $currentround . "<br /><br />";
echo "Previous Round " . $oldRound . "<br /><br />";

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

function query_songs($round, $db) {
  $query = "SELECT  *
    FROM    songs 
    WHERE   round = '$round' 
    HAVING  votes =
    (
      SELECT  votes
      FROM    songs 
      WHERE   round = '$round'
      ORDER BY votes DESC
      LIMIT 1  
    )"; 
  $songs = $db->query($query);
  return $songs;
}
?>

<h2>/r/GameofBands Admin Settings</h2>
<?php
  $subRedditSettings = $reddit->getAboutSettings('gameofbands');
  $AboutSettings = $subRedditSettings->description;
  $AboutSettings = urldecode($AboutSettings);

  echo "<h2>Before Changes</h2><pre>";
  echo $AboutSettings;
  echo "</pre>";

  $banditTypes = array('music','lyrics','vocals');
  $displayTypes = array('Musician','Lyricist','Vocalist');
  $i = 0;

  //CLean out last round's winning song
    $AboutSettings = str_replace("Round " . $oldRound,"Round " . $currentround,$AboutSettings);
    $AboutSettings = str_replace("http://gameofbands.co/round/" . $oldRound,"http://gameofbands.co/round/" . $currentround,$AboutSettings);

    $previousWinningSong = query_songs($oldRound, $db);
    foreach ($previousWinningSong as $oldSong) {
      $AboutSettings = str_replace("[" . $oldSong['name'] . "](http://gameofbands.co/song/" . $oldSong['id'] . "),","",$AboutSettings);
    }
    $currentWinningSong = query_songs($currentround, $db);
    foreach ($currentWinningSong as $newSong) {
      $newWinningSongs .= "[" . $newSong['name'] . "](http://gameofbands.co/song/" . $newSong['id'] . "), ";

    }
    $AboutSettings = str_replace("**Winning Song:** ","**Winning Song:** " . $newWinningSongs,$AboutSettings);

    foreach ($banditTypes as $type) {
        //Clean out previous round's winners
        $previousWinners = query_winners($type, $oldRound, $db);
        foreach ($previousWinners as $previousWinner) {
          $AboutSettings = str_replace("[" . $previousWinner[$type] . "](http://gameofbands.co/bandit/" . $previousWinner[$type] . "),","",$AboutSettings);
        }
    }

    foreach ($banditTypes as $type) {
        $listOfWinners = "";

        //Add this round's winners
        $winners = query_winners($type, $currentround, $db);
        foreach ($winners as $winner) {
            $listOfWinners .= "[" . $winner[$type] . "](http://gameofbands.co/bandit/" . $winner[$type] . "), ";
        }
        $AboutSettings = str_replace("**Best " . $displayTypes[$i] . ":**","**Best " . $displayTypes[$i] . ":** " . $listOfWinners,$AboutSettings);

      $i++;
    }

  echo "<hr><h2>After Changes</h2><pre>";
  echo $AboutSettings;
  echo "</pre>";

  //Let's save out new subreddit settings
  //$response = $reddit->siteAdmin($subredditID, $subRedditSettings->allow_top, $subRedditSettings->api_type, $subRedditSettings->comment_score_hide_mins, $subRedditSettings->css_on_cname, rawurlencode($AboutSettings), $subRedditSettings->exclude_banned_modqueue, $subRedditSettings->header_title, $subRedditSettings->lang, $subRedditSettings->content_options, $subRedditSettings->name, $subRedditSettings->over_18, $subRedditSettings->public_description, $subRedditSettings->public_traffic, $subRedditSettings->show_cname_sidebar, $subRedditSettings->show_media, $subRedditSettings->spam_comments, $subRedditSettings->spam_links, $subRedditSettings->spam_selfposts, $subRedditSettings->submit_link_label, $subRedditSettings->submit_text, $subRedditSettings->submit_text_label, $subRedditSettings->title , $subRedditSettings->subreddit_type, $subRedditSettings->wiki_edit_age, $subRedditSettings->wiki_edit_karma, $subRedditSettings->wikimode);
?>