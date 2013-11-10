<?php
include 'header.php';

echo "<div class='header'>
<a href=process.php>Return to song library</a><br />
</div>";

if ($round=$_GET['round']) {
	$roundDetails = mysql_query("SELECT * FROM rounds WHERE number='$round'") or die(mysql_error());
	$rowRound = mysql_fetch_array($roundDetails);
	$result = mysql_query("SELECT * FROM songs WHERE round='$round'") or die(mysql_error());
	echo "<div class='header'>";
	echo "Viewing Round ".$rowRound['number'].": ".$rowRound['theme'].".";
	echo "</div>";
	// Retrieve all the data from the "songs" table
	echo "<table id='songlist'>";
	echo "<tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Music</th><th>Music Vote</th><th>Lyrics</th><th>Lyrics Vote</th><th>Vocals</th><th>Vocals Vote</th></tr>";
	// store the query from songs table into $row, and run a while loop to get each row
	
	while($row = mysql_fetch_array($result)){
	
	// Print out the contents of the entry 
	echo "<tr>";
	// round
	echo "<td><a href='process.php?round=".$row['round']."'>".$row['round']."</a></td>";
	// Name as url
	echo "<td><a href='process.php?song=".$row['id']."'>".$row['name']."</a></td>";
	//Votes Received
	echo "<td>".$row['votes']."</td>";
	// Music, lyrics and vocals with votes
	echo "<td><a href='process.php?bandit=".$row['music']."'>".$row['music']."</a></td><td>".$row['musicvote']."</td>";
	
	echo "<td><a href='process.php?bandit=".$row['lyrics']."'>".$row['lyrics']."</a></td><td>".$row['lyricsvote']."</td>";
	
	echo "<td><a href='process.php?bandit=".$row['vocals']."'>".$row['vocals']."</a></td><td>".$row['vocalsvote']."</td>";
	echo "</tr>";

	}
	echo "</table>";
	
	// display prev and next round
	
	$prevRound = $round-1;
	$roundDetails = mysql_query("SELECT * FROM rounds WHERE number='$prevRound'") or die(mysql_error());
	if ($rowRound = mysql_fetch_array($roundDetails)) {
		echo "<div class='header'>";
			echo "Prev Round: <a href='viewRound.php?round=".$prevRound."'> Round ".$rowRound['number']." - ".$rowRound['theme']."</a>.";
			echo "</div>";
			
		}
	
	$nextRound = $round+1;
	$roundDetails = mysql_query("SELECT * FROM rounds WHERE number='$nextRound'") or die(mysql_error());
	if ($rowRound = mysql_fetch_array($roundDetails)) {
		echo "<div class='header'>";
			echo "Next Round: <a href='viewRound.php?round=".$nextRound."'> Round ".$rowRound['number']." - ".$rowRound['theme']."</a>.";
			echo "</div>";
			
		}


} else {
	$result = mysql_query("SELECT * FROM rounds ORDER by number DESC") or die(mysql_error());
	echo "<div class='header'>";
	echo "Game of Bands Rounds:";
	echo "</div>";
	
	// Retrieve all the data from the "rounds" table
	echo "<table>";
	echo "<tr><th>Round</th><th>Theme</th></tr>";
	// store the query from rounds table into $row, and run a while loop to get each row
	
	while($row = mysql_fetch_array($result)){
	
	// Print out the contents of the entry 
	echo "<tr>";
	// round
	echo "<td><a href='process.php?round=".$row['number']."'>".$row['number']."</a></td>";
	// Name as url
	echo "<td><a href='process.php?round=".$row['number']."'>".$row['theme']."</a></td>";
	echo "</tr>";
	}
	echo "</table>";
}

include 'footer.php';
?>