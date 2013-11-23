<div class='header'>
	<a href='index.php'>Return to song library</a><br>
  Game of Bands Rounds:
</div>

<?php
  require_once('query.php');
  
  $db     = database_connect();
  $rounds = $db->query('SELECT * FROM rounds ORDER by number DESC');

	echo "<table>";
	echo "<tr><th>Round</th><th>Theme</th></tr>";
	foreach ($rounds as $row) {
		echo "<tr>";
	  echo "<td>".a_round($row['number'],$row['number'])."</td>";
	  echo "<td>".a_round($row['number'],$row['theme'] )."</td>";
	  echo "</tr>";
	}
	echo "</table>";
?>