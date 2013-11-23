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

<div class='header'>
  <a href='/index.php'>Return to song library</a>
</div>

<div id="putTheWidgetHere"></div>
  <script type="text/JavaScript">
		SC.oEmbed("<?php echo $song['url'];?>", {color: "000000"},
		  document.getElementById("putTheWidgetHere"));
	</script>
	<p><a href="<?php echo $song['url'];?>">Listen To Song</a></p>

<?php
// Display table with this song.
display_songs(array($song));

$round  = $song['round'];
$lyrics = $song['lyricsheet'];

// Display round details
$details = query_round_details($db, $round);

echo "<div class='header'>";
echo "This song was created for Game of Bands, round " . a_round_details($details);
echo "</div>";

// Display lyrics if available
if($lyrics) {
	echo "<div class='header'>";
	echo "Lyrics:";
	echo "</div>";
	echo "<div class='lyrics'>";
	echo nl2br($lyrics);
	echo "</div>";
}
?>