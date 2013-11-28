<?php
  $bandit = $_GET['bandit'];
  if (!$bandit) {
    header("Location: /index.php"); // revert to index
    exit();
  }
?>

<aside id="otherviews">
  <a href='/index.php' class="returnhome">Return to song library</a>
</aside>

<h2><?php echo $bandit; ?>'s Game of Bands profile</h2>

<section id='profile'>
<?php
  require_once('query.php');
  
  $db    = database_connect();  
  $query = $db->prepare('SELECT * FROM songs WHERE (lyrics=:lyrics OR music=:music OR vocals=:vocals) AND approved=1');
  $query->execute(array('music' => $bandit, 'lyrics' => $bandit, 'vocals' => $bandit));
  display_songs($query);
?>
</section>
