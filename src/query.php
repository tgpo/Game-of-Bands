<?php

define ( 'DEBUG', (gethostname() == 'gameofbands.co') ? false : true ); //IMPORTANT: SET TO FALSE ON SERVER
define ( 'DEBUG_USER', 'RetroTheft' );
define ( 'DEBUG_VOTING', false ); // set to true to always enable voting, and see all votes in error log.
define ( 'DEBUG_SQL', true ); // Set to true to see all queries in the apache log.

require_once ('secrets.php');
require_once ('gob_user.php');
$db = false;
/* ************************************************************************
 Database access
************************************************************************ */
function database_connect() {
	global $mysql_user, $mysql_password, $mysql_db; // from secrets.php
	//$dsn = "mysql:dbname=$mysql_db;host=localhost;charset=utf8;"; //host=localhost;
	//$class = 'PDO';
	// if(!class_exists('PDO')){
	$lib = dirname(__FILE__) . '/../lib/phppdo-1.4/';
	include_once $lib . 'phppdo.php';
	$class = 'PHPPDO';

	//  }

	// Use real prepared statements. See <http://stackoverflow.com/a/60496/403805>
	// $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	try{
		$db = new $class('', $mysql_user, $mysql_password); //DSN has been hard-coded.. stupid PHPPDO couldn't parse it.. 
	}catch (PDOException $e){
		error_log($e->getMessage());
		mail('clonemeagain@gmail.com','GOB DB Error',$e->getMessage());
		die("We are having a slight problem with our database, the admins have been notified, but if you want to add additional info about what happened: " . get_issue_link('Q:DBC_Unable_to_connect'));
	}
	return $db;
}

// Query the  rounds  table for a particular round number
function query_round_details($db,$number) {
	$query = $db->prepare('SELECT * FROM rounds WHERE number=:number');
	$query->execute(array('number' => $number));
	return $query->fetch();
}


/* ************************************************************************
 Table display
************************************************************************ */
// Display a collection of songs.
function display_songs($result) {
	echo "<table id='songtable' class='sortTable'>";
	echo "<thead><tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Music</th><th>Music Vote</th><th>Lyrics</th><th>Lyrics Vote</th><th>Vocals</th><th>Vocals Vote</th></tr></thead><tbody>";
	foreach ($result as $row) {
		tr_song($row);
	}
	echo "</tbody></table>";
}

// Display a particular song as a row
function tr_song($row) {
	echo "<tr>";
	echo '<td class="round">' . a_round($row['round'],$row['round']) . "</td>";
	echo '<td class="songname">' . a_song($row)  . "</td>";
	echo '<td class="songvotes">' . $row['votes'] . "</td>";
	td_bandit('music' ,$row);
	td_bandit('lyrics',$row);
	td_bandit('vocals',$row);
	echo "</tr>";
}

// make two <td> tags for a bandit's name
function td_bandit($type, $row) {
	echo '<td class="' . $type . 'name">' . a_bandit($row[$type]) . "</td>";
	echo '<td class="' . $type . 'votes">' . $row[$type.'vote']    . "</td>";
}

/* ************************************************************************
 Making links
************************************************************************ */
function a_bandit($name) {
	return "<a class='banditname' href='/bandit/".$name."'>".$name."</a>";
}
function a_round($number,$name) {
	return "<a class='round' href='/round/".$number."'>".$name."</a>";
}
function a_round_details($details) {
	return "<a class='round' href='/round/".$details['number']."'> Round ".$details['number']." - ".$details['theme']."</a>.";
}
function a_song($row) {
	return '<a class="song" 
			data-url="' . ((strlen($row ['url'])) ? $row ['url'] : '#') 
	. '" data-id="' . $row ['id'] 
	. '" href="/song/' . $row ['id'] 
	. '" title="Listen to this song">' . $row ['name'] . "</a>";
}


/*** New code **/

function pdo_query($sql, $params = array(), $expect_response = true) {
	global $db;
	if (! $db instanceof PDO) {
		$db = database_connect ();
	}
	$data = array ();
	if (DEBUG_SQL)
		error_log ( "pdo_query: $sql" . print_r ( $params, true ) );
	
	try {
		$stmt = $db->prepare ( $sql );
		$stmt->execute ( $params );
		
		if ($expect_response && $stmt->rowCount ()) {
			if ($stmt->rowCount () > 1) {
				while ( $a = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
					$data [] = $a;
				}
				return $data;
			} else {
				return $stmt->fetch ( PDO::FETCH_BOTH );
			}
		}
		$stmt = null;
	} catch ( PDOException $e ) {
		error_log ( $e->getMessage () ); // we don't display this.
		return false;
	} catch ( Exception $e ) {
		error_log ( "NonPDO Exception!: " . $e->getTraceAsString () );
	}
}
/**
 * Inserts data, returns lastInsertID
 * 
 * @param string $sql        	
 * @param array $params
 *        	associative array of parameters for PDO query.
 * @return int
 */
function insert_query($sql, $params = false) {
	global $db;
	// make the query, get the freshly inserted row's ID number
	pdo_query ( $sql, $params, false );
	return $db->lastInsertId ();
}
/**
 * Appends "LIMIT 1" to any query sent its way, returns the first result only.
 * RETURNS ARRAY, keyed to column, so yes, you can do:
 * print get_one("SELECT COUNT(*) as a FROM thing")['a'];
 * ERROR: This only works on PHP5.4+.. not on the version on gameofbands.co current server!
 * 
 * @param string $sql        	
 * @param array $params        	
 * @return Ambigous <boolean, multitype:unknown , mixed>
 */
function get_one($sql, $params = array()) {
	return pdo_query ( "$sql LIMIT 1", $params, true );
}

// MySQL Helper Functions
function get_bandit_id($bandit_name = false) {
	if(!$bandit_name){
		return bandit_id(); // Assumes called with no input, run against current user.
	}
	
	$b = get_one ( 'SELECT id FROM bandits WHERE name=:bandit', array (
			'bandit' => $bandit_name 
	) );
	if (is_array ( $b ) && isset ( $b ['id'] )) {
		return $b ['id'];
	}
	return false;
}
function bandit_made_song($bandit_name, $song_id) {
	$team = get_one ( "SELECT music,lyrics,vocals FROM songs WHERE id=:song", array (
			'song' => $song_id 
	) );
	if (in_array ( $bandit_name, $team )) {
		return true;
	}
	return false;
}

/**
 * Determine if a song participant is a bandit (ie, have they ever logged into the system)
 * @param unknown $name
 * @return boolean
 */
function bandit_name_exists($name) {
	$n = get_one('SELECT name FROM bandits WHERE name=:name',array('name'=>$name));
	return $n['name'] == $name;
}

/**
 * Calculate how many songs this bandit has participated in.
 * Also useful for testing names to see if they are in the songs list (0 songs = lazy-false, >0 = lazy-true)
 * @param string $name
 * @return int number of songs participated in.
 */
function bandit_song_count($name){
	$l = pdo_query('SELECT COUNT(*) as p FROM songs WHERE lyrics=:a OR music=:b OR vocals =:c',array('a'=>$name,'b'=>$name,'c'=>$name));
	return $l['p'];
}

/**
 * Determine if now is within the bounds of current round_start and round_end
 * 
 * @param
 *        	int (Which round to check if active)
 * @return boolean
 */
function voting_is_active($round = false) {
	if (DEBUG_VOTING)
		return true;
	$round = (is_numeric ( $round )) ? $round : get_latest_round_id ();
	$r = get_one ( 
			'
			SELECT COUNT(*) as active 
			FROM rounds 
			WHERE number = ' . $round . ' 
			AND NOW() >= start AND NOW() <= end ' );
	return ($r ['active'] == 1) ? true : false;
}
function get_latest_round_id() {
	// Rounds are only truly active when they have a Signup Thread on Reddit.
	$a = get_one ( 
			'
			SELECT number 
			FROM rounds 
			WHERE signupID IS NOT NULL 
			AND signupID <> "NULL" 
			ORDER BY number DESC' );
	return $a['number'];
}

/**
 * Attempt to figure out what would cause a problem with the JSON, it seemed to work .
 * . @ random.
 * Found UTF8 Errors when query.php/connect_to_db()'s connection string doesn't include ";charset=utf8"
 * These errors can be printed into the console as they show up as complete: status.
 * http://stackoverflow.com/q/4361459 and php.net/json_last_error etc.
 */
function check_json() {
	$msg = '';
	switch (json_last_error ()) {
		case JSON_ERROR_NONE :
			break;
		case JSON_ERROR_DEPTH :
			$msg = ' - Maximum stack depth exceeded';
			break;
		case JSON_ERROR_STATE_MISMATCH :
			$msg = ' - Underflow or the modes mismatch';
			break;
		case JSON_ERROR_CTRL_CHAR :
			$msg = ' - Unexpected control character found';
			break;
		case JSON_ERROR_SYNTAX :
			$msg = ' - Syntax error, malformed JSON';
			break;
		case JSON_ERROR_UTF8 :
			$msg = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
		default :
			$msg = ' - Unknown error';
			break;
	}
	if (strlen ( $msg ) > 0) {
		error_log ( "JSON Error: $msg" );
	}
}
function format_date($datetime) {
	return date ( 'd-m-y H:i:s', strtotime ( $datetime ) );
}
function has_round_started($round) {
	return ($round ['start'] !== "0000-00-00 00:00:00");
}
function has_round_ended($round) {
	return ($round ['end'] !== "0000-00-00 00:00:00");
}
function is_checked($true = true) {
	return ($true) ? 'checked="checked"' : '';
}

/**
 * Callback function, use like array_map('fix_quotes',$array_which_needs_fixed);
 * 
 * @param unknown $text, Either string or array of strings, returns same type. 	
 * @return mixed
 */
function fix_quotes($text) {
	$bad_chars = array('â','€','™','`');
	return str_replace ( $bad_chars, "'", $text );
}


function get_song_votes($bandit, $song_id) {
	$bandit_id = get_bandit_id ( $bandit );
	$votes = pdo_query ( "SELECT type from votes WHERE banditID=:bandit AND songID=:song", 
			array (
					'song' => $song_id,
					'bandit' => $bandit_id 
			) );
	$return = array ();
	foreach ( $votes as $v ) {
		$return [] = $v ['type'];
	}
	return $return;
}

/**
 * Sends a JSON error message as defined by $string
 * 
 * @param string $string        
 * @param mixed $element_id some data you want to send back, originally designed for specifying the element ID of an html element, to identify it via jQuery.
 * @param number $code        	
 */
function fail($string = '', $element_id = false, $code = 500) {
	error_log ( "FAIL: $string" );
	header("Content-type: application/json");
	header ( $_SERVER ['SERVER_PROTOCOL'] . " $code Internal Server Error", true, $code );
	$data = array();
	$data['msg'] = $string;
	if($element_id){
		$data['element_id'] = $element_id;
	}
	$response = json_encode ( $data) ; 
	print $response . PHP_EOL;
	exit ();
}
function ok($message = 'Success!', $element_id = false, $code = 200) {
	header("Content-type: application/json");
	header ( $_SERVER ['SERVER_PROTOCOL'] . ' ', true, $code );
	$response = json_encode ( array (
			'msg' => $message,
			'element_id' => (($element_id) ? $element_id : '') 
	) );
	print $response . PHP_EOL;
	exit ();
}

/**
 * Converts an SQL query into an associative array.
 * USE NAMED FIELDS! as * will probably fail.. :-(
 * 
 * @param string $sql        	
 * @return boolean|array
 */
function sql_to_array($sql) {
	if (! $sql)
		return false;
	
	global $db;
	
	if (($db instanceof PDO) || ($db instanceof PHPPDO)) {
		if(DEBUG_SQL) error_log("Already connected to database...");
	}else{
		if(DEBUG_SQL) error_log("Connecting to database...");
		$db = database_connect ();
	}
	
	if(DEBUG_SQL)
		error_log($sql);
	
	$stmt = $db->prepare ( $sql );
	$stmt->execute ();
	return $stmt->fetchAll ( PDO::FETCH_ASSOC );
}

/**
 * Converts an sql query into an HTML table.
 * 
 * @param string $sql        	
 * @return string HTML table formatted output of the query.
 */
function sql_to_table($sql, $table_id='', $replacementHeaders = false) {
	if (! $sql)
		return false;
	return array_to_table ( sql_to_array ( $sql ), $table_id, $replacementHeaders );
}

/**
 * Convert associative array into html table.
 * 
 * @param array $arrayofassoc        	
 * @return string html formatted table
 */
function array_to_table($arrayofassoc=false, $table_id='', $replacementHeaders=false) {
	if (! $arrayofassoc)
		return '';
	
	$headers = (count ( $replacementHeaders )) ? $replacementHeaders : array_keys ( $arrayofassoc [0] );
	
	$tablestr = '<table id="' . $table_id . '">';
	$tablestr .= table_row ( $headers, true ) . "\n";
	$tablestr .= implode ( '', array_map ( 'table_row', $arrayofassoc ) );
	$tablestr .= '</table>';
	return $tablestr;
}

/**
 * Formats a cell of data as either td or th, converts contents into html compatible strings and returns them in elements.
 * 
 * @param unknown $item        	
 * @param string $header        	
 * @return string
 */
function table_cell($item, $header = false) {
	// In order to display table spaced correctly, this just uses a space for null/empty values.
	if (! $item)
		$item = ' ';
	$elemname = ($header) ? 'th' : 'td';
	$escitem = strip_tags ( $item, '<a>' );
	return "<{$elemname}>{$escitem}</{$elemname}>";
}

/**
 * Function created for array_map purposes.
 * 
 * @param unknown $item        	
 * @return string
 */
function table_header_cell($item) {
	return table_cell ( $item, true );
}

/**
 * Needs to be a function for array_map calls
 * 
 * @param array $items        	
 * @param string $header        	
 * @return string
 */
function table_row($items, $header = false) {
	$func = ($header) ? 'table_header_cell' : 'table_cell';
	return (is_array ( $items )) ? '<tr>' . implode ( '', array_map ( $func, $items ) ) . "</tr>\n" : '';
}

/**
 * Execute a request (with curl), was needed for something, but the implementation changed. 
 * 
 * @param string $url
 *        	URL
 * @param mixed $parameters
 *        	Array of parameters
 * @return html
 */
function get_remote_url($url, $parameters = false) {
	if (is_array ( $parameters )) {
		$url .= '?' . http_build_query ( $parameters, null, '&' );
	} elseif ($parameters) {
		$url .= '?' . $parameters;
	}
	
	$ch = curl_init ();
	curl_setopt_array ( $ch, array (
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $http_method 
	) );
	// https handling
	// bypass ssl verification
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 1 );
	$result = curl_exec ( $ch );
	//$http_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	//$content_type = curl_getinfo ( $ch, CURLINFO_CONTENT_TYPE );
	if ($curl_error = curl_error ( $ch )) {
		throw new Exception ( $curl_error );
	}
	curl_close ( $ch );
	
	return $result;
}

/**
 * Creates a GitHub issue link
 * @param string $text The reference to the error, defaults to "Unknown error"
 * @return string html link
 */
function get_issue_link($text = ''){
	$text = ($text) ? $text : ' Unknown error';
	return '<a href="https://github.com/clonemeagain/Game-of-Bands/issues/new" title="Submit an issue to the developers to help us fix this sooner">Please submit an issue for this fault, REF: ' . $text . '</a>';
}
