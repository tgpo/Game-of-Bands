<?php session_start();

require_once('src/gob_user.php');
require_once('lib/reddit.php');
require_once('src/secrets.php');
require_once('src/query.php');

loggedin_check();

$reddit = new reddit($reddit_user, $reddit_password);

if( isset($_POST['leaveTeam']) ){
  leaveTeam();

} elseif ( isset($_POST['submitSongPage']) ) {
  goSubmitSongPage();

} elseif( isset($_POST['submitSong']) ){
  submitSong($reddit);

}

function leaveTeam(){
    $user = $_SESSION['GOB']['name'];
    $response = $reddit->sendMessage('/r/waitingforgobot', $user . ' Wants To Leave His Team', $user . ' wants to leave team 1.');
    
    redirect();

}

function goSubmitSongPage(){
    redirect('/user_submitsong');

}

function submitSong($reddit){
    $db = database_connect();

    $user = $_SESSION['GOB']['name'];
    $round = filter_input(INPUT_POST, 'round', FILTER_SANITIZE_NUMBER_INT);
    $teamnumber = filter_input(INPUT_POST, 'teamnumber', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'songname', FILTER_SANITIZE_URL);
    $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
    $lyrics = filter_input(INPUT_POST, 'lyrics', FILTER_SANITIZE_SPECIAL_CHARS);
    $music = filter_input(INPUT_POST, 'music', FILTER_SANITIZE_SPECIAL_CHARS);
    $vocals = filter_input(INPUT_POST, 'vocals', FILTER_SANITIZE_SPECIAL_CHARS);
    $lyricsheet = filter_input(INPUT_POST, 'lyricsheet', FILTER_SANITIZE_SPECIAL_CHARS);

    $newSong = $db->prepare('INSERT INTO songs (name, url, music, lyrics, vocals, lyricsheet, round, teamnumber, submitby, approved) VALUES (:name, :url, :music, :lyrics, :vocals, :lyricsheet, :round, :teamnumber, :submitby, :approved)');
    $newSong->execute(array(':name' => $name,
                          ':url' => $url,
                          ':music' => $music,
                          ':lyrics' => $lyrics,
                          ':vocals' => $vocals,
                          ':lyricsheet' => $lyricsheet,
                          ':round' => $round,
                          ':teamnumber' => $teamnumber,
                          ':submitby' => $user,
                          ':approved' => false
    ));

    
    $response = $reddit->sendMessage('/r/gameofbands', 'Team ' . $teamnumber, $user . ' submitted the song ' . $name );
    redirect();

}

function redirect($page = 'index.php'){
    header('Location: '.$page);

}

?>