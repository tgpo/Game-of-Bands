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
$currentround = $query->fetch();
$oldRound = $currentround['number']-1;

$query = $db->query('SELECT * FROM rounds WHERE number=:oldRound limit 1');
$query->execute(array('oldRound' => $oldRound));
$oldRound = $query->fetch();

echo "Current Round " . $currentround['number'] . "<br /><br />";
echo "Previous Round " . $oldRound['number'] . "<br /><br />";

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

//Replace header links with Started Round links
    $postInfo = $reddit->getPageInfo($oldRound['songvotingthreadID'],'id');
    $oldRoundURL = $postInfo->data->children[0]->data->url;

    $postInfo = $reddit->getPageInfo($currentround['signupID'],'id');
    $currentSignupURL = $postInfo->data->children[0]->data->url;

    //Remove Old Top Header Links
    $oldHeaderLinks = "### [Go Listen, Vote, and Comment on Round " . $oldRound['number'] . "!](" . $oldRoundURL . " \"Bandits unite!\") | Want to Play With Us? [Sign Ups Now Open for Round " . $currentround['number'] . "](" . $currentSignupURL . " \"Because we are your friends!\") | [Submit Your Theme Ideas for May] (http://www.reddit.com/r/gameofbands/comments/232y43/official_theme_voting_post_may_2014/ \"Best Prizes Ever!\")";
                
    $AboutSettings = str_replace($oldHeaderLinks,"%Placeholder%",$AboutSettings);

    $currentround['number'] = $currentround['number']-1;
    $oldRound['number'] = $oldRound['number']-1;

    //Replace Round link with current Round
    $AboutSettings = str_replace("Round " . $oldRound['number'],"Round " . $currentround['number'],$AboutSettings);
    $AboutSettings = str_replace("http://gameofbands.co/round/" . $oldRound['number'],"http://gameofbands.co/round/" . $currentround['number'],$AboutSettings);


    //Remove previous round's winning song
    $previousWinningSong = query_songs($oldRound['number']);

    foreach ($previousWinningSong as $oldSong) {
      $AboutSettings = str_replace("[" . $oldSong['name'] . "](http://gameofbands.co/song/" . $oldSong['id'] . "),","",$AboutSettings);
    }

    //Add current round's winning song
    $currentWinningSong = query_songs($currentround['number']);
    foreach ($currentWinningSong as $newSong) {
      $newWinningSongs .= "[" . $newSong['name'] . "](http://gameofbands.co/song/" . $newSong['id'] . "), ";

    }
    $AboutSettings = str_replace("**Winning Song:** ","**Winning Song:** " . $newWinningSongs,$AboutSettings);

    //Removeout previous round's winners for each bandit type
    foreach ($banditTypes as $type) {
        $previousWinners = query_winners($type, $oldRound['number']);
        foreach ($previousWinners as $previousWinner) {
          $AboutSettings = str_replace("[" . $previousWinner[$type] . "](http://gameofbands.co/bandit/" . $previousWinner[$type] . "),","",$AboutSettings);
        }
    }

    //Add current round's winners for each bandit type
    foreach ($banditTypes as $type) {
        $listOfWinners = "";

        //Add this round's winners
        $winners = query_winners($type, $currentround['number']);
        foreach ($winners as $winner) {
            $listOfWinners .= "[" . $winner[$type] . "](http://gameofbands.co/bandit/" . $winner[$type] . "), ";
        }
        $AboutSettings = str_replace("**Best " . $displayTypes[$i] . ":**","**Best " . $displayTypes[$i] . ":** " . $listOfWinners,$AboutSettings);

      $i++;
    }

    $currentround['number'] = $currentround['number']+1;
    $oldRound['number'] = $oldRound['number']+1;

    $postInfo = $reddit->getPageInfo($currentround['consolidationID'],'id');
    $currentconsolidationURL = $postInfo->data->children[0]->data->url;

    $postInfo = $reddit->getPageInfo($currentround['congratsID'],'id');
    $congratsURL = $postInfo->data->children[0]->data->url;

    $postInfo = $reddit->getPageInfo($currentround['announceID'],'id');
    $announceURL = $postInfo->data->children[0]->data->url;

$newHeaderLinks = "### [Round " . $currentround['number'] . " has begun!](" . $announceURL . " \"Bandits unite!\") | Want to Play With Us? [Form A Team Now!](" . $currentconsolidationURL . " \"Because we are your friends!\") | [Be Cool, Congratulate the Winners of Round " . $oldRound['number'] . "] (" . $congratsURL . " \"Best Prizes Ever!\")";
                
    $AboutSettings = str_replace("%Placeholder%",$newHeaderLinks,$AboutSettings);

  echo "<hr><h2>After Changes</h2><pre>";
  echo $AboutSettings;
  echo "</pre>";

  //Let's save out new subreddit settings
  //$response = $reddit->siteAdmin($subredditID, $subRedditSettings->allow_top, $subRedditSettings->api_type, $subRedditSettings->comment_score_hide_mins, $subRedditSettings->css_on_cname, rawurlencode($AboutSettings), $subRedditSettings->exclude_banned_modqueue, $subRedditSettings->header_title, $subRedditSettings->lang, $subRedditSettings->content_options, $subRedditSettings->name, $subRedditSettings->over_18, $subRedditSettings->public_description, $subRedditSettings->public_traffic, $subRedditSettings->show_cname_sidebar, $subRedditSettings->show_media, $subRedditSettings->spam_comments, $subRedditSettings->spam_links, $subRedditSettings->spam_selfposts, $subRedditSettings->submit_link_label, $subRedditSettings->submit_text, $subRedditSettings->submit_text_label, $subRedditSettings->title , $subRedditSettings->subreddit_type, $subRedditSettings->wiki_edit_age, $subRedditSettings->wiki_edit_karma, $subRedditSettings->wikimode);

// Query individual winners
function query_winners($type, $round) {
  $db = database_connect();
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

function query_songs($round) {
  $db = database_connect();
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
  $result = $songs->fetchAll();
  return $result;
}


?>