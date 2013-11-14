<?php session_start(); ?>
<?php
require_once('lib/reddit.php');
require_once('src/secrets.php');
$reddit = new reddit($reddit_user, $reddit_password);

if(isset($_POST['leaveTeam'])){
	$user = $_SESSION['GOB']['name'];
	$response = $reddit->sendMessage('/r/waitingforgobot', $user . ' Wants To Leave His Team', $user . ' wants to leave team 1.');
	
	redirect('dashboard');
}



function redirect($pagename){
	header('Location: '.$pagename.'.php');
}
?>