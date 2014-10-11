<?php
/**
 * Retrieves the contents of a template file DOES NOT INCLUDE.. so, make sure its just HTML
 *
 * @param string $file the bit before the .inc
 * @param string $return whether or not to return the contents as a string, or include in $out.. defaults to $out
 */
function get_template($file,$return=false){
	global $out;
	$filename = dirname(__FILE__) . '/fragments/' . $file . '.inc';
	$a = '';
	if (is_file($filename)) {
		ob_start();
		include $filename;
		$a = ob_get_clean();
	}
	if($return)
		return $a;

	$out .= $a;
}

function fragment($name){
	echo get_template($name,true);
}

/****************************************************************************** Macro Parser */
/**
 * An array of patterns to match against, with corresponding replacement texts.
 * Should be pluggable into preg_replace
 */
$macros = array(
		'gob' => '[Game of Bands](http://gameofbands.co/xmas "Visit Game of Bands X-Mas special")',
		'rgob' => '[subreddit](http://reddit.com/r/gameofbands "Visit our subreddit")'
);

function process_macros($text){
	global $macros;
	// Look for {{template# and grab the id number before the }},
	// grab the template text from database.
	$matches = array();
	if(preg_match('/{template(\d)}/', $text, $matches) === 1){
		$id = $matches[1];
		
	}
	// Fetch any templates referenced by a template.
	$text = preg_replace_callback('/{template(\d)}/i','dereference_template', $text);
	
	// Process all other available macros
	return preg_replace_callback("/\{([a-z_]*)\}/i", 'dereference_macro', $text);
}
function dereference_macro($m){
	global $macros; // We can't assume mixins works on the server.. I'm betting inline functions don't work either.
	return $macros[$m[1]];
}
function dereference_template($m){
	$id = $m[1];
	error_log("PM Matched: " . $matched . ", got id=" . $id);
	$t = get_one('SELECT text FROM templates WHERE id=' . $id);
	// replace the template macro with the retrieved text
	return $t['text'];
}

function set_team_macro($team,$id){
	set_macro('team',"[$team](http://gameofbands.co/team/$id \"Visit $team's page\")");
}
/**
 * Sets up two macros relating to cities
 * @param unknown $city Name of city
 * @param unknown $reddit the /r/{THISBIT}  (Gets turned into markdown link to the subreddit)
 */
function set_city_macro($city,$reddit){
	set_macro('city',$city);
	set_macro('rcity','[' . $reddit . '](http://reddit.com/r/' . $reddit .' "The '. $city .' subreddit")');
}
function set_charity_macro($charity){
	set_macro('charity',$charity);
}
/**
 * Macro builder builder.. adds regex's to the list of replaceable macros.
 * Or overwrites existing macros.
 * @param unknown $name the inside of the matcher, usually a name like "city", but could conceivably be any regex.
 * @param unknown $text the replacement text
 */
function set_macro($name,$text){
	global $macros;
	$macros[$name ] = $text;
}

function get_macros(){
	global $macros;
	set_charity_macro('CHARITY_NAME');
	set_city_macro('CITY_NAME','SUBREDDIT');
	set_team_macro('TEAM_NAME', 'TEAM_ID');
	$macros['template1'] = 'TEXT OF TEMPLATE # 1';
	return $macros;
}