<?php
require_once( 'src/gob_user.php' );
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
		<title>The Game of Bands Song Depository</title>
		<script src="//connect.soundcloud.com/sdk.js"></script>
		<script>
		  SC.initialize({
			client_id: "xxxclient_idxxx",
			redirect_uri: "http://gameofbands.co/index.php",
		  });
		</script>
	</head>
	<body>

		<div id='headerimage'></div>

		<div id='banner'>Welcome to the Game of Bands Song Depository. Stay a while and listen.
		<?php
			if (is_loggedin())
			{
				echo get_username() . "(" . get_karma() . ")";
				
				if (is_mod()) echo '<a href="/admin">Admin Panel</a> | ';
				
				echo ' <a href="login.php/logout">Logout</a>';
				
			} else {
				echo '<a href="login.php">Login</a>';
			}
		?>
		
		</div>

		<?php
		mysql_connect("localhost", "xxxuserxxx", "xxxpasswordxxx") or die(mysql_error());
		mysql_select_db("xxxdatabasexxx") or die(mysql_error());

		echo "<div id='content'>";
		?>