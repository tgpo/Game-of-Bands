<?php
  $round = $_GET['round'];
  if (!$round) {
    header("Location: index.php"); // revert to index
    exit();
  }

  require_once('query.php');
  
  $db           = database_connect();
  $roundDetails = query_round_details($db, $round);

  $query = $db->prepare('SELECT * FROM songs WHERE round=?');
  $query->bind_param('s',$round);
  $query->execute();
  $songs = $query->get_result();
?>

<div class='header'>
  <a href='index.php'>Return to song library</a>
  Viewing Round <?php echo $roundDetails['number']; ?> :
    "<?php echo $roundDetails['theme']; ?>"
</div>

<?php
	// Display all songs for this round.
	display_songs($songs);

	// Display previous and next round
	$details = query_round_details($db, $round-1);
	if ($details) {
		echo "<div class='header'>";
		echo "Previous Round: ".a_round_details($details);
		echo "</div>";
	}
	
	$details = query_round_details($db, $round+1);
	if ($details) {
		echo "<div class='header'>";
		echo "Next Round: ".a_round_details($details);
		echo "</div>";
	}

?>