<?php session_start();

require_once('lib/reddit.php');
require_once('src/query.php');
require_once('src/gob_user.php');

loggedin_check();

if( isset($_POST['leaveTeam']) ){
  leaveTeam();

} elseif ( isset($_POST['submitSongPage']) ) {
  goSubmitSongPage();

} elseif( isset($_POST['submitSong']) ){
  submitSong();

}

function leaveTeam(){
    $user = $_SESSION['GOB']['name'];
    $response = $reddit->sendMessage('/r/waitingforgobot', $user . ' Wants To Leave His Team', $user . ' wants to leave team 1.');
    
    redirect();

}

function goSubmitSongPage(){
    redirect('/user_submitsong');

}

function submitSong(){
	$reddit = new reddit($reddit_user, $reddit_password);
    $db = database_connect();

    $user = $_SESSION['GOB']['name'];
    $round = $_POST["round"];
    $teamnumber = $_POST["teamnumber"];
    $name = $_POST["songname"];
    $url = $_POST["url"];
    $lyrics = $_POST["lyrics"];
    $music = $_POST["music"];
    $vocals = $_POST["vocals"];
    $lyricsheet = $_POST["lyricsheet"];

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