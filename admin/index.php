<?php
require_once( '../src/query.php' );
mod_check();

define( 'INDEX', true );

function writeNewMessageCount(){ //why isn't this in query?
    $db = database_connect();
    $currentuser = get_username();
    $messagecount = $db->prepare('SELECT COUNT(*) FROM messages WHERE user_to=:currentuser AND new = 1 order by date_sent desc');
    $messagecount->execute(array('currentuser' => $currentuser));

    echo $messagecount->fetchColumn();
  
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, maximum-scale=1.0" />

  <link rel="stylesheet" href="css/styles.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="/lib/sidr/jquery.sidr.min.js"></script>
  <link rel="stylesheet" href="/lib/sidr/stylesheets/jquery.sidr.dark.css">
  <script src="/lib/stacktable/stacktable.js"></script>

  <script>
    $(document).ready(function() {
      $("#menu").sidr({
        name:     "leftNav",
        source:   "#leftnav",
        displace: false,
        onOpen: function(){
          $('.sidr-inner').prepend('<a style="display: block;padding: 15px;" id="closeMenu">Close</a>');
        },
        onClose: function(){
          $('.sidr-inner #closeMenu').remove();
        }
      });
      $("body").on("click", "#closeMenu", function(event){
        $.sidr('close', 'leftNav');
      });

      $('table').stacktable({class:'mobileTable'});

      $('.confirm').click(function() {
        var c = confirm("You are about change the website database!\n\n You sure you want to do that?");
        return c; 
      });
    });
  </script>
</head>
<body>
  <div class="header-container">
    <header class="wrapper clearfix">
      <a id="menu" href="#"><img src="/images/ico.menu.png" /></a>
      <h1 class="title left"><a href="/admin" class="home">Game of Bands Admin</a></h1>
      <span id="showMessages"><img src="/images/ico.email.gif"><span id="messageCount"><?php writeNewMessageCount(); ?></span></span>
      <section id="messagesContainer"><?php require('src/messages.php'); ?></section>
      <div class="clearfix"></div>
    </header>
  </div>
  <div id="container">
    <div id="page">
      <nav id="leftnav">
        <ul>
          <li>
            <h3>Mod Functions</h3>
          </li>
          <li>
            <ul>
                <li><a href="index.php?view=songlist">Songs</a></li>
                <li><a href="index.php?view=roundlist">Rounds</a></li>
                <li><a href="index.php?view=teamlist">Teams</a></li>
                <li><a class="confirm" href="index.php?view=resetflair">Reset Flair Counts</a></li>
            </ul>
          </li>
          <li>
            <h3>Use /u/GameofBands</h3>
          </li>
          <li>
            <ul>
                <li><a href="index.php?view=postmessage">Make New Post</a></li>
            </ul>
          </li>
          <li>
            <h3>Under Development</h3>
          </li>
          <li>
            <ul>
                <li><a href="index.php?view=inbox">/u/GameofBands Inbox</a></li>
                
                <li><a href="index.php?view=editflair">edit Flair</a></li>
                <li><a href="index.php?view=editcities" title="Modify cities, post messages to mods etc">Xmas Cities</a></li>
                
            </ul>
          </li>
          <li><h3>Tools</h3></li>
          <ul>
          <li><a href="index.php?view=clearcache" title="Empty the static page cache for bandits, makes your changes visible">Clear Cache</a></li>
          </ul>
        </ul>
        <div class="clearfix"></div>
      </nav>

      <div id="content">
        <div id="page-content">
          <?php
            // Decide which data to display
            switch (isset($_GET['view'])?$_GET['view']:'') {
              case 'addsong'          : include_once 'src/addsong.php';             break;
              case 'editsong'         : include_once 'src/editsong.php';            break;
              case 'editround'        : include_once 'src/editround.php';           break;
              case 'roundlist'        : include_once 'src/roundlist.php';           break;
              case 'songlist'         : include_once 'src/songlist.php';            break;
              case 'postmessage'      : include_once 'src/postmessage.php';         break;
              case 'teamlist'         : include_once 'src/teamlist.php';            break;
              case 'addteam'          : include_once 'src/addteam.php';             break;
              case 'editteam'         : include_once 'src/editteam.php';            break;
              case 'inbox'            : include_once 'src/inbox.php';               break;
              case 'editflair'        : include_once 'src/editflair.php';           break;
              case 'resetflair'       : include_once 'src/resetflair.php';          break;
              case 'bestof2013'       : include_once 'src/bestOf2013.php';          break;
              case 'adminSettings'    : include_once 'src/view_AdminSettings.php';  break;
              case 'editcities'				: include_once 'xmas/cities.php';							break;
              case 'editxmasteams'		: include_once 'xmas/teams.php';							break;
              case 'clearcache'				: {
              	array_map("unlink",glob('../../src/cache/*.html')); echo '<h3>Static page-cache cleared</h3>';
              	/* No break, show dashboard */
              }	
              default                 : include_once 'src/dashboard.php';           break;

            }
          ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>