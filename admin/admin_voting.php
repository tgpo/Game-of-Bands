<?php
require_once( 'includes/gob_admin.php' );
require_once( '../src/query.php' );

mod_check();

if(isset($_POST['startvoting'])){
	change_voting(true);
}elseif(isset($_POST['closevoting'])){
	change_voting(false);
}else{
	say('Wut?');
}


function change_voting($start=true){
	$round = (isset($_POST['inputround'])) ? $_POST['inputround'] : false;
	if(!is_numeric($round) || strlen($round)<1){
		say('You must enter a round #');
	};
	if(isset($_POST['datetime']) && strlen($_POST['datetime'])>1){
		$datetime = $_POST['datetime'];
	}else{
		say("We could just do it now.. but it's better if you specify when.");
	}
	$mysql_time = date('Y-m-d H:i:s',strtotime($datetime));
	insert_query("UPDATE rounds SET " . (($start) ? 'start' : 'end') . " ='$mysql_time' WHERE number=:round",array('round' => $round));
	
	say("Voting for # $round has " . (($start) ? 'started' : 'ended') . ', queued for: ' 
			. date('d-m-Y H:i:s',strtotime($datetime)));
}

function say($text){
	$_SESSION['admin_voting_msg'] = $text;
	redirect('dashboard');
	exit();
}

function redirect($pagename){
	header('Location: /admin/' . $pagename);
}