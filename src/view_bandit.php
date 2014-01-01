<?php
  if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
  }

  $bandit = $_GET['bandit'];
  if (!$bandit) {
    header("Location: /index.php"); // revert to index
    exit();
  }
?>

<aside id="otherviews">
  <a href='/index.php' class="returnhome">Return to song library</a>
</aside>

<h2><?php echo $bandit; ?>'s Game of Bands profile  <?php if($bandit == get_username()) write_edit_controls(); ?></h2> 

<section id='profile'>
<?php
  
  write_bandit_profile($bandit);
  
  require_once('query.php');
  
  $db    = database_connect();  
  $query = $db->prepare('SELECT * FROM songs WHERE (lyrics=:lyrics OR music=:music OR vocals=:vocals) AND approved=1 ORDER BY round DESC');
  $query->execute(array('music' => $bandit, 'lyrics' => $bandit, 'vocals' => $bandit));
  display_songs($query);
?>
</section>
