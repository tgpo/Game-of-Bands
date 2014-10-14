<?php
define('INDEX', true);
$here = dirname(__FILE__);
set_include_path( get_include_path(). ':' . $here   . ':' . $here .'/src/' . ':' . $here . '/classes/');
require_once( 'query.php' );
require_once( 'gob_user.php' );
include_once( 'cacheme.php');

// We should probably get a "favicon.ico".. currently the request will be "handled" erroneously and incorrectly by the index.
// TODO: Come up with a GOB icon.
if(isset($_GET['view']) && $_GET['view'] == 'favicon.ico') die();

header('Content-type: text/html; charset=utf-8');

function writeNewMessageCount(){
    $db = database_connect();
    $currentuser = get_username();
    $messagecount = $db->prepare('SELECT COUNT(*) FROM messages WHERE user_to=:currentuser AND new = 1 order by date_sent desc');
    $messagecount->execute(array('currentuser' => $currentuser));

    echo $messagecount->fetchColumn();
}
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="description" content="<?php fragment('site_title');?>">
<meta name="viewport" content="width=device-width" />
<title><?php fragment('site_title');?></title>
<link rel="stylesheet" type="text/css" href="/css/styles.css?var=update" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/validator/3.12.0/validator.min.js"></script>
<script src="/lib/js/jquery-tablesorter/jquery.tablesorter.min.js"></script>
<script src="https://w.soundcloud.com/player/api.js" type="text/javascript"></script>
<script src="/src/js/site.js"></script>
<?php
//if(isset($previous_session)){
//TODO: Post session details here, so javascript can reinstate them.
//GOB.previous = { song: $song_id, ... };
//}
fragment('google');
?>

</head>
<body>
	<header>
		<h1 id="header1"><?php fragment('header');?></h1>
		<a href="/" id="returnhome"></a>
		<nav id="accountLinks">
            <?php
            if ( is_loggedin() ) {
                echo ' <span class="username">' . get_bandit_links() . '</span>';
            
                if ( is_mod() ) { echo '<a class="adminpanel" href="/admin">Admin Panel</a> | '; }
            
                echo ' <a class="logout" href="/login.php/logout">Logout</a>';
              ?>
               <span id="showMessages"><img src="/images/ico.email.gif"><span
				id="messageCount"><?php writeNewMessageCount(); ?></span></span>
			<section id="messagesContainer"><?php require('admin/src/messages.php'); ?></section>
              <?php
            } else {
                echo '<a class="login" href="/login.php">Login</a>';
            }
            ?>
        </nav>
		<div id='welcomebar'><span id="welcome"><?php fragment('welcome');?></span></div>
	</header>
	<section id="content">
    <?php
      // Decide which data to display
      $view = isset($_GET['view']) ? $_GET['view'] : false;
      
      if(!$view){
				include_once 'src/view_all.php';
      }else{
					// Simply add a file in /src/ where /ViewName is the url and ViewName.php is the file.
					if (is_readable ( 'src/view_' . $view . '.php' )) {
						include_once 'src/view_' . $view . '.php';
					} elseif (is_readable ( 'src/' . $view . '.php' )) {
						include_once 'src/' . $view . '.php';
					} else {
						// Something made it here? What?
						error_log ( "Something strange: " . $view );
						include_once ('src/view_all.php');
					}
				}
				// Save users current page
				$_SESSION ['last'] = $_SERVER['REQUEST_URI'];
    ?>
    </section>

	<footer>
		<p class="redditlink"><?php fragment('reddit');?></p>
		<p class="credits"><?php fragment('credits');?></p>
	</footer>

	<div id="votingWidget">
		<a href="#" id="close-sc" title="Close this widget">[Exit]</a>
		<div id="titleBlock"></div>
		<div id="roundBlock"></div>
		<div id="soundcloudBlock"></div>
		<div id="bandBlock">
			<div id="vote-notification"></div>
			<ul>
				<li class="lyrics"></li>
				<li class="music"></li>
				<li class="vocals"></li>
			</ul>
			<div class="clear"></div>
		</div>
		<div id="lyricsBlock"></div>
	</div>
</body>
</html>
