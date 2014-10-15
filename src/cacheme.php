<?php
/*
* Disk-render dynamic pages to dramatically accelerate page-load times.
* Works for all pages except those submitting $_POST variables.
* Saves lots of database queries, as only the first hit in 24 hrs incurs the hit.
* Initiate file-based caching, wrap everything in a buffer which is then cached to disk.
* For slowly changing data, we don't actually need to use the database on every page, and it should therefore be MUCH FASTER
* also, it means we won't run out of concurrent database connections when under load.
*  
* cachme.php
*
* Copyright 2010, Thomas Robinson
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
*
* Based on techniques described at http://www.snipe.net/2009/03/quick-and-dirty-php-caching/
*
* Heavily modified by Aaron Were for Game of Bands (http://gameofbands.co)
* September 2014
* clonemeagain@gmail.com
*/
// It's hard to debug cached data.. obviously.
if(defined('DEBUG') && DEBUG)
	return; 

// Don't cache logged-in-users.. would be a nightmare!
// Save our precious db connections for logged in users.
if(is_loggedin())
	return;

// Allow scripts to override type.
if(!defined('CACHE_TYPE'))
	define('CACHE_TYPE','html'); //alternate cache-types cannot handle injected debugging, json etc.

if(!defined('CACHE_TIME'))
	define('CACHE_TIME',(24 * 60 * 60)); // 24 hours.

// Tell the browser and inline-proxies to cache this
header('Cache-Control: private, max-age=0, must-revalidate');
header('Expires: Sat, 1 Jan 2000 12:00:00 GMT');
header('Pragma:'); // disable the web server's default response of no-cache
header('X-Powered-By: RetroThefts time, money & effort.');
if(CACHE_TYPE == 'json')
	header("Content-type: application/json");

$cache_dir = dirname(__FILE__) . '/cache';
if(!file_exists($cache_dir))
	mkdir($cache_dir);

$uri = $_SERVER['REQUEST_URI'];
// some chars in filesystem entries can do funny things.. have to md5 it.
$uri = (CACHE_TYPE == 'html') ? md5($uri) : str_replace('/','_',$uri);  // json only has integers and stuff.. should be ok.
$cache_file =  $cache_dir . '/' . $uri . '.' . CACHE_TYPE;
if(defined('DEBUG') && DEBUG){
	$cache_error_string 	= '<!-- Warning: unable to cache - filehandle not valid, check permissions? '	. $_SERVER['REQUEST_URI'].'-->';
	$cache_cached_string 	= '<!-- Cached at: ' . date('jS F Y H:i', time()) 		. $_SERVER['REQUEST_URI'].'-->';
	$cache_loaded_string 	= '<!-- Loaded from cache: ' . date('jS F Y H:i', @filemtime($cache_file)). $_SERVER['REQUEST_URI'].'-->';
}else{
    $cache_cached_string = $cache_error_string = $cache_loaded_string = '';
}

function cache_echo($string){
	if(CACHE_TYPE == 'html') //cannot append debugging string on json files.. xml might not be well-formed either.. html.. meh.
		echo $string;
}
function cache_page() {
	global $cache_dir,$cache_file,$cache_cached_string;
		// attempt to create the cache file
		$fh = fopen($cache_file, 'w');
		if($fh){
			// Render page to cache & browser.
			fwrite($fh, ob_get_contents());
			fclose($fh);
			cache_echo($cache_cached_string);
			ob_end_flush();
			return;
		}
	cache_echo($cache_error_string); 
}

//Main caching functions:
//Check to see if POST data has been submitted, we won't cache any pages like this, so skip it.
if(empty($_POST)){
	// check to see if a valid cached file exists, serve that where possible
	if (file_exists($cache_file) && (time() - CACHE_TIME < filemtime($cache_file))) {
		echo file_get_contents($cache_file); //push cached page at users browser
		cache_echo($cache_loaded_string); //prepend debug string
		exit; //thats all folks, if we kept processing, we would actually regenerate the page again (which is what we want if the file is too old)
	}
	// start output buffering
	ob_start();
	//Run function at end of all other scripts to wrap page generation and create cache file.
	register_shutdown_function('cache_page');
}
