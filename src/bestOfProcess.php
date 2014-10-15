<?php
require_once ('secrets.php');
require_once ('query.php');
require_once ('gob_user.php');

if (!(DEBUG || is_loggedin())){
	fail("Not logged in.");
}
	//loggedin_check ( 'login.php' ); //redirects to login.. not graceful over ajax.

if (! voting_is_active ()) {
	fail ( "Please wait for the next voting round to begin." ); // TODO: Find out when next voting round begins: .. 'in 2 days 14 minutes' etc.?
}

if (isset ( $_POST ['voteSong'] )) {
	voteSong (); // Original bestOf2013 code
} elseif (isset ( $_POST ['vote'] )) {
	vote (); // New code.
}
function voteSong() {
	$db = database_connect ();
	
	$bandit = get_username ();
	$catagory = filter_input ( INPUT_POST, 'catagory', FILTER_SANITIZE_SPECIAL_CHARS );
	$vote = filter_input ( INPUT_POST, 'vote', FILTER_SANITIZE_SPECIAL_CHARS );
	
	$validColumns = array (
			'bestSong',
			'bestLyricist',
			'bestMusician',
			'bestVocalist',
			'bestSave',
			'underAppreciatedSong',
			'underAppreciatedBandit',
			'bestApplicationRound',
			'bestXmasSong',
	);
	
	if (! in_array ( $catagory, $validColumns )) {
		throw new Exception ( 'Not a valid column name.' );
	}
	
	// Check if this user has already submitted a nomination
	// If so, update rather than add a new row
	$query = $db->prepare ( 
			"SELECT * FROM finalBestof2013 WHERE bandit = :bandit" );
	$query->execute ( array (
			'bandit' => $bandit 
	) );
	$row = $query->fetch ();
	
	if (! $row ['bandit']) {
		$query = $db->prepare ( 
				'INSERT INTO finalBestof2013 (bandit, ' . $catagory .
						 ') VALUES (:bandit, :vote)' );
		$query->execute ( 
				array (
						'bandit' => $bandit,
						'vote' => $vote 
				) );
	} else {
		$query = $db->prepare ( 
				'UPDATE finalBestof2013 SET ' . $catagory .
						 '=:vote WHERE bandit=:bandit' );
		$query->execute ( 
				array (
						'vote' => $vote,
						'bandit' => $bandit 
				) );
	}
}
// Allows all types of voting.
function vote() {
	$bandit = get_username (); 
	                                                    
	// Parse the vote 
	$vote = json_decode ( $_POST ['vote'] );
	
	// Sanitize
	$type = (isset ( $vote->type ) && is_string ( $vote->type ) &&
			 strlen ( $vote->type ) < 23) ? $vote->type : false;
	$round = (isset ( $vote->round ) && is_numeric ( $vote->round ) &&
			 strlen ( $vote->round ) < 11) ? $vote->round : false; // 11 is based on integer database field size..
	$song = (isset ( $vote->song ) && is_string ( $vote->song ) &&
			 strlen ( $vote->song ) < 11) ? $vote->song : false;
	$element_id = (isset($vote->element_id)) ? $vote->element_id : false;
	
	if(DEBUG_VOTING) error_log ( "Voter: $bandit, Type: $type, Round: $round, Song: $song, Element: $element_id" ); //uncomment to see all votes in server log.
	
	if (! $round || ! $type || ! $song) {
		fail ( "Invalid input.", $element_id, 404 );
	}
	
	$validTypes = array (
			'bestSong',
			'bestLyricist',
			'bestMusician',
			'bestVocalist',
			'bestProducer',
			'bestSave',
			'underAppreciatedSong',
			'underAppreciatedBandit',
			'bestApplicationRound',
			'bestXmasSong',
	);
	// Only accept specific types, database will do this anyway (ENUM), but its worth checking again.. I suppose.
	if (! in_array ( $type, $validTypes )) {
		fail ( "Vote type invalid.", $element_id, 404 );
	}
	
	// get the bandits ID
	$bandit_id = get_bandit_id ( $bandit );
	if (! $bandit_id) {
		fail ( "Who said that?", $element_id,  404 );
	}
	
	// Setup array of parameters used to insert/delete votes.
	$params = array (
			'type' => $type,
			'bandit' => $bandit_id,
			'round' => $round 
	);
	
	if (bandit_made_song ( $bandit, $song )) {
		fail ( 'You are unable to vote for your own song.', $element_id, 403 );
	}
	
	$success_message = 'Success!';
	
	// Delete any previous matching vote, only one vote per round per type will be kept.
	$previous_vote = pdo_query ( 
			"SELECT *, count(*) as number FROM votes WHERE type=:type AND banditID=:bandit AND roundID=:round", 
			$params );
	if(DEBUG)
		error_log ( "Previous votes found: " . print_r ( $previous_vote, true ) );
	
	if ($previous_vote ['number'] > 0) {
		pdo_query ( 
				"DELETE FROM votes WHERE type=:type AND banditID=:bandit AND roundID=:round", 
				$params, false );
		$success_message = 'Your vote for: ';
		// Figure out who they voted for, as tables aren't normalised, we can't assume the team-member is actually a bandit.
		$bandit_table_type = '';
		switch ($type) {
			case 'bestSong' :
				break;
			case 'bestMusician' :
				$bandit_table_type = 'music';
				break;
			case 'bestVocalist' :
				$bandit_table_type = 'vocals';
				break;
			case 'bestLyricist' :
				$bandit_table_type = 'lyrics';
				break;
		}
		if ($type !== 'bestSong') {
			$bandit_table_type .= ' as a,';
		}
		// TODO: make this less "shit".
		$that_bandit = pdo_query ( 
				"SELECT $bandit_table_type name FROM songs WHERE id=" .
						 $previous_vote ['songID'] . " LIMIT 1" );
		// Notify user that vote has changed.
		$success_message .= (isset($that_bandit['a'])) ? $that_bandit ['a'] . ' in' : '';
		
		$success_message .= ' category ' . $type . ' for song ' . $that_bandit ['name'] . ' in round ' . $round . ' has been replaced with your new vote.';
	}// END Previous Vote
	
	// Song only relates to new vote.
	$params ['song'] = $song;
	// Save new vote.
	if (insert_query ( 
			"INSERT INTO votes (type,banditID,roundID,songId) VALUES (:type,:bandit,:round,:song)", 
			$params )) {
		ok ( $success_message, $element_id  );
	}
	fail ( "DOH!: Don't know what happened here.. contact us for support.", $element_id  );
}
// Default is to fail.. 
fail ( "An unknown error occurred."  );
?>