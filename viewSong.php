<?php
if ($song=$_GET['song']) {
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: http://gameofbands.co/song/" . $song); 
} 
else {
	header("Location: index.php"); // revert to index
	exit();
}
?>