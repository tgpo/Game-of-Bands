<?php
// Xmas admin backends
// Which subreddit are we posting to? (change to gameofbands when live)
define ( 'SUBREDDIT', 'waitingforgobot' );

require_once ('../includes/gob_admin.php');
require_once ('../../src/query.php');

mod_check ();

$macros = array(
	'/{{gob}}/' => '[Game of Bands](http://gameofbands.co/xmas "Visit Game of Bands")',
	'/{{rgob}}/' => '[subreddit](http://reddit.com/r/gameofbands "Visit our subreddit")'
);

// we assume for now, that the mod hasn't put anything crazy into the fields.. :-(
$id = false;
if (isset ( $_GET ['type'] )) {
	if (DEBUG)
		error_log ( print_r ( $_POST, true ) );
	
	switch ($_GET ['type']) {
		case 'city' :
			{
				$params = array (
						'name' 				=> filter_input ( INPUT_POST, 'name', FILTER_SANITIZE_STRING ),
						'template_id' => filter_input ( INPUT_POST, 'template_id', FILTER_VALIDATE_INT ),
						'subreddit' 	=> filter_input ( INPUT_POST, 'subreddit', FILTER_SANITIZE_STRING ),
						'lat'					=> filter_input ( INPUT_POST, 'lat', FILTER_VALIDATE_FLOAT),
						'lng'					=> filter_input ( INPUT_POST, 'lng', FILTER_VALIDATE_FLOAT)
				);
				$sql = "INSERT INTO cities (id, name, subreddit, template_id, messaged_mods, post, created,lat,lng) VALUES (NULL,:name,:subreddit,:template_id,0,0, NULL,:lat,:lng)";
				$id = insert_query ( $sql, $params );
				break;
			}
		case 'template' :
			{
				$params = array (
						'title' 		=> filter_input ( INPUT_POST, 'title', FILTER_SANITIZE_STRING ),
						'text' 			=> filter_input ( INPUT_POST, 'text', FILTER_SANITIZE_STRING ) 
				);
				$id = insert_query ( "INSERT INTO templates (id, title,text) VALUES(NULL,:title,:text)", $params );
				break;
			}
		case 'delete' :
			{
				$table = filter_input ( INPUT_POST, 'type', FILTER_SANITIZE_STRING );
				if ($table == 'city') {
					$table = 'cities';
				} elseif ($table == 'template') {
					$table = 'templates';
				}else{
					fail("Doh! can't delete that.");
				}
				$id = filter_input ( INPUT_POST, 'id', FILTER_VALIDATE_INT );
				if(!is_numeric($id))
					fail("Invalid id.");
				
				pdo_query ( "DELETE FROM $table WHERE id=:id LIMIT 1", array ('id' => $id ), false );
				break;
			}
		case 'modmessage' :
			{
				fail ( "TODO!" ); //TODO: This could be better.. and functional!
				require_once ('../../lib/reddit.php');
				modmessage ( new reddit ( $reddit_user, $reddit_password ) );
				break;
			}
		case 'postthread' :
			{
				fail ( 'TODO!' );
			}
		case 'concept':
			{
				$text = json_decode($_POST['text']);
				// Save the text into the filesystem, no need for databse for everything!
				file_put_contents(dirname(__FILE__).'/../../src/fragments/xmas_concept.inc',$text);
				ok();
			}
		case 'changed' :
			{
				fail(print_r($_POST,true));
				/****: TESTING
				$params = array(
					'city' => filter_input(INPUT_POST,'city',FILTER_SANITIZE_STRING),
					'template_id' => filter_input(INPUT_POST,'template_id',FILTER_VALIDATE_INT),
				);
				$id = insert_query('UPDATE cities SET template_id=:template_id WHERE id=:city',$params);
				*/
			}
	}
}

/**
 * Send a message to the moderators of a subreddit.
 * According to reddit, its just name of subreddit prepended with a #. So, #gameofbands will message GOB mods.
 * 
 * @param reddit $reddit        	
 */
function modmessage($reddit) {
	$id = filter_input ( INPUT_POST, 'id', FILTER_SANITIZE_INT );
	$city = sql_to_array( 'SELECT template_id FROM cities WHERE id=' . $id);
	$template = sql_to_array ( 'SELECT title,text FROM templates WHERE id=' . $city['template_id']);
	$to = '#' . $city ['subreddit'];
	if(!(strlen($to) || strlen($template['title']) || strlen($template['text']))){
		fail("Invalid recipient, title or text.. ");
	}
	insert_query('UPDATE cities SET messaged_mods = 1 WHERE id=' . $id . ' LIMIT 1');
	ok ( json_encode($reddit->sendMessage ( $to, $template ['title'], $template ['text'] ) ) );
}

if (is_numeric ( $id )) {
	ok ( 'Done', $id );
} else {
	fail ( 'Doh.' );
}

function process_macros($text){
	global $macros;
	//TODO: Look for {{template# and grab the id number before the }}, grab the template text from database.
	$matches = array();
	if($matches = preg_match('/{{template#(\d)}}', $text, $matches)){
		$id = $matches[0];
		$text = get_one('SELECT text FROM templates WHERE id=' . $id)['text'];
	}
	return preg_replace(array_keys($macros), array_values($macros), $text);
}
function set_team_macro($team,$id){
	set_macro('team',"[$team](http://gameofbands.co/team/$id \"Visit $team's page\")");
}
function set_city_macro($city,$reddit_url){
	set_macro('city',$city);
	set_macro('rcity',$reddit_url);
}
function set_charity_marco($charity){
	set_macro('charity',$charity);
}
function set_macro($name,$text){
	global $macros;
	$macros['/{{' . $name . '}}/'] = $text;
}
