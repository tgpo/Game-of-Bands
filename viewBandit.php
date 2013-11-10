<?php

include 'header.php';

if ($bandit=$_GET['bandit']) {
	$result = mysql_query("SELECT * FROM songs WHERE music='$bandit' OR lyrics='$bandit' OR vocals='$bandit'") or die(mysql_error());;
	echo "<div class='header'>";
	echo "<a href=index.php>Return to song library</a>";
	echo "</div>";
	echo "<div class='header'>";
	echo "Viewing ".$bandit."'s profile:";
	echo "</div>";
} else {
	header("Location: index.php"); // revert to index
	exit();
}

// Retrieve all the data from the "songs" table
echo "<table id='songlist'>";
echo "<tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Music</th><th>Music Vote</th><th>Lyrics</th><th>Lyrics Vote</th><th>Vocals</th><th>Vocals Vote</th>";
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

include 'footer.php';

?>