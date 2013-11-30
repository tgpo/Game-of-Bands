<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db($mysql_db) or die(mysql_error());

$result = mysql_query("SELECT * FROM teams ORDER BY round DESC") or die(mysql_error()); ?>

<h1>Team List</h1>
<a href="index.php?view=addteam">Add New Team</a>
<table>
	<thead>
		<tr>
			<th>Round</th>
			<th>Team</th>
			<th>Musician</th>
			<th>Lyricist</th>
			<th>Vocalist</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while($row = mysql_fetch_array($result)){
			echo "<tr>";
				echo "<td>" . $row['round'] . "</td>";
				echo "<td>" . $row['teamnumber'] . "</td>";
				echo "<td>" . $row['musician'] . "</td>";
				echo "<td>" . $row['lyricist'] . "</td>";
				echo "<td>" . $row['vocalist'] . "</td>";
				echo '<td><a href="index.php?view=editteam&id='.$row['id'].'">Edit</a></td>';
			echo "</tr>";
		}
		?>
	</tbody>
</table>
