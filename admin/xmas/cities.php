 <?php 
mod_check (); //forces script to be loaded using admin semantics, and will error if run directly is attempted.
require_once (dirname(__FILE__).'/../../src/fragments/google_geocomplete.php');
require_once (dirname(__FILE__).'/../../src/functions.php');
?>
<script type="text/javascript">
<?php
$cities = sql_to_array ( "SELECT id,name,template_id,subreddit,messaged_mods,post, (SELECT COUNT(*) FROM xmas_teams WHERE city_id = cities.id) as team_count FROM cities ORDER BY name ASC" );
// embed array as JSON.. because.. the databinding needed it.
echo 'var cities_data = ' . json_encode($cities) . ';';
$templates = sql_to_array ( 'SELECT id,title,text FROM templates' );
echo 'var templates_data = ' . json_encode($templates) . ';';
?>
</script>
<script type="text/javascript" src="/admin/xmas/cities.js"></script>
<div id="citiespage">
<h1>Manage X-Mas Cities</h1>
<div style="float:right;"><h2><a href="/xmas/find_team">Add new team</a></h2></div>
	<?php //TODO autosave
			//<label><input type="checkbox" name="autosave" checked="checked" autocomplete="off"> Autosave</label> 
	?>
	<table id="cities">
	<thead>
	<tr>
		<th>Name</th>
		<th title="Template ID (See below)">T</th>
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
	<h2>Template definitions</h2>
	<table id="templates">
	<thead>
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Text</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	</table>
	<input class="new" data-type="template" type="button" value="Save new Template" />
	<br />
	<hr width="100%" />
	<br />
	<h2><a href="/xmas/">xmas homepage</a> text</h2>
	<textarea id="concept" style="width:100%;height:100px;"><?php echo get_template('xmas_concept',true);?>
	</textarea>
	<input id="save_concept" type="button" value="Save Concept" /><span>Note: HTML</span>
</div>

	
