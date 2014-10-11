<?php
require_once ('../includes/gob_admin.php');
require_once ('../../src/query.php');
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
<table id="charity_list">
	<thead>
		<tr>
			<th>Status</th>
			<th>Name</th>
			<th>Location</th>
			<th>Email</th>
			<th>Charity ID</th>
			<th title="Message thread">Msg</th>
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
		. '<a href="/admin/xmas/messages.php?id=' . $id . '" title="View messages">' . '</td></tr>';
}
?></table>

<script type="text/javascript">
$(document).ready(function(){
	// Create delete function
	$('#charity_list').on('click','a.delete',function(){
		var name = $(this).nearest('li').data('name');
		var id = $(this).nearest('li').data('id');
		confirm("You sure you want to delete team: " + name + " ?");
	
		console.log('Deleting ' + name);
		$.ajax({
			type: "POST",
			url: "xmas/json.php?type=delete",
			data: {type: 'charity',id: id},
			success: function(r){
				console.log("Removed.");
			},
			error: function(xhr){
				console.log(xhr); //TODO: Notify mod
			},
		});
	});
});
</script>