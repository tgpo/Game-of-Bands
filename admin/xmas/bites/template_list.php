<h3>Available macro List</h3><ul>
<?php
// $macros is a global, prefilled
$macros = get_macros();
foreach ( $macros as $f => $r ) {
	$f = str_replace ( '/', '', str_replace ( '\\', '', $f ) );
	echo '<li style="font-size:1.5em;">{'.$f.'} => '.$r.'</li>';
}
?>
</ul>
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

<script type="text/javascript">
<?php
$templates = sql_to_array ( 'SELECT id,title,text FROM templates ORDER BY title ASC' );
echo 'var templates_data = ' . json_encode($templates) . ';';
?>
</script>