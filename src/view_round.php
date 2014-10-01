<?php
if (! defined ( 'INDEX' )) {
	header ( 'Location: ../index.php' );
	die ();
}

$round = filter_input(INPUT_GET,'round',FILTER_VALIDATE_INT);
if (! $round) {
	header ( "Location: /index.php" ); // revert to index
	exit ();
}

require_once ('query.php');

$db = database_connect ();
$roundDetails = query_round_details ( $db, $round );

$songs = $db->prepare ('
SELECT songs.*, teams.musician, teams.lyricist, teams.vocalist  
	FROM songs  
	INNER JOIN teams 
	ON songs.teamnumber = teams.teamnumber 
	AND songs.round=:round AND songs.approved=1
	GROUP BY teams.teamnumber
		' );
try {
	$songs->execute ( array (
			'round' => $round 
	) );
} catch ( PDOException $e ) {
	error_log("SELECT songs query failed on view_round.php");
}

?>

<aside id="otherviews">
	<a href='/index.php' class="returnhome">Return to song library</a>
</aside>

<h2>Game of Bands Round <?php echo $roundDetails['number']; ?> :
    "<?php echo $roundDetails['theme']; ?>" </h2>

<section id="songlist">
<?php
// Display all songs for this round.
display_songs ( $songs );

// Display previous and next round
$details = query_round_details ( $db, $round - 1 );
if ($details) {
	echo "<span class='previousRound'>";
	echo "Previous Round: " . a_round_details ( $details );
	echo "</span>";
}

$details = query_round_details ( $db, $round + 1 );
if ($details) {
	echo "<span class='nextRound'>";
	echo "Next Round: " . a_round_details ( $details );
	echo "</span>";
}

?>
</section>