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

function writeBestOfNomination(){
    $db    = database_connect();
    $songID = filter_input(INPUT_GET, 'song', FILTER_SANITIZE_NUMBER_INT);
    $bandit = $_SESSION['GOB']['name'];

    $query = $db->prepare('SELECT * FROM songs WHERE id=:songID');
    $query->execute(array('songID' => $songID));
    $song  = $query->fetch();

    $query = $db->prepare("SELECT * FROM bestof2013 WHERE bandit = :bandit");
    $query->execute(array( 'bandit' => $bandit ));
    $bestOf = $query->fetch();

    $html = "<ul>";

    $html .= '<li';
        if (isset($bestOf['bestSong'])) { $html .= ' class="done"';}
    $html .= '>Best song<br /><a href="#" rel="bestSong" class="button" data-id="' . $songID . '">' . $song['name'] . '</a></li>';

    $html .= '<li';
        if (isset($bestOf['bestLyricist'])) { $html .= ' class="done"';}
    $html .= '>Best Lyricist<br /><a href="#" rel="bestLyricist" class="button" data-id="' . $songID . '">' . $song['lyrics'] . '</a></li>';

    $html .= '<li';
        if (isset($bestOf['bestMusician'])) { $html .= ' class="done"';}
    $html .= '>Best Musician<br /><a href="#" rel="bestMusician" class="button" data-id="' . $songID . '">' . $song['music'] . '</a></li>';

    $html .= '<li';
        if (isset($bestOf['bestVocalist'])) { $html .= ' class="done"';}
    $html .= '>Best Vocalist<br /><a href="#" rel="bestVocalist" class="button" data-id="' . $songID . '">' . $song['vocals'] . '</a></li>';

    $html .= '<li';
        if (isset($bestOf['bestSave'])) { $html .= ' class="done"';}
    $html .= '>Best Save (Someone who replaced a quitter on a team)<br />';
        $html .= '<a href="#" rel="bestSave" class="button" data-id="' . $song['lyrics'] . " | " . $songID . '">' . $song['lyrics'] . '</a>';

        $html .= '<a href="#" rel="bestSave" class="button" data-id="' . $song['music'] . " | " . $songID . '">' . $song['music'] . '</a>';

        $html .= '<a href="#" rel="bestSave" class="button" data-id="' . $song['vocals'] . " | " . $songID . '">' . $song['vocals'] . '</a></li>';
    
    $html .= '<li';
        if (isset($bestOf['underAppreciatedSong'])) { $html .= ' class="done"';}
    $html .= '>Criminally under-appreciated song<br /><a href="#" rel="underAppreciatedSong" class="button" data-id="' . $songID . '">' . $song['name'] . '</a></li>';
    
    $html .= '<li';
        if (isset($bestOf['underAppreciatedBandit'])) { $html .= ' class="done"';}
    $html .= '>Criminally under-appreciated bandit<br />';

        $html .= '<a href="#" rel="underAppreciatedBandit" class="button" data-id="' . $song['lyrics'] . " | " . $songID . '">' . $song['lyrics'] . '</a>';

        $html .= '<a href="#" rel="underAppreciatedBandit" class="button" data-id="' . $song['music'] . " | " . $songID . '">' . $song['music'] . '</a>';

        $html .= '<a href="#" rel="underAppreciatedBandit" class="button" data-id="' . $song['vocals'] . " | " . $songID . '">' . $song['vocals'] . '</a></li>';


    $html .= '<li';
        if (isset($bestOf['bestApplicationRound'])) { $html .= ' class="done"';}
    $html .= '>Best application of a round\'s theme<br /><a href="#" rel="bestApplicationRound" class="button" data-id="' . $songID . '">' . $song['name'] . '</a></li>';
    
    $html .= '</ul>';
    
    echo $html;

}

?>
<html>
<head>
  <title>The Game of Bands Song Depository | A reddit game of making music</title>
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="/src/js/site.js"></script>
  <script src="/lib/js/jquery-validation-1.11.1/dist/jquery.validate.min.js"></script>
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
      <?php
      if ( is_loggedin() ) { 
          if ( filter_input(INPUT_GET, 'view', FILTER_SANITIZE_SPECIAL_CHARS) == "song") {
              if ( filter_input(INPUT_GET, 'song', FILTER_SANITIZE_SPECIAL_CHARS) > 78 && filter_input(INPUT_GET, 'song', FILTER_SANITIZE_SPECIAL_CHARS) < 257) {
                ?>
                <section id="bestOf" style="display:none;">
                    <div class="close">X</div>
                    <h2>Best of 2013</h2>
                    <p>Nominate the best songs of 2013</p>
                    <?php 
                        writeBestOfNomination();
                    ?>
                </section>
                <?php
              }
          }
      }

      ?>
        <h1 id="header1">Game of Bands - A reddit game of making music</h1>
        <a href="/" id="returnhome"></a>
        <nav id="accountLinks">
            <?php
            if ( is_loggedin() ) {
                echo ' <span class="username">' . get_bandit_links() . '</span>';
            
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
        case 'edit_profile'     : include_once 'src/edit_profile.php';    break;
        case 'irc'              : include_once 'src/irc.php';             break;
        default                 : include_once 'src/view_all.php';        break;
      }
    ?>

    </section>
  
    <footer>
        <p class="redditlink">gameofbands.co is a counterpart to the awesome Game of Bands community at
        <a href='http://gameofbands.reddit.com' target='_blank'>gameofbands.reddit.com</a>.</p>
        <p class="credits">Site programming by RetroTheft, Orphoneus, IAmTriumph, and tgpo. Design by RetroTheft.
          All music and lyrics presented herein are copyright of their original creators.</p>
    </footer>
</body>
</html>
