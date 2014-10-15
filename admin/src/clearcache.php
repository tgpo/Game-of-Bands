<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;
}

$files = glob( $dir .'/../src/cache/*.html');
$num_files = count($files);
array_map("unlink",$files); 

echo '<h3>Static page-cache cleared of ' . $num_files .' cached files.</h3>';

include_once 'dashboard.php';