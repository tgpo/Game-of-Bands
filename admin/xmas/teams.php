<?php 
require_once ( $dir . '/../src/gob_user.php'); // using require_once to fail on non-admin use.
require_once ( $dir . '/../src/query.php');
mod_check (); //forces script to be loaded using admin semantics
?>
<s>//TODO: display list of teams<br /></s>
//TODO: delete team function
//TODO: Message creator
	<a href=\"https://github.com/clonemeagain/Game-of-Bands/issues/8\">Issue in question</a>
	<br />
	<h1>Manage X-Mas Teams</h1>
	<table id="team_list">
	<thead>
		<tr>
			<th>Status</th>
			<th>Name</th>
			<th>Location</th>
			<th>Creator</th>
			<th title="Message thread">Msg</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
<?php 
$teams = sql_to_array('SELECT * FROM xmas_teams ORDER BY name ASC');
if(!count($teams)){
	echo "No teams found yet, <a href=\"/xmas/find_team\">Add one?</a>.";
}
foreach($teams as $t){
	$id = $t['id'];
	$name = $t['name'];
	$city = convert_id_to_name($t['city_id'],'cities');
	$bandit = convert_id_to_name($t['creator']);
	
	echo '<tr data-id="'.$id.'" data-name="'.$name.'">'
		.'<td>' . $t['status'] . '</td>'
		.'<td><a href="/xmas/team/'.$id.'" title="View Team">'.$name.'</a></td>'
		.'<td><a href="/xmas/city/'.$t['city_id'].'">' . $city .'</a></td>'
		.'<td><a href="/bandit/'.$t['creator'].'">'.$bandit.'</a></td>'
		.'<td><a href="/admin/xmessages?type=bandit_pm&id=' . $id .'">Msg Creator</a></td>'

		.'<td>[<a class="delete_row" href="#" title="Remove this team">X</a>]</td>'
		.'</tr>';
		
}
?></ul>
<script type="text/javascript" src="/admin/xmas/xmas.js"></script>