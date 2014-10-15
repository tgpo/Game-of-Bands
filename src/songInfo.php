<?php
/**
 *  When a user clicks on a link with class "song", it will send the songs ID number here, 
 *  we must return the details of the song & the theme of the round in JSON format.
 */
$here = dirname(__FILE__);
require_once ($here .'/query.php');

define('CACHE_TYPE','json');
include($here .'/cacheme.php');

require_once ($here .'/../classes/class.round.php');
require_once ($here .'/../classes/class.song.php');

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
	
	/*/ Enables voting if active for round.
	if(voting_is_active($s->get_round())){
		$info['votable'] = 1;
		// Show different icon if user has already voted for things.
		if(is_loggedin()){
			$info['voted'] = get_song_votes(get_username(),$song);
		}
	}else{*/
		$info['votable'] = 0;// change to using jQuery data on applicable rounds.. 
	//}
	
	$response = json_encode($info);
	
	check_json(); //verify that json_encoding hasn't b0rked.
	
	print $response . PHP_EOL;
}
exit(); // Just in case this isn't called directly.. 
