<?php
  $song=$_GET['song'];
  if (!$song) {
    header("Location: /index.php"); // revert to index
    exit();
  }

  require_once('query.php');

  $db    = database_connect();
  $query = $db->prepare('SELECT * FROM songs WHERE id=:song and approved=1');
  $query->execute(array('song' => $song));
  $song  = $query->fetch();
?>

<aside id="otherviews">
  <a href='/index.php' class="returnhome">Return to song library</a>
</aside>

<h2><?php echo $song['name'];?></h2>
<h3>This song was created for Game of Bands round <?php echo $song['round']; ?></h3>

<div id="putTheWidgetHere"></div>
  <script type="text/JavaScript">
		SC.oEmbed("<?php echo $song['url'];?>", {color: "000000"},
		  document.getElementById("putTheWidgetHere"));
	</script>
	<p class="extlink"><a href="<?php echo $song['url'];?>" class="listen">Listen To Song</a></p>

<?php
// Display table with this song.
display_songs(array($song));

$round  = $song['round'];
$lyrics = $song['lyricsheet'];

// Display round details
$details = query_round_details($db, $round);

// Display lyrics if available
if($lyrics) {
	echo "<h4>";
	echo "Lyrics:";
	echo "</h4>";
	echo "<div class='lyrics'>";
	echo nl2br($lyrics);
	echo "</div>";
}
?>