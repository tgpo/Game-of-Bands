<?php
/**
 *  When a user clicks on a link with class "song", it will send the songs ID number here, 
 *  we must return the details of the song & the theme of the round in JSON format.
 */

require_once ('query.php');

define('CACHE_TYPE','json');
include('cacheme.php');

require_once ('../classes/class.round.php');
require_once ('../classes/class.song.php');

$song = filter_input ( INPUT_GET, 'song', FILTER_VALIDATE_INT );
if (! $song) {
	fail('Invalid Song ID.');
}else{
	$s = new Song($song);
	$info = $s->getInfo();
	if(strlen($info['name'])<1){
		fail("Invalid Song ID");
	}
	$info['theme'] = Round::get_theme($s->get_round());
	
	// Enables voting if active for round.
	if(voting_is_active($s->get_round())){
		$info['votable'] = 1;
		// Show different icon if user has already voted for things.
		if(is_loggedin()){
			$info['voted'] = get_song_votes(get_username(),$song);
		}
	}else{
		$info['votable'] = 0;
	}
	
	$response = json_encode($info);
	
	check_json(); //verify that json_encoding hasn't b0rked.
	
	print $response . PHP_EOL;
}
exit();

/* old code..
$db = database_connect ();
$query = $db->prepare ( 'SELECT * FROM songs WHERE id=:song and approved=1' );
$query->execute ( array (
		'song' => $song 
) );
$song = $query->fetch ();

$query = $db->prepare ( 'SELECT theme FROM rounds WHERE number=:round' );
$query->execute ( array (
		'round' => $song ['round'] 
) );
$round = $query->fetch ();
function fixAscii($string) {
	$map = Array (
			'’' => "'" 
	);
	
	$search = Array ();
	$replace = Array ();
	
	foreach ( $map as $s => $r ) {
		$search [] = $s;
		$replace [] = $r;
	}
	
	return str_replace ( $search, $replace, $string );
}

//header ( $_SERVER ['SERVER_PROTOCOL'] . ' ', true, 200 ); // Browsers like the 200 success code.
$response = json_encode ( array (
		"name" => $song ['name'],
		"url" => $song ['url'],
		"round" => $song ['round'],
		"lyricsheet" => fixAscii ( nl2br ( $song ['lyricsheet'] ) ),
		"theme" => $round ['theme'],
		"banditLyrics" => $song ['lyrics'],
		"banditMusic" => $song ['music'],
		"banditVocals" => $song ['vocals'] 
) );
*/