<aside id="otherviews">
  <a href='/index.php' class="returnhome">Return to song library</a>
</aside>

<h2>Game of Bands Rounds</h2>

<section id="roundlist">

<?php
  require_once('query.php');
  
  $db     = database_connect();
  $rounds = $db->query('SELECT * FROM rounds ORDER by number DESC');

	echo "<table id='roundtable'>";
	echo "<thead><tr><th>Round</th><th>Theme</th></tr></thead><tbody>";
	foreach ($rounds as $row) {
		echo "<tr>";
	  echo "<td class='round'>".a_round($row['number'],$row['number'])."</td>";
	  echo "<td class='theme'>".a_round($row['number'],$row['theme'] )."</td>";
	  echo "</tr>";
	}
	echo "</table></tbody>";
?>

</section>