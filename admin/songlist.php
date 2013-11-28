<?php
require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db($mysql_db) or die(mysql_error());

$result = mysql_query("SELECT * FROM songs ORDER BY round DESC") or die(mysql_error());
?>

<h1>Song List</h1>

<table>
	<thead>
		<tr>
			<th>Round</th>
			<th>SongName</th>
			<th>SongURL</th>
			<th>Lyrics</th>
			<th>Music</th>
			<th>Vocals</th>
			<th>votesSong</th>
			<th>votesLyrics</th>
			<th>votesMusic</th>
			<th>votesVocals</th>
			<th>Winner</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while($row = mysql_fetch_array($result)){
			echo "<tr";
				if($round != $row['round']) echo ' class="newround"';
			echo ">";
				echo "<td>" . $row['round'] . "</td>";
				echo '<td title="' . $row['lyricsheet'] . '">' . $row['name'] . '</td>';
				echo '<td><a href="' . $row['url'] . '" target="_blank">Listen</a></td>';
				echo "<td>" . $row['lyrics'] . "</td>";
				echo "<td>" . $row['music'] . "</td>";
				echo "<td>" . $row['vocals'] . "</td>";
				echo "<td>" . $row['votes'] . "</td>";
				echo "<td>" . $row['lyricsvote'] . "</td>";
				echo "<td>" . $row['musicvote'] . "</td>";
				echo "<td>" . $row['vocalsvote'] . "</td>";
				echo "<td>";
					if($row['winner']) echo 'Yes!';
				echo "</td>";
				echo '<td><a href="index.php?view=editsong&id='.$row['id'].'">Edit</a></td>';
			echo "</tr>";
			
			$round = $row['round'];
		}
		?>
	</tbody>
</table>
