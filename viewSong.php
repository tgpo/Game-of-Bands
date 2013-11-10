<?php

include 'header.php';

echo "	<div class='header'>
		<a href=process.php>Return to song library</a><br />
		</div>";

if ($song=$_GET['song']) {
	$result = mysql_query("SELECT * FROM songs WHERE id='$song'") or die(mysql_error());
	$temp = mysql_fetch_array($result);
	$url = $temp['url']; 		
  	echo '
  			<div id="putTheWidgetHere"></div>
  			<script type="text/JavaScript">
  				SC.oEmbed("'.$url.'", {color: "000000"}, 
  				document.getElementById("putTheWidgetHere"));
  			</script>';
	

	$result = mysql_query("SELECT * FROM songs WHERE id='$song'") or die(mysql_error());
} 
else {
	header("Location: index.php"); // revert to index
	exit();
}

$row = mysql_fetch_array($result);
$round = $row['round'];
$lyrics = $row['lyricsheet'];
$roundDetails = mysql_query("SELECT * FROM rounds WHERE number='$round'"); // save round details to be used later



// Retrieve all the data from the "songs" table
echo "<table id='songlist'>";
echo "<tr><th>Song Title</th><th>Votes</th><th>Music</th><th>Music Vote</th><th>Lyrics</th><th>Lyrics Vote</th><th>Vocals</th><th>Vocals Vote</th>";
// store the query from songs table into $row, and run a while loop to get each row

// Print out the contents of the entry 
echo "<tr>";
// Name as url
echo "<td><a href='process.php?song=".$row['id']."'>".$row['name']."</a></td>";
//Votes Received
echo "<td>".$row['votes']."</td>";
// Music, lyrics and vocals with votes
echo "<td><a href='process.php?bandit=".$row['music']."'>".$row['music']."</a></td><td>".$row['musicvote']."</td>";

echo "<td><a href='process.php?bandit=".$row['lyrics']."'>".$row['lyrics']."</a></td><td>".$row['lyricsvote']."</td>";

echo "<td><a href='process.php?bandit=".$row['vocals']."'>".$row['vocals']."</a></td><td>".$row['vocalsvote']."</td>";
echo "</tr>";


echo "</table>";


$row = mysql_fetch_array($roundDetails);

echo "<div class='header'>";
echo "This song was created for Game of Bands, round <a href='process.php?round=".$row['number']."'>".$row['number'].": ".$row['theme']."</a>.";
echo "</div>";

if($lyrics) {
	echo "<div class='header'>";
	echo "Lyrics:";
	echo "</div>";
	echo "<div class='lyrics'>";
	echo nl2br($lyrics);
	echo "</div>";
}

include 'footer.php';

?>