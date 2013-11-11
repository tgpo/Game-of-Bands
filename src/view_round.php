<?php
  $round = $_GET['round'];
  if (!$round) {
    header("Location: index.php"); // revert to index
    exit();
  }

  require_once('query.php');
  
  $db = mysqli_connect();
  $roundDetails = $db->query("SELECT * FROM rounds WHERE number='$round'");
  $rowRound     = $roundDetails->fetch_assoc();
  $songs        = $db->query("SELECT * FROM songs WHERE round='$round'");
?>

<div class='header'>
  <a href='index.php'>Return to song library</a>
  Viewing Round <?php echo $rowRound['number']; ?> :
    "<?php echo $rowRound['theme']; ?>"
</div>

<?php
	// Display all songs for this round.
	display_songs($songs);

	// Display previous and next round	
	$prevRound    = $round-1;
	$roundDetails = $db->query("SELECT * FROM rounds WHERE number='$prevRound'");
	if ($details  = $roundDetails->fetch_assoc()) {
		echo "<div class='header'>";
		echo "Previous Round: ".a_round_details($details);
		echo "</div>";
	}
	
	$nextRound    = $round+1;
	$roundDetails = $db->query("SELECT * FROM rounds WHERE number='$nextRound'");
	if ($details  = $roundDetails->fetch_assoc()) {
		echo "<div class='header'>";
		echo "Next Round: ".a_round_details($details);
		echo "</div>";
	}

?>