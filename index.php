<?php
define('INDEX', true);
require_once( 'src/gob_user.php' );
require_once( 'src/query.php' );

function writeNewMessageCount(){
    $db = database_connect();
    $currentuser = $_SESSION['GOB']['name'];
    $messagecount = $db->prepare('SELECT COUNT(*) FROM messages WHERE user_to=:currentuser AND new = 1 order by date_sent desc');
    $messagecount->execute(array('currentuser' => $currentuser));

    echo $messagecount->fetchColumn();
  
}

?>
<html>
<head>
  <title>The Game of Bands Song Depository | A reddit game of making music</title>
  <link rel="stylesheet" type="text/css" href="/stylesheet.css" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
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
	<nav id="accountLinks">
	      <?php
        if ( is_loggedin() ) {
			echo ' <span class="username">' . '<a href="bandit/'. get_username() . '">' . get_username()  . '</a></span><span class="karma">(' . get_karma() . ")</span>";
			
			if ( is_mod() ) { echo '<a class="adminpanel" href="/admin">Admin Panel</a> | '; }
			
			echo ' <a class="logout" href="/login.php/logout">Logout</a>';
      ?>
       <span id="showMessages"><img src="/images/ico.email.gif"><span id="messageCount"><?php writeNewMessageCount(); ?></span></span>
       <section id="messagesContainer"><?php require('admin/src/messages.php'); ?></section>
      <?php
		} else {
			echo '<a class="login" href="/login.php">Login</a>';
        }
      ?>
     
	</nav>

    <div id='welcomebar'>
      <span id="welcome">Game of Bands is a musical tournament where redditors band together, create a song in 10 days, and compete and critique their music.</span>
    </div>
  </header>
  
  <section id="content">

    <?php
      // Decide which data to display
      switch ($_GET['view']) {
        case 'bandit'           : include_once 'src/view_bandit.php';     break;
        case 'fame'             : include_once 'src/view_fame.php';       break;
        case 'round'            : include_once 'src/view_round.php';      break;
        case 'rounds'           : include_once 'src/view_rounds.php';     break;
        case 'song'             : include_once 'src/view_song.php';       break;
        case 'user_dashboard'   : include_once 'src/user_dashboard.php';  break;
        case 'user_submitsong'  : include_once 'src/user_submitsong.php'; break;
        case 'login_request'    : include_once 'src/login_request.php';   break;
        default                 : include_once 'src/view_all.php';        break;
      }
    ?>
  </section>
  
  <footer>
  <p class="redditlink">gameofbands.co is a counterpart to the awesome Game of Bands community at
  <a href='http://gameofbands.reddit.com' target='_blank'>gameofbands.reddit.com</a>.</p>
  <p class="credits">Site programming by RetroTheft, Orphoneus, and tgpo. Design by RetroTheft.
  All music and lyrics presented herein are copyright of their original creators.</p>
  </footer>
</body>
</html>
