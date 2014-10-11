<?php
// Xmas admin backends relating to the Cities.

// define which subreddit are we posting from? (change to gameofbands when live)
define ( 'SUBREDDIT', 'waitingforgobot' );

//note: set DEBUG mode in query.php in order to test sending functions, all reddit messages/threads are redirected to waitingforgobot.

require_once ('../includes/gob_admin.php');
require_once ('../../src/query.php');

mod_check (); //important, some of this is pretty powerful/flexible.. in other words dangerous.


// we assume for now, that the mod hasn't put anything crazy into the fields.. :-(
$id = false;
if (isset ( $_GET ['type'] )) {
	if (DEBUG)
		error_log ( print_r ( $_POST, true ) );
	
	switch ($_GET ['type']) {
		case 'city' : // Create new city
			{
				$params = array (
						'name' 					=> filter_input ( INPUT_POST, 'name', FILTER_SANITIZE_STRING ),
						'message_template_id' 	=> filter_input ( INPUT_POST, 'message_template_id', FILTER_VALIDATE_INT ),
						'post_template_id' 		=> filter_input ( INPUT_POST, 'post_template_id', FILTER_VALIDATE_INT ),
						'subreddit' 			=> filter_input ( INPUT_POST, 'subreddit', FILTER_SANITIZE_STRING ),
						'lat'					=> filter_input ( INPUT_POST, 'lat', FILTER_VALIDATE_FLOAT),
						'lng'					=> filter_input ( INPUT_POST, 'lng', FILTER_VALIDATE_FLOAT)
				);
				$sql = "INSERT INTO cities (id, name, subreddit, message_template_id,post_template_id, messaged_mods, post, created,lat,lng) VALUES (NULL,:name,:subreddit,:template_id,0,0, NULL,:lat,:lng)";
				$id = insert_query ( $sql, $params );
				break;
			}
		case 'template' :  // Create new template
			{
				$params = array (
						'title' 		=> filter_input ( INPUT_POST, 'title', FILTER_SANITIZE_STRING ),
						'text' 			=> filter_input ( INPUT_POST, 'text', FILTER_SANITIZE_STRING ) 
				);
				$id = insert_query ( "INSERT INTO templates (id, title,text) VALUES(NULL,:title,:text)", $params );
				break;
			}
		case 'delete' : // delete something.. be careful!
			{
				$type = filter_input ( INPUT_POST, 'type', FILTER_SANITIZE_STRING );
				switch($type){
					case 'city'		: $table = 'cities'; break;
					case 'template'	: $table = 'templates'; break;
					case 'charity'	: $table = 'charities'; break;
					case 'team'		: $table = 'xmas_teams'; break;
					default:	fail("Doh! can't delete that.");
				}
				$id = filter_input ( INPUT_POST, 'id', FILTER_VALIDATE_INT );
				if(!is_numeric($id))
					fail("Invalid id.");
				
				pdo_query ( "DELETE FROM $table WHERE id=:id LIMIT 1", array ('id' => $id ), false );
				ok($type . ' deleted.');
				break;
			}
		case 'modmessage' :// Build a reddit private mod message from the template specified, convert macros into text and save
			{
				message_mods();
				break;
			}
		case 'sendmodmessage':		// Grab a previously created modmessage and actually send it.
			{
				$mid = filter_input(INPUT_POST,'message_id',FILTER_VALIDATE_INT);
				send_mod_message($mid);
				break;
			}
		case 'createthread' : // Build a reddit post from the specified template.
			{
				build_thread();
				break;
			}
		case 'postthread':	// Actually post the thread to reddit
			{
				$pid = filter_input(INPUT_POST,'message_id',FILTER_VALIDATE_INT);
				post_thread($pid);
				break;
			}
		case 'fragment': // Update the text of a fragment. OVERWRITES IT COMPLETELY.. so. yeah.
			{
				$text = $_POST['text'];
				$fragment = filter_input(INPUT_POST,'fragment',FILTER_SANITIZE_STRING);
				$file = $_SERVER['DOCUMENT_ROOT'].'/src/fragments/' . $fragment;
				error_log("UPDATING: $file");
				error_log($text);
				if(!file_exists($file)){
					fail("File doesn't exist!");
				}elseif(!is_writable($file)){
					fail("Can't write to the file.");
				}
				if(!file_put_contents($file,$text)){
					fail("Unable to write to $fragment!");
				}
				ok();
			}
		case 'concept': // fragment is the extensible version, allowing mod to modify any template.
			{
				$text = json_decode($_POST['text']);
				// Save the text into the filesystem, no need for databse for everything!
				file_put_contents(dirname(__FILE__).'/../../src/fragments/xmas_concept.inc',$text);
				ok();
			}
		case 'update_row' : // Update a template or cities fields
			{
				//TODO: Extensiblize(?) this somewhat.. I mean, a database abstraction layer would prevent having to re-hard-code every change all over the joint..
				$type = filter_input(INPUT_POST,'type',FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
				$sql = '';
				$params = array('id' => $id);
				if($type == 'template'){
					$params['title'] = filter_input(INPUT_POST,'title',FILTER_SANITIZE_STRING);
					$params['text'] = filter_input(INPUT_POST,'text',FILTER_SANITIZE_STRING);
					$sql = "UPDATE templates SET title=:title, text=:text WHERE id=:id LIMIT 1";
				}elseif($type == 'city'){
					$params['name'] = filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
					$params['post_template_id'] = filter_input(INPUT_POST,'post_template_id',FILTER_VALIDATE_INT);
					$params['message_template_id'] = filter_input(INPUT_POST,'message_template_id',FILTER_VALIDATE_INT);
					$params['subreddit'] = filter_input(INPUT_POST,'subreddit',FILTER_SANITIZE_STRING);
					$params['mm'] = filter_input(INPUT_POST,'messaged_mods',FILTER_SANITIZE_STRING);
					$params['post'] = filter_input(INPUT_POST,'post',FILTER_SANITIZE_STRING);
					$sql = "UPDATE cities SET name=:name, message_template_id=:message_template_id, post_template_id=:post_template_id, subreddit=:subreddit, messaged_mods=:mm, post=:post WHERE id=:id LIMIT 1";
				}else{
					fail('Unknown action attempted');
				}
				$id = insert_query($sql,$params);
				break;
			}
		case 'get_macros': // retrieve the list of known macros
			{
				ok(' ',get_macros());
			}
		default: fail('Invalid parameter.');
	}
}
/**
 * Build a thread for reddit post purposes.
 */
function build_thread(){
	$id = filter_input ( INPUT_POST, 'id', FILTER_SANITIZE_INT );
	
	// Retrieve details about the city
	$city = sql_to_array( 'SELECT id,name,reddit,template_id FROM cities WHERE id=' . $id);
	set_city_macro($city['name'], $city['reddit']);
	
	// Retrieve the template
	$template = sql_to_array ( 'SELECT title,text FROM templates WHERE id=' . $city['template_id']);
	
	// Retrieve the charity
	$charity = sql_to_array('SELECT name FROM charities WHERE id=' . $city['charity_id']);
	set_charity_macro($charity['name']);
	
	$to = $city ['subreddit'];
	if(DEBUG){
		$to = 'waitingforgobot';
	}
	if(!(strlen($to) || strlen($template['title']) || strlen($template['text']))){
		fail("Invalid recipient, title or text.. ");
	}
	
	// Run the macro parser over the templates text.
	$text = process_macros($template['text']);
	
	// Save the message text (now macro free!) into the database.
	$message_id = insert_query("INSERT INTO sent_messages SET recipient=:recipient, subject=:subject, text=:text, type='city_post' recipient_id=:city_id, mod_id=:mod_id",
			array(	'recipient'=> $to,
					'subject' => $template['title'],
					'text' => $text,
					'recipient_id' => $city['id'],
					'mod_id' => bandit_id()
			))	;
	if(is_int($message_id)){
		//Success!
		ok('Thread templates parsed and ready to post.',array('title'=>$template['title'],'text'=>$text, 'message_id' => $message_id));
	}else{
		fail();
	}	
}
function post_thread($id){
	global $reddit_user,$reddit_password;
	if(!$id)
		fail('How?');
	// Fetch message we've previously created.
	$msg = get_one('SELECT recipient,subject,text FROM sent_messages WHERE id=' . $id);
	if(!is_array($msg) || strlen($msg['text'])==0){
		fail('Invalid message');
	}
	require_once ('../../lib/reddit.php');
	$reddit = new reddit ( $reddit_user, $reddit_password );
	// Recipient should be a reddit, send NULL to avoid the link restrictions, we want a self-post to include our text.
	$r = $reddit->createStory($msg['subject'], null, $msg['recipient'], $msg['text']); 
	error_log("< < < < < < < < < < ------------ > > > > > > >  REDDIT RESPONSE TO POST CREATION"); // Make it hideously obvious in the log.
	error_log(print_r($r,true));
	//TODO: Figure out how to tell if this has failed
	// Get the id of the message from the response
	$mid = $r[0]->data->name; // ASSUMPTION.. untested.. https://github.com/reddit/reddit/wiki/JSON
	// save it
	insert_query('INSERT INTO sent_messages, set ref=:ref WHERE id=:id',array('id'=> $message_id, 'ref'=>$mid));
	// send it back so we can update the table live
	ok('Post created on: ' . $recipient, $mid);// I think.. Will need to test.
}


/**
 * Build a message to the moderators of a subreddit.
 * According to reddit, its just name of subreddit prepended with a #. So, #gameofbands will message GOB mods.
 * 
 * @param reddit $reddit        	
 */
function message_mods() {
	$id = filter_input ( INPUT_POST, 'id', FILTER_SANITIZE_INT );
	
	$city = sql_to_array( 'SELECT id,name,reddit,template_id FROM cities WHERE id=' . $id);
	set_city_macro($city['name'], $city['reddit']);
	
	$template = sql_to_array ( 'SELECT title,text FROM templates WHERE id=' . $city['template_id']);
	
	$charity = sql_to_array('SELECT name FROM charities WHERE id=' . $city['charity_id']);
	set_charity_macro($charity['name']);
	
	$to = '#' . $city ['subreddit'];
	if(DEBUG){
		$to = '#waitingforgobot';
	}
	if(!(strlen($to) || strlen($template['title']) || strlen($template['text']))){
		fail("Invalid recipient, title or text.. ");
	}
	
	// Run the macro parser over the text.
	$text = process_macros($template['text']);
	
	// Save the message text (now macro free!) into the database.
	$message_id = insert_query("INSERT INTO sent_messages SET recipient=:recipient, subject=:subject, text=:text, type='city_message' recipient_id=:city_id, mod_id=:mod_id",
			array(	'recipient'=> $to,
				  	'subject' => $template['title'],
					'text' => $text,
					'recipient_id' => $city['id'],
					'mod_id' => bandit_id() 
			))	;
	if(is_int($message_id)){
		//Success!
		ok('Message parsed and ready to send.',array('title'=>$template['title'],'text'=>$text, 'message_id' => $message_id));
	}else{
		fail();
	}
}
/**
 * Actually send the message identified by
 * @param int $message_id
 */
function send_mod_message($message_id){
	global $reddit_user,$reddit_password;
	if(!$message_id)
		fail('How?');
	// Fetch message we've previously created.
	$msg = get_one('SELECT recipient,subject,text FROM sent_messages WHERE id=' . $message_id);
	if(!is_array($msg) || strlen($msg['text'])==0){
		fail('Invalid message');
	}
	require_once ('../../lib/reddit.php');
	$reddit = new reddit ( $reddit_user, $reddit_password );
	$r = $reddit->sendMessage ( $msg['recipient'], $msg['subject'], $msg['text'] )  ;
	error_log("-----------------------------------------------------------------> > > > > > > > > > > > REDDIT RESPONSE TO MOD_MESSAGE"); // Make it hideously obvious in the log.
	error_log(print_r($r,true));
	// Get the id of the message from the response
	$mid = $r[0]->data->name; // ASSUMPTION.. untested.. https://github.com/reddit/reddit/wiki/JSON
	// save it
	insert_query('INSERT INTO sent_messages, set ref=:ref WHERE id=:id',array('id'=> $message_id, 'ref'=>$mid));
	// send it back so we can update the table live
	ok('Message sent to mods: ' . $recipient, $mid);// I think.. Will need to test.
}


// Default handler 
// $id is set to false, so something above had better change that.

if (is_numeric ( $id )) {
	ok ( 'Done', $id );
} else {
	fail ( 'Doh.' );
}


