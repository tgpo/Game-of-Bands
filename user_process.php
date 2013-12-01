<?php session_start(); ?>
<?php
require_once('lib/reddit.php');
require_once('src/query.php');
require_once('src/gob_user.php');

loggedin_check();
$reddit = new reddit($reddit_user, $reddit_password);

if(isset($_POST['leaveTeam'])){
  $bandit     = $_SESSION['GOB']['name'];
  
  mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
  mysql_select_db($mysql_db) or die(mysql_error());
  $result = mysql_query("SELECT * FROM rounds order by number desc limit 1 ") or die(mysql_error()); 
  $round = mysql_fetch_array($result);
  
  $currentround = $round['number'];
  $currentround = 34;  
  
  echo 'Round' . $currentround;
  
  $result = mysql_query("SELECT * FROM teams WHERE musician='$bandit' OR lyricist='$bandit' OR vocalist='$bandit' AND round='$currentround' limit 1") or die(mysql_error()); 
  $team = mysql_fetch_array($result);
  
  echo "You Are On Team Number " . $team['teamnumber'];
  
  //$response = $reddit->sendMessage('tgpo', $bandit . ' Has left Team', $bandit . ' wants to leave team 1.');
  
  
  //redirect();
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
    ':lyricsheet' => $_POST["lyricsheet"],
    ':round'      => $_POST["round"],
    ':teamnumber' => $_POST["teamnumber"],
    ':submitby'   => $user,
    ':approved'   => 'false'
    ));
  
  $response = $reddit->sendMessage('/r/gameofbands', 'Team', $user . ' submitted the song ' . $name );
  
  redirect();
}

function redirect($page = 'index.php'){
  header('Location: '.$page);
}
?>