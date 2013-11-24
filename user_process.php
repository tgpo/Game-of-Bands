<?php session_start(); ?>
<?php
require_once('lib/reddit.php');
require_once('src/query.php');
require_once( 'src/gob_user.php' );

$reddit = new reddit($reddit_user, $reddit_password);
loggedin_check();

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db("xxxdatabasexxx") or die(mysql_error());

if(isset($_POST['leaveTeam'])){
	$user = $_SESSION['GOB']['name'];
	$response = $reddit->sendMessage('/r/waitingforgobot', $user . ' Wants To Leave His Team', $user . ' wants to leave team 1.');
	
	redirect();
}

if(isset($_POST['submitSongPage'])){
	redirect('/user_submitsong');
}

if(isset($_POST['submitSong'])){
	$user = mysql_real_escape_string( $_SESSION['GOB']['name'] );
	$round = mysql_real_escape_string( $_POST["round"] );
	$teamnumber = mysql_real_escape_string( $_POST["teamnumber"] );
	$name = mysql_real_escape_string( $_POST["songname"] );
	$url = mysql_real_escape_string( $_POST["url"] );
	$lyrics = mysql_real_escape_string( $_POST["lyrics"] );
	$music = mysql_real_escape_string( $_POST["music"] );
	$vocals = mysql_real_escape_string( $_POST["vocals"] );
	$lyricsheet = mysql_real_escape_string( $_POST["lyricsheet"] );
	
	
  
	$db = database_connect();  
	$sql = "INSERT INTO songs (name, url, music, lyrics, vocals, lyricsheet, round, teamnumber, submitby, approved) VALUES (:name, :url, :music, :lyrics, :vocals, :lyricsheet, :round, :teamnumber, :submitby, :approved)";
	$query = $db->prepare($sql);
	$query->execute(array(':name'=>$name,
					  ':url'=>$url,
					  ':music'=>$music,
					  ':lyrics'=>$lyrics,
					  ':vocals'=>$vocals,
					  ':lyricsheet'=>$lyricsheet,
					  ':round'=>$round,
					  ':teamnumber'=>$teamnumber,
					  ':submitby'=>$user,
					  ':approved'=>'false'
					  ));

	
	$response = $reddit->sendMessage('tgpo', 'Team', $user . ' submitted the song ' . $name );
	
	redirect();
}

function redirect($page = 'index.php'){
	header('Location: '.$page);
}
?>