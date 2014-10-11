<?php
/**
 * Process GoB Xmas things.
 */
$type = $string = $out = '';
require_once('query.php');
require_once('gob_user.php');
require_once('functions.php');

if (isset ( $_GET ['type'] ) && strlen($_GET['type'])) {
	$params = explode ( '/', $_GET ['type'] ); // should convert something like: /xmas/team/name into this script $type = 'team' and $name = 'name';
	$type = array_shift($params);
	$type = strtolower(filter_var($type,FILTER_SANITIZE_FULL_SPECIAL_CHARS));
	$string = array_shift($params);
	if(!strlen($string)){
		$string = false;
	}	
} else {
	$type = 'list';
}
// X-Mas Router, we can process many things from this. 
$func = 'show_' . $type;
if(function_exists($func)){
	call_user_func($func,$string);
}else{
	switch ($type) {
		case 'album' :
			$out = "To be decided.";
			break;
		case 'vote' :
			//add_vote ( $string );
			$out = 'Currently not implemented.';
			break;
		default :
			$out = 'How did you get here?';
	}
}

/*********************************
 * /xmas/team/id
 * 
 * Displays details & controls for team
 * 
 * If a bandit is in the team, should show controls, otherwise, just details,
 * eventually, should show song player.
* *********************************
*/
function fail_team(){ echo "Invalid team identifier. I don't know what to say mate. " . get_issue_link("XM:FT:Invalid_Team"); }
function show_team($id) {
	$id = filter_var($id,FILTER_VALIDATE_INT);
	if(!$id) { 
		fail_team(); return;
	}
	$team_details = sql_to_array('SELECT * FROM xmas_teams WHERE id=' . $id . ' LIMIT 1');
	if(count($team_details) == 0){
		fail_team(); return;
	}
	$city_id = $team_details['city_id'];
	
	// Check if bandit is in team, if so, display control panel
	if(is_loggedin()){
		$team_members = sql_to_array('SELECT name FROM bandits WHERE xmas_team_id=' . $id);
		if(!$team_members){
			// We don't have any members yet.. who created this team?
			echo get_issue_link("XM:ST:Team members error.");
			exit(); 
		}
		$bandit = get_bandit_name();
		if(is_mod() || in_array($bandit,$team_members)){
			include_once('xmas_control_panel.php');
		}
	}
	
	// get song url, team info, city link, reddit link to city subreddit, etc..
	global $out;
	$team_name = get_one('SELECT name from xmas_teams WHERE id=:id',array('id'=>$id));
	$team_name = $team_name['name'];
	$out = '<h2>Team id: ' . $team_name . '</h2><ul class="team-list">'; 
	$out .= array_to_table($team_details); //TODO:!!!
	$out .='</ul>';
	if(is_loggedin()){
		if(!has_xmas_team()){
			// only show this to Bandits who are not in a team.
			$out .= '<a href="/xmas/join/' . $id . '" title="Want to join this team?">Join this team</a>';
		}
	}
}
/*********************************
 * /xmas/join/id
 * 
 * Allows bandit to join the team
 *********************************
 */
function show_join($id){
	set_xmas_team($id);
	// Redirect user to their new team
	header ( 'Location: /xmas/team/' . $id );
	echo 'Redirecting... <a href="/xmas/team/' . $id . '" title="Your new team!">here</a>';
	exit();
}
/*********************************
 * /xmas/city/id
 * 
 * Creates list of all teams associated with this city.
 *********************************
 */
function fail_city(){ echo "Invalid city identifier. " .  get_issue_link("XM:FC:Invalid_City"); }
function show_city($id) {
	global $out;
	$id = filter_var($id,FILTER_VALIDATE_INT);
	
	if(!$id) { 
		fail_city(); return; 
	}else{
		$a = array('id' => $id);
		$name = get_one('SELECT name FROM cities WHERE id=:id',$a);
		$name = $name['name'];
		if(!$name){ 
			fail_city(); return; 
		}
		$teams = sql_to_array("SELECT id,name FROM xmas_teams WHERE city_id=$id ORDER BY name ASC");
		$out = '<h2>Teams in ' . $name . '</h2>
				<ul class="team-list">'; 
		foreach ($teams as $t){
			$out.= '<li class="team"><a href="/xmas/team/' . $t['id'] . '" title="View team info">' . $t ['name'] . '</a></li>';
		}
		$out .='</ul>';
	}
}
/*********************************
 * The default output, a list of participating cities.
 * 
 * TODO: Find list of submitted songs and allow voting.. like normal homepage..
 *********************************
 */
function show_list() {
	global $out;
	// get all cities in database
	$cities = sql_to_array ( "SELECT id,name, (SELECT COUNT(id) FROM xmas_teams WHERE city_id = cities.id) as teams FROM cities HAVING teams > 0 ORDER BY name ASC" ); //add team count?
	get_template('xmas_list_heading');
	foreach ( $cities as $c ) {
		$out .= '<li class="city"><a href="/xmas/city/' . $c ['id'] . '" title="View city info">' . $c ['name'] . '</a> (' . $c['teams']. ')</li>';
	}
	$out . '</ul>';
}

/*********************************
 * JSON Wrapper; uses latitude & longitude and returns the 20 nearest teams 
 *********************************
 */
function show_jsonteams(){
	$teams = false;
	$lat = filter_input(INPUT_GET,'lat',FILTER_VALIDATE_FLOAT);
	$lng = filter_input(INPUT_GET,'lng',FILTER_VALIDATE_FLOAT);
	
	if(!$lat || !$lng)
		fail('No coordinates received, unable to process.');
	
	// Attempt to match via latitude/longitude : http://stackoverflow.com/a/574762
	// Modified to join on teams table compared to the city it's linked with.
	$teams = sql_to_array('
	SELECT x.id as tid,x.name as team,c.id as cid,c.name as city,
	( 6371 * 
		acos( 
			cos( radians(' . $lat .') ) 
		  * cos( radians(c.lat) ) 
		  * cos( radians(' . $lng .') - radians(c.lng) ) 
		  + sin( radians(' . $lat .') ) 
		  * sin( radians(c.lat)) ) 
	 )   AS distance 
	FROM cities c JOIN xmas_teams x ON x.city_id = c.id
	HAVING distance < 500 
	ORDER BY distance ASC
	LIMIT 0 , 20' // Find first 20 teams within 500 kms, ordered by closest
		); 
	
	if(!$teams){
		fail('No teams within range.',false,404);//not an error, a perfectly normal program state, just using fail to transmit the lack of team data
	}else{
		header("Content-type: application/json");
		echo json_encode(array('teams' => $teams));
		exit(); //must exit early, else the menu will get added.
	}
}
/*********************************
 * /xmas/create_team 
 * 
 * NEEDS WORK!
 * 
 * Should only be invoked via /find_team, as we need certain things before we can create it, and that script
 * is in charge of generating them.
 *********************************
 */
function fail_create_team(){ echo "Invalid inputs, please <a href=\"/xmas/find_team\">go back & try again</a> or "  . get_issue_link("XM:FT:Create_Team_Error"); }
function show_create_team(){
	loggedin_check();
	$team_name = filter_input(INPUT_GET,'team_name',FILTER_SANITIZE_STRING);
	$city_name = filter_input(INPUT_GET,'city_name',FILTER_SANITIZE_STRING);
	$lat = filter_input(INPUT_GET,'lat',FILTER_VALIDATE_FLOAT);
	$lng = filter_input(INPUT_GET,'lng',FILTER_VALIDATE_FLOAT);
	
	if(!$lat || !$lng || !$team_name || !$city_name){
		fail_create_team(); return;
	}
	
	// If the city isn't in the system yet, we should create it.
	$city_id = get_one('SELECT id FROM cities WHERE name=:name',array('name'=>$city_name));
	$city_id = $city_id['id'];
	if(!$city_id){
		$city_id = insert_query('INSERT INTO cities SET name=:name,lat=:lat,lng=:lng',array('name'=>$city_name,'lat'=>$lat,'lng'=>$lng));
	}
	// Create the team in db. (Ideally we would have a database abstraction to ensure success, but for now)
	$team_id = insert_query('INSERT INTO xmas_teams SET name=:name, city_id=:city_id',array('name'=>$team_name, 'city_id' => $city_id));
	
	// Join user to team
	show_join($team_id);
}

/**
 * /xmas/find_team 
 * 
 * with Google backed location Autocomplete, 
 * geocoding and browser-detection enabling 'closest teams'
 */
function show_find_team(){
	include('fragments/google_geocomplete.php');
	get_template('xmas_find_team');
}
/*********************************
 * Utilities
 *********************************
 */
/**
 * Adds a team ID to a bandit, and vis-a-versa
 * @param int $id
 * @return boolean
 */
function set_xmas_team($id){
	if(!$id)
		return false;
	
	if(!has_xmas_team()){
		$_SESSION['GOB']['xmas_team_id'] = $id;
		insert_query('UPDATE bandits SET xmas_team_id = ' . $id . ' WHERE name=:name LIMIT 1', array('name'=>get_username()));
	}
}
/**
 * Test if the current bandit has a team already
 * @return boolean
 */
function has_xmas_team(){
	// we know bandit has logged in at this point (well, we should always check before running this, but loggedin_check will force that.
	loggedin_check();
	if(isset($_SESSION['GOB']['xmas_team_id'])){
		return $_SESSION['GOB']['xmas_team_id'];
	}
	return false;
}


/********************************* 
 * Script output wrapper
 *********************************
 */
echo get_template('xmas_menu',true); //i.e: before the rest.
echo $out;	

