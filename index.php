<html>
<head>
  <link rel="stylesheet" type="text/css" href="/stylesheet.css" />
  <title>The Game of Bands Song Depository</title>
  <script src="//connect.soundcloud.com/sdk.js"></script>
  <script>
    SC.initialize({
      client_id: "5839c7c20cacba125c0540cbf85614ab",
      redirect_uri: "http://gameofbands.co/index.php",
    });
  </script>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-45997039-1', 'gameofbands.co');
	  ga('send', 'pageview');

	</script>
</head>
<body>
	<header>
		<h1 id="header1">Game of Bands - A reddit game of making music</h1>
		<a href="/" id="returnhome"></a>

		<div id='welcomebar'>
			Welcome to the Game of Bands Song Depository. Stay a while and listen.
			<?php
			  require_once( 'src/gob_user.php' );

			  if (is_loggedin()) {
				echo get_username() . "(" . get_karma() . ")";
				if (is_mod()) { echo '<a href="/admin">Admin Panel</a> | '; }
				echo ' <a href="/login.php/logout">Logout</a>';
			  } else {
				echo '<a href="/login.php">Login</a>';
			  }
			?>
		</div>
	</header>
  
  <div id="content">

	  <?php
	    // Decide which data to display
	    switch ($_GET['view']) {
	      case 'bandit' : include_once 'src/view_bandit.php';     break;
	      case 'fame'   : include_once 'src/view_fame.php';       break;
	      case 'round'  : include_once 'src/view_round.php';      break;
	      case 'rounds' : include_once 'src/view_rounds.php';     break;
	      case 'song'   : include_once 'src/view_song.php';       break;
		  case 'user_dashboard'   : include_once 'src/user_dashboard.php';       break;
		  case 'user_submitsong'   : include_once 'src/user_submitsong.php';       break;
		  case 'login_request'   : include_once 'src/login_request.php';       break;
	      default       : include_once 'src/view_all.php';        break;
	    }
	  ?>
	
	  <div id='fineprint'>
	    <p>gameofbands.co is a counterpart to the awesome Game of Bands community at
	    <a href='http://gameofbands.reddit.com' target='_blank'>gameofbands.reddit.com</a>.</p>
		<p>Site programming by RetroTheft, Orphoneus, and tgpo. Design by RetroTheft.
	    All music and lyrics presented herein are copyright of their original creators.</p>
	  </div>
  </div>
</body>
</html>
