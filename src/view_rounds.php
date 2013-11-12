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
	  echo "<td><a href='index.php?view=round&round=".$row['number']."'>".$row['number']."</a></td>";
	  echo "<td><a href='index.php?view=round&round=".$row['number']."'>".$row['theme']."</a></td>";
	  echo "</tr>";
	}
	echo "</table>";
?>