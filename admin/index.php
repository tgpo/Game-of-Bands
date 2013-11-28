<?php
require_once( 'includes/gob_admin.php' );
mod_check();
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
        <h1 class="title"><a href="/admin" class="home">Game of Bands Admin</a></h1>
        <nav>
            <ul>
                <li><a href="index.php?view=addsong">Add Song</a></li>
                <li><a href="index.php?view=songlist">Song List</a></li>
                <li><a href="index.php?view=roundlist">Round List</a></li>
            </ul>
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
        default                 : include_once 'dashboard.php';      break;
      }
    ?>
  </div>	
</body>
</html>