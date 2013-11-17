<?php
if ($bandit=$_GET['bandit']) {
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: http://gameofbands.co/bandit/" . $bandit); 
} 
else {
	header("Location: index.php"); // revert to index
	exit();
}
?>