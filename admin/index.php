<?php
require_once( 'includes/gob_admin.php' );
mod_check();

require_once('../src/query.php');
$db    = database_connect();

define('INDEX', true);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width">

  <link rel="stylesheet" href="css/styles.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body>
  <div class="header-container">
    <header class="wrapper clearfix">
        <span id="showMessages"><img src="/images/ico.email.gif"></span>
        <section id="messagesContainer" style="display:none; background: #fff; border: 1px solid #ccc; padding: 7px; position: absolute; right: 10px;"><?php require('src/messages.php'); ?></section>
        <h1 class="title"><a href="/admin" class="home">Game of Bands Admin</a></h1>
        <nav>
            <ul>
                <li><a href="index.php?view=songlist">Songs</a></li>
                <li><a href="index.php?view=roundlist">Rounds</a></li>
				        <li><a href="index.php?view=teamlist">Teams</a></li>
                <li><a href="index.php?view=postmessage">Post Message</a></li>
            </ul>
            <div class="clearfix"></div>
        </nav>
    </header>
  </div>

  <div class="main-container">
    <?php
      // Decide which data to display
      switch ($_GET['view']) {
        case 'addsong'          : include_once 'addsong.php';        break;
        case 'editsong'         : include_once 'editsong.php';       break;
        case 'editround'        : include_once 'editround.php';      break;
        case 'roundlist'        : include_once 'roundlist.php';      break;
        case 'songlist'         : include_once 'songlist.php';       break;
        case 'postmessage'      : include_once 'postmessage.php';    break;
		    case 'teamlist'         : include_once 'teamlist.php';       break;
		    case 'addteam'          : include_once 'addteam.php';        break;
		    case 'editteam'         : include_once 'editteam.php';       break;
		    case 'inbox'            : include_once 'inbox.php';          break;
		    case 'editcss'          : include_once 'editcss.php';        break;
        default                 : include_once 'dashboard.php';      break;
      }
    ?>
  </div>	
</body>
</html>