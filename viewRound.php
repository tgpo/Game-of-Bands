<?php
if ($round=$_GET['round']) {
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: http://gameofbands.co/round/" . $round); 
} 
else {
	header("Location: index.php"); // revert to index
	exit();
}
?>