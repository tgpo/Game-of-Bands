<?php

include 'header.php';

$result = mysql_query("SELECT * FROM songs ORDER by id DESC") or die(mysql_error());
echo "<div class='header'>";
echo "Select individual rounds, songs or bandits to navigate.<br />";
echo "<a href='viewHallofFame.php'>View Hall of Fame</a><br />";
echo "<a href='viewRound.php'>View Rounds by Theme</a><br />";
echo "Viewing Complete Song Library:";
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

include 'footer.php';

?>