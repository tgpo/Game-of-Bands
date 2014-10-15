<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}
?>
<h2><?php fragment('library_title');?></h2>
<?php fragment('view_all_header');?>

<section id='songlist'>
<?php
  require_once('query.php');
  
  $db    = database_connect();
  $songs = $db->query('SELECT * FROM songs WHERE approved=1 ORDER by id DESC');
  display_songs($songs);
?>
</section>
