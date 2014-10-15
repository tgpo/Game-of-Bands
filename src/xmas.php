<?php
/**
 * Process GoB Xmas things.
 */
$type = $string = $out = '';
$here = dirname(dirname(__FILE__));
set_include_path( get_include_path(). ':' . $here   . ':' . $here .'/src/' . ':' . $here . '/classes/');

require_once('query.php');
require_once('gob_user.php');
require_once('functions.php');
require_once('class.xteam.php');
require_once('class.city.php');

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
		fail_team();
		return;
	}
	$team = new XmasTeam($id);

	if(!is_object($team)){
		fail_team();
		return;
	}
	$city_id = $team->getCityId();
	$city = new City($city_id);
	$city_name = $city->getName();

	$creator = $team->getCreatorName();

	// Get list of team members.
	$team_members = $team->getTeam();

	// Check if bandit is in team, if so, display control panel
	if(is_loggedin()){
		if(!$team_members){
			// We don't have any members yet.. who created this team?
			echo get_issue_link("XM:ST:Team members error.");
			// Either way, we know this guy isn't in the team, as NOBODY IS.
		}
		if($team->inTeam(get_bandit_name())){
			include_once('xmas_control_panel.php');
		}
	}

	// get song url, team info, city link, reddit link to city subreddit, etc..
	global $out;
	$out = '<h2>Team: ' . $team->getName() . "</h2>
	<hr>
	<h3>This team is based in <a href=\"/xmas/city/{$city->getId()}\">{$city->getName()}</a></h3>
	<p>Team Creator: " . a_bandit($team->getCreatorName()). " </p>
	<p>Current members are: </p>
	<ul>";
	foreach($team_members as $t){
		$out .= '<li>' . a_bandit($t) . '</li>';
	}
	$out .= '</ul>' ;
	if($team->hasCharity()){
		$out .= '<h3>Partial proceeds of this teams share of album sales will be sent directly to ' . $team->getCharity();
	}
	if($team->hasUrl())
		$out .= '<p>Team song: <a href="#" title="Would be a listen link with the widget..">Listen</a></p>';
	$out .= '<p>Team created: UTC(' . $team->created() .')</p>';
	if(is_loggedin()){
		if(!get_xmas_team()){
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
	global $out;
	set_xmas_team($id);
	$out .= '<h1>You Joined!</h1>';
	show_team($id);
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

		$city = new City($id);

		if(!$city->getName()){
			fail_city(); return;
		}
		$teams = XmasTeam::getList();
		$out = '<h2>Teams in ' . $city->getName() . '</h2>
				<ul class="team-list">';
		foreach ($teams as $t){
			$out.= '<li class="team"><a href="/xmas/team/' . $t['id'] . '" title="View team info">' . $t ['name'] . '</a></li>';
		}
		$out .='</ul>';
		// Output a tiny script containing the coordinates.
		echo '<script type="text/javascript">var coordinates = {lat:' . $city->lat() . ',lng:' . $city->lng() .'};</script>';
		get_template('google_maps_template');

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
	$cities = City::getList();
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
	$lat = filter_input(INPUT_GET,'lat',FILTER_VALIDATE_FLOAT);
	$lng = filter_input(INPUT_GET,'lng',FILTER_VALIDATE_FLOAT);

	$teams = XmasTeam::find_teams($lat,$lng);

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

	$team = XmasTeam::create_team();

	// Join user to team
	set_xmas_team($team->getId());

	echo '<h1>Team ' . $team->getName() . ' Created!</h1>';

	show_team($team->getId);
}

/**
 * /xmas/find_team
 *
 * with Google backed location Autocomplete,
 * geocoding and browser-detection enabling 'closest teams'
 */
function show_find_team(){
	global $out;
	include('fragments/google_geocomplete.php');
	$prefilled_city_name = '';
	if(isset($_SERVER['HTTP_REFERER'])){
		// Someone came to us from some other site, lets pull the subreddit from the url and attempt to match it.
		$ref = parse_url($_SERVER['HTTP_REFERER']);
		error_log("REFERRER ROUND: " . $ref['path']);
		$ref = str_replace('/r/','',$ref['path']);
		$ref = preg_replace('#([a-zA-Z_]*)/.*#', '\1', $ref); // we should have just the subreddit id now.
		error_log("Looking for reddit: " . $ref);
		// See if we have a match
		$city = get_one('SELECT * FROM cities WHERE subreddit=:subreddit',array('subreddit'=>$ref));
		if($city['subreddit']){
			// send the city name to the drop-down box and submit it.
			$prefilled_city_name = $city['name'];
		}
	}
	include('fragments/xmas_menu.inc');
	echo $out;
	include('fragments/xmas_find_team.inc');
	exit();//due to includes, we have to exit.
}

function show_charity($id){
	die('Incomplete.');
	$id = filter_var($id,FILTER_VALIDATE_INT);
	if(!$id){
		die('Unknown charity.');
	}
	$charity = pdo_query('SELECT * FROM charities WHERE id=:id LIMIT 1',array('id'=>$id));
	print_r($charity);//TODO: Not sure if we even need to display this stuff at all..
}

function show_jsonsetcharity(){
	$bandit_id = (DEBUG) ? DEBUG_USER_ID : bandit_id();
	$team_id = (DEBUG) ? 2 : get_xmas_team();
	if(!$team_id){
		fail("How did you get here?");
	}
	$eid = filter_input(INPUT_GET,'existing_id',FILTER_VALIDATE_INT);
	if($eid > 0){
		//use id number of existing charity id on current team.
		insert_query('UPDATE xmas_teams SET charity_id=:cid WHERE id=:id LIMIT 1',array('id'=>$team_id, 'cid'=>$eid));
	}else{
		$p = array();
		$p['charity_id'] = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
		$p['name'] 	= filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING);
		$p['loc'] 	= filter_input(INPUT_GET,'locality',FILTER_SANITIZE_STRING);
		$p['email'] = filter_input(INPUT_GET,'email',FILTER_SANITIZE_STRING);
		$p['mod_id'] = $bandit_id;

		foreach($p as $k => $v){
			if(strlen($v)==0){
				fail("Invalid paramenter: $k");
			}
		}

		// Check for existing charities? Existing nominations? //TODO:

		$iid = insert_query("INSERT INTO charities (name,locality,email,charity_id,status,mod_id)
				VALUES (:name, :loc, :email, :charity_id, 'nominated', :mod_id)", $p);
		if($iid){
			insert_query('UPDATE xmas_teams SET nominated_charity=:charity_id WHERE id=:id LIMIT 1',
			 array('id'=> $team_id, 'charity_id'=>$iid));
		}else{
			fail("Failed to insert into charities.");
		}

		ok();
	}
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

	if(!get_xmas_team()){
		$_SESSION['GOB']['xmas_team_id'] = $id;
		insert_query("UPDATE bandits SET xmas_team_id=:id, xmas_team_status='pending' WHERE name=:name LIMIT 1", array('id'=>$id,'name'=>get_username()));
	}
}
/**
 * Test if the current bandit has a team already
 * @return boolean
 */
function get_xmas_team(){
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

