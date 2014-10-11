<?php 
require_once ( $dir . '/../src/gob_user.php'); // using require_once to fail on non-admin use.
require_once ( $dir . '/../src/query.php');
mod_check (); //forces script to be loaded using admin semantics
?>
<s>//TODO: display list of teams<br /></s>
//TODO: delete team function
	<a href=\"https://github.com/clonemeagain/Game-of-Bands/issues/8\">Issue in question</a>
	<br />
	<h1>X-Mas Teams</h1>
	<ul id="team_list">
<?php 
$teams = sql_to_array('SELECT * FROM xmas_teams ORDER BY name ASC');
if(!count($teams)){
	echo "No teams found yet.";
}
foreach($teams as $t){
	$id = $t['id'];
	$name = $t['name'];
	echo '<li data-id="'.$id.'" data-name="'.$name.'">'
		.'[<a class="delete" href="#" title="Remove this team">X</a>]&nbsp;&nbsp;'
		.'<a href="/xmas/team/'.$id.'" title="View Team">'.$name.'</a></li>';
}
?></ul>
<script type="text/javascript">
$(document).ready(function(){
	// Create delete function
	$('#team_list').on('click','a.delete',function(){
		var name = $(this).nearest('li').data('name');
		var id = $(this).nearest('li').data('id');
		confirm("You sure you want to delete team: " + name + " ?");
	
		console.log('Deleting ' + name);
		$.ajax({
			type: "POST",
			url: "xmas/json.php?type=delete",
			data: {type: 'team',id: id},
			success: function(r){
				var id = r.element_id;
				console.log("Removed.");
			},
			error: function(xhr){
				console.log(xhr); //TODO: Notify mod
			},
		});
	});
});
</script>