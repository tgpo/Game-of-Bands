<?php 
require_once ( $dir . '/../src/gob_user.php'); // using require_once to fail on non-admin use.
require_once ( $dir . '/../src/query.php');
mod_check (); //forces script to be loaded using admin semantics
?>
//TODO: display list of messages<br />
<pre>
<?php 
$id = $_GET['id'];

$messages = sql_to_array('SELECT * FROM sent_messages WHERE recipient_id='.$id);

foreach($messages as $m){
	echo print_r($m,true);
	echo "\n";
}
?>
</pre>
//TODO: display list of templates
<?php include_once('bites/template_list.php');?>