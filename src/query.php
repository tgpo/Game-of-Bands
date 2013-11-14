<?php
// Query the song database and display results.

require_once('secrets.php');

/* ************************************************************************
  Database access
************************************************************************ */
function database_connect() {
  global $mysql_user, $mysql_password, $mysql_db; // from secrets.php
  $db = new PDO("mysql:host=localhost;dbname=$mysql_db", $mysql_user, $mysql_password);

  // Use real prepared statements. See <http://stackoverflow.com/a/60496/403805>
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  return $db;
}


// Query the  rounds  table for a particular round number
function query_round_details($db,$number) {
  $query = $db->prepare('SELECT * FROM rounds WHERE number=:number');
  $query->execute(array('number' => $number));
  return $query->fetch();
}


/* ************************************************************************
  Table display
************************************************************************ */
// Display a collection of songs.
function display_songs($result) {
  echo "<table id='songlist'>";
  echo "<tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Music</th><th>Music Vote</th><th>Lyrics</th><th>Lyrics Vote</th><th>Vocals</th><th>Vocals Vote</th></tr>";
  foreach ($result as $row) {
    tr_song($row);
  }
  echo "</table>";
}

// Display a particular song as a row
function tr_song($row) {
  echo "<tr>";
  echo "<td>" . a_round($row['round'],$row['round']) . "</td>";
  echo "<td>" . a_song($row)  . "</td>";
  echo "<td>" . $row['votes'] . "</td>";
  td_bandit('music' ,$row);
  td_bandit('lyrics',$row);
  td_bandit('vocals',$row);
  echo "</tr>";
}

// make two <td> tags for a bandit's name
function td_bandit($type, $row) {
  echo "<td>" . a_bandit($row[$type]) . "</td>";
  echo "<td>" . $row[$type.'vote']    . "</td>";
}

/* ************************************************************************
  Making links
************************************************************************ */
function a_bandit($name) {
  return "<a href='index.php?view=bandit&bandit=".$name."'>".$name."</a>";
}
function a_round($number,$name) {
  return "<a href='index.php?view=round&round=".$number."'>".$name."</a>";
}
function a_round_details($details) {
  return "<a href='index.php?view=round&round=".$details['number']."'> Round ".$details['number']." - ".$details['theme']."</a>.";
}
function a_song($row) {
  return "<a href='index.php?view=song&song=".$row['id']."'>".$row['name']."</a>";
}
