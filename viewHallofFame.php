<?php
include 'header.php';

echo "<div class='header'>
<a href=process.php>Return to song library</a><br />
</div>";

// BEST SONGS

// orders the table by votes, then selects the first entry of each group. Still needs to be modified to display equal maximums.
$result = mysql_query("SELECT * FROM (SELECT * FROM songs WHERE votes IS NOT NULL ORDER BY votes DESC) AS s GROUP BY round DESC") or die(mysql_error());

// displays a brief header before table
echo "<div class='header'>";
echo "The Game of Bands Hall of Fame - Winning Songs";
echo "</div>";

// dispays the standard song list
include 'displaySongList.php';

// BEST MUSICIANS

// orders the table by votes, then selects the first entry of each group. Still needs to be modified to display equal maximums.
$result = mysql_query("SELECT id, name, round, votes, music AS winner, musicvote AS winnervotes FROM (SELECT * FROM songs WHERE votes IS NOT NULL ORDER BY musicvote DESC) AS s GROUP BY round DESC") or die(mysql_error());

echo "<div class='float'>";

// displays a brief header before table
echo "<div class='header'>";
echo "The Game of Bands Hall of Fame - Winning Musicians";
echo "</div>";

// BEST LYRICISTS

// dispays the standard song list
include 'displaySingleBanditSongList.php';
echo "</div>";

// orders the table by votes, then selects the first entry of each group. Still needs to be modified to display equal maximums.
$result = mysql_query("SELECT id, name, round, votes, lyrics AS winner, lyricsvote AS winnervotes FROM (SELECT * FROM songs WHERE votes IS NOT NULL ORDER BY lyricsvote DESC) AS s GROUP BY round DESC") or die(mysql_error());

echo "<div class='float'>";


// displays a brief header before table
echo "<div class='header'>";
echo "The Game of Bands Hall of Fame - Winning Lyricists";
echo "</div>";

// dispays the standard song list
include 'displaySingleBanditSongList.php';

echo "</div>";
// BEST VOCALISTS

// orders the table by votes, then selects the first entry of each group. Still needs to be modified to display equal maximums.
$result = mysql_query("SELECT id, name, round, votes, vocals AS winner, vocalsvote AS winnervotes FROM (SELECT * FROM songs WHERE votes IS NOT NULL ORDER BY vocalsvote DESC) AS s GROUP BY round DESC") or die(mysql_error());

echo "<div class='float'>";

// displays a brief header before table
echo "<div class='header'>";
echo "The Game of Bands Hall of Fame - Winning Vocalists";
echo "</div>";

// dispays the standard song list
include 'displaySingleBanditSongList.php';

echo "</div>";
include 'footer.php';
?>