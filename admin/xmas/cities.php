<?php
mod_check (); //forces script to be loaded using admin semantics, and will error if run directly is attempted.
require_once (dirname(__FILE__).'/../../src/fragments/google_geocomplete.php');
require_once (dirname(__FILE__).'/../../src/functions.php');
?>
<div id="citiespage">
<h1>Manage X-Mas Cities</h1>
<div style="float:right;">
<h2><a href="/xmas/find_team">Add new team</a></h2></div>
	<table id="cities">
	<thead>
	<tr>
		<th>Name</th>
		<th title="Post Template ID (See below)">P_T</th>
		<th title="Message Template ID">M_T</th>
		<th>Reddit</th>
		<th>Messaged</th>
		<th>Posted</th>
		<th>&nbsp;</th>
		<th title="Number of teams competing in this City">#</th>
		</tr>
	</thead>
	</table>
	<input class="new" data-type="city" type="button" value="Save new City"  />
	<br />
	<hr width="100%" />
	<br />
<?php include_once('bites/template_list.php');?>
	<br />
	<hr width="100%" />
	<br />
	<h2><a href="/xmas/">xmas homepage</a> text</h2>
	<textarea id="concept" style="width:100%;height:100px;"><?php echo get_template('xmas_concept',true);?>
	</textarea>
	<input id="save_concept" type="button" value="Save Concept" /><span>Note: HTML</span>
</div>

	
<script type="text/javascript">
<?php
$cities = sql_to_array ( "SELECT id,name,post_template_id,message_template_id,subreddit,messaged_mods,post, (SELECT COUNT(*) FROM xmas_teams WHERE city_id = cities.id) as team_count FROM cities ORDER BY name ASC" );
// embed array as JSON.. because.. the databinding needed it.
echo 'var cities_data = ' . json_encode($cities) . ';';
?>
</script>
<script type="text/javascript" src="/admin/xmas/xmas.js"></script>