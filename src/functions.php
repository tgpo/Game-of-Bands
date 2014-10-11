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