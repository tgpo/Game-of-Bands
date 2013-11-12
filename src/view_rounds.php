<div class='header'>
  Game of Bands Rounds:
</div>

<?php
  require_once('query.php');
  
  $db     = mysqli_connect();
  $rounds = $db->query('SELECT * FROM rounds ORDER by number DESC');

	echo "<table>";
	echo "<tr><th>Round</th><th>Theme</th></tr>";
	while($row = $rounds->fetch_assoc()){
		echo "<tr>";
	  echo "<td>".a_round($row['number'],$row['number'])."</td>";
	  echo "<td>".a_round($row['number'],$row['theme'] )."</td>";
	  echo "</tr>";
	}
	echo "</table>";
?>