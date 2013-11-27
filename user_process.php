<?php session_start(); ?>
<?php
require_once('lib/reddit.php');
require_once('src/query.php');
require_once('src/gob_user.php');

loggedin_check();
$reddit = new reddit($reddit_user, $reddit_password);

if(isset($_POST['leaveTeam'])){
  $user     = $_SESSION['GOB']['name'];
  $response = $reddit->sendMessage('/r/waitingforgobot', $user . ' Wants To Leave His Team', $user . ' wants to leave team 1.');
  
  redirect();
}

if(isset($_POST['submitSongPage'])){
  redirect('/user_submitsong');
}

if(isset($_POST['submitSong'])){
  $db    = database_connect();  
  $sql   = "INSERT INTO songs (name, url, music, lyrics, vocals, lyricsheet, round, teamnumber, submitby, approved) VALUES (:name, :url, :music, :lyrics, :vocals, :lyricsheet, :round, :teamnumber, :submitby, :approved)";
  $query = $db->prepare($sql);
  $query->execute(array(
    ':name'       => $_POST["songname"],
    ':url'        => $_POST["url"],
    ':music'      => $_POST["music"],
    ':lyrics'     => $_POST["lyrics"],
    ':vocals'     => $_POST["vocals"],
    ':lyricsheet' => $_POST["lyricsheet"]
    ':round'      => $_POST["round"],
    ':teamnumber' => $_POST["teamnumber"],
    ':submitby'   => $user,
    ':approved'   => 'false'
    ));
  
  $response = $reddit->sendMessage('tgpo', 'Team', $user . ' submitted the song ' . $name );
  
  redirect();
}

function redirect($page = 'index.php'){
  header('Location: '.$page);
}
?>