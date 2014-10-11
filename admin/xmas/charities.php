<?php
require_once ( $dir . '/../src/gob_user.php'); // using require_once to fail on non-admin use.
require_once ( $dir . '/../src/query.php');
mod_check (); // forces script to be loaded using admin semantics
?>
//TODO: display list of nominated charities, their vote-counts,
		 and the teams associated
<br />
//TODO: include functions to contact the charity //TODO: show status of
charity, whether it was contacted, link to messages
<a href=\"https://github.com/clonemeagain/Game-of-Bands/issues/8\">Issue
	in question</a>
<br />
<h1>Manage X-Mas Charities</h1>
<table id="charity_list">
	<thead>
		<tr>
			<th>Status</th>
			<th>Name</th>
			<th>Location</th>
			<th>Email</th>
			<th>Charity ID</th>
			<th title="Message thread">Msg</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
<?php
$charities = sql_to_array ( 'SELECT * FROM charities ORDER BY name ASC' );
if (! count ( $charities )) {
	echo "No charities found yet.";
}
foreach ( $charities as $c ) {
	$id = $c ['id'];
	$name = $c ['name'];
	echo '<tr data-name="' . $name . '" data-id="' . $id . '"><td>' 
		. $c ['status'] . '</td><td>' 
		. '<a href="/xmas/charity/' . $id . '" title="View charity">' . $name . '</a>' 
		. '</td><td>' . $c ['locality'] . '</td><td>' 
		. $c ['charity_id'] . '</td><td>' 
		. '<a href="/admin/xmessages?type=charity&id=' . $id . '" title="View messages">' . '</td>'
		. '<td>[<a href="#" class="delete_row">X</td>]</tr>';
}
?></table>
<script type="text/javascript" src="/admin/xmas/xmas.js"></script>