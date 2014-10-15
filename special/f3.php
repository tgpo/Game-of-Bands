<?php

// Find the parent folder, add to include path.
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(dirname(__FILE__))). DIRECTORY_SEPARATOR;


// Note: Returns the var, not just includes!
/**
 * Must be used: $f3 = require_once('f3.php'); 
 */
$f3 =  require('lib/fatfree-master/lib/base.php');

$f3->set('UI', 'templates/');
$f3->set('DEBUG',3);

return $f3;