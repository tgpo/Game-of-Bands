<?php
require_once( 'includes/gob_admin.php' );
require_once( 'includes/admin_header.php' );
require_once( 'includes/secrets.php' );

mod_check();
?>

<h1>Song List</h1>
<?php
mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db("xxxdatabasexxx") or die(mysql_error());

$result = mysql_query("SELECT * FROM songs ORDER BY round DESC") or die(mysql_error()); ?>

<table>
	<thead>
		<tr>
			<th>Round</th>
			<th>SongName</th>
			<th>SongURL</th>
			<th>Lyrics</th>
			<th>Music</th>
			<th>Vocals</th>
			<th>SongLyrics</th>
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
			echo "<tr>";
				echo "<td>" . $row['round'] . "</td>";
				echo "<td>" . $row['name'] . "</td>";
				echo "<td>" . $row['url'] . "</td>";
				echo "<td>" . $row['lyrics'] . "</td>";
				echo "<td>" . $row['music'] . "</td>";
				echo "<td>" . $row['vocals'] . "</td>";
				echo "<td>" . substr($row['lyricsheet'], 0, 15) . "</td>";
				echo "<td>" . $row['votes'] . "</td>";
				echo "<td>" . $row['lyricsvote'] . "</td>";
				echo "<td>" . $row['musicvote'] . "</td>";
				echo "<td>" . $row['vocalsvote'] . "</td>";
				echo "<td>" . $row['winner'] . "</td>";
				echo '<td><a href="editsong.php?id='.$row['id'].'">Edit</a></td>';
			echo "</tr>";
		}
		?>
	</tbody>
</table>
<?php
require_once( 'includes/admin_footer.php' );
?>