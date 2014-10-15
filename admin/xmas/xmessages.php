<?php
require_once ($dir . '/../src/gob_user.php'); // using require_once to fail on non-admin use.
require_once ($dir . '/../src/query.php');
mod_check (); // forces script to be loaded using admin semantics
// Build macro list with test data.
$macros = get_macros ();
?>
<s>//TODO: display list of messages<br /></s>
<s>//TODO: Create new-message function to this recipient, prepopulated should the template be pre-selected</s>
//TODO: Output messages in a more "sane" manner.
//TODO: handle bandit pms/emails
<h1>X-Mas Messaging</h1>
<pre>
// DEBUGGING:
 
<?php print_r($_GET);
$id = $_GET ['id'];
$type = $_GET ['type'];
$sql = '';
$charity = $city = $recipient = false;
// Build any available macro texts.
if($type == 'charity'){
	$charity = get_one('SELECT * FROM charities WHERE id='.$id); 
	set_charity_macro($charity['name']);
	$recipient = $charity['email'];
}elseif($type == 'city_post' || $type == 'city_message'){
	$city = get_one('SELECT * FROM cities WHERE id=' . $id);
	set_city_macro($city['name'], $city['subreddit']);
	$recipient = $city['subreddit'];
}

$messages = sql_to_array ( 'SELECT * FROM sent_messages WHERE recipient_id=' . $id );



foreach ( $messages as $m ) {
	echo "Existing Message found: \n";
	print_r ( $m );
	echo "\n";
}

$body = $title = '';
if(isset($_GET['template'])){
	// Parse the template
	$template = filter_input(INPUT_GET,'template',FILTER_SANITIZE_NUMBER_INT);
	$t = array_shift(sql_to_array('SELECT id,title,text FROM templates WHERE id='.$template.' LIMIT 1'));
	if(isset($t['title'])){ 
		$title = $t['title'];
		$body = process_macros($t['text']);
	}
}

$function = $h = '';
switch($type){
	case 'bandit_email':
	case 'charity': $function = 'sendemail'; $h='Email';break;
	case 'city_post':$function = 'postthread'; $h='Reddit Thread';break;
	case 'bandit_pm':
	case 'city_message': $function = 'sendmodmessage'; $h = 'Reddit PM';break;		
}
?>
</pre>
<div id="message">
	<h3>Sending via <?php echo $h;?></h3>
	<label for="recipient">Recipient:</label><br />
	<input type="text" class="recipient" style="width:600px;" value="<?php echo $recipient; ?>"/><?php if(DEBUG) echo "RECIPIENT WILL BE SET TO 'waitingforgobot";?><br />
	<label for="title">Title:</label><br />
	<input type="text" class="title" style="width:600px;" value="<?php echo $title; ?>"/><br />
	<label for="body">Body:</label><br />
	<textarea class="body" style="width:600px;height:300px;"><?php echo $body; ?></textarea><br />
	<input type="button" class="send_message" value="Send message" data-function="<?php echo $function; ?>" data-id="<?php echo $id;?>" data-type="<?php echo $type;?>" />
</div>

<?php include_once('bites/template_list.php');?>
<script type="text/javascript" src="/admin/xmas/xmas.js"></script>