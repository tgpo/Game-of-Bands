<?php
require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db('xxxdatabasexxx') or die(mysql_error());

$result = mysql_query("SELECT * FROM rounds ORDER BY number DESC") or die(mysql_error()); ?>

<h1>Round List</h1>
<table>
	<thead>
		<tr>
			<th>Round</th>
			<th>Theme</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while($row = mysql_fetch_array($result)){
			echo "<tr>";
				echo "<td>" . $row['number'] . "</td>";
				echo "<td>" . $row['theme'] . "</td>";
				echo '<td><a href="index.php?view=editround.php&id='.$row['number'].'">Edit</a></td>';
			echo "</tr>";
		}
		?>
	</tbody>
</table>
