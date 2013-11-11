<?php
  $song=$_GET['song'];
  if (!$song) {
    header("Location: index.php"); // revert to index
    exit();
  }

  $db    = mysqli_connect();
  $songs = $db->query("SELECT * FROM songs WHERE id='$song'");
	$song  = $db->fetch_array($songs);
?>

<div class='header'>
  <a href='index.php'>Return to song library</a>
</div>

<div id="putTheWidgetHere"></div>
  <script type="text/JavaScript">
		SC.oEmbed("<?php echo $song['url'];?>", {color: "000000"},
		  document.getElementById("putTheWidgetHere"));
	</script>;
</div>

<?php
$round  = $song['round'];
$lyrics = $song['lyricsheet'];

// Display table with this song.
display_songs($song);

// Display round details
$roundDetails = $db->query("SELECT * FROM rounds WHERE number='$round'");
$row          = $roundDetails->fetch_assoc();

echo "<div class='header'>";
echo "This song was created for Game of Bands, round " . a_round_details($row);
echo "</div>";

if($lyrics) {
	echo "<div class='header'>";
	echo "Lyrics:";
	echo "</div>";
	echo "<div class='lyrics'>";
	echo nl2br($lyrics);
	echo "</div>";
}
?>