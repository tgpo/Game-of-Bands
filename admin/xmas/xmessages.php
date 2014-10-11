<?php
require_once ($dir . '/../src/gob_user.php'); // using require_once to fail on non-admin use.
require_once ($dir . '/../src/query.php');
mod_check (); // forces script to be loaded using admin semantics
// Build macro list with test data.
$macros = get_macros ();
?>
<s>//TODO: display list of messages<br /></s>
//TODO: Create new-message function to this recipient, prepopulated
should the template be pre-selected
<h1>X-Mas Messaging</h1>
<pre>
<?php print_r($_GET);
$id = $_GET ['id'];
$type = $_GET ['type'];
$sql = '';
$charity = $city = $bandit = false;
switch ($type) {
	case 'charity' : $charity = convert_id_to_name($id,'charities'); set_charity_macro($charity); break;
	case 'city_post' :$city = convert_id_to_name($id,'citites'); set_city_macro($city); break;
	case 'city_message' :$city = convert_id_to_name($id,'cities'); set_city_macro($city); break;
//	case 'bandit_pm' :$bandit = convert_id_to_name($id); set_charity_macro($bandit); break;
//	case 'bandit_email' :$bandit = convert_id_to_name($id); set_charity_macro($bandit); break;
	default :
}



$messages = sql_to_array ( 'SELECT * FROM sent_messages WHERE recipient_id=' . $id );

foreach ( $messages as $m ) {
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
?>
</pre>
<h3>Compiled Message</h3>
<label for="title">Title:</label><br />
<input type="text" id="title" style="width:600px;" value="<?php echo $title; ?>"/><br />
<label for="body">Body:</label><br />
<textarea id="body" style="width:600px;height:300px;"><?php echo $body; ?></textarea>
<input type="button" class="send_message" value="Send" />

<?php include_once('bites/template_list.php');?>
<script type="text/javascript" src="/admin/xmas/xmas.js"></script>