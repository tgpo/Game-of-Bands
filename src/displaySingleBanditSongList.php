<?php // This file displays the standard song table list.

echo "<table>";
	echo "<tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Winner</th><th>Votes</th></tr>";
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
	echo "<td><a href='process.php?bandit=".$row['winner']."'>".$row['winner']."</a></td><td>".$row['winnervotes']."</td>";
	echo "</tr>";

	}
	echo "</table>";
?>