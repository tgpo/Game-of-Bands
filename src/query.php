<?php
// Query the song database and display results.

require_once('secrets.php');

/* ************************************************************************
  Database access
************************************************************************ */
function mysqli_connect() {
  $mysqli = new mysqli("localhost", $mysql_user, $mysql_password, "gameofbands");
  if ($mysqli->connect_errno) {
    die ($mysqli->connect_errno);
  }
  return $mysqli;
}


// Query the  rounds  table for a particular round number
function query_round_details($db,$number) {
  $round = $number;
  $query = $db->prepare('SELECT * FROM rounds WHERE number=?');
  $query->bind_param('s',$round);
  $query->execute();
  $roundDetails = $query->get_result();
  return $roundDetails->fetch_assoc();
}


/* ************************************************************************
  Table display
************************************************************************ */
// Display a collection of songs.
function display_songs($result) {
  echo "<table id='songlist'>"
  echo "<tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Music</th><th>Music Vote</th><th>Lyrics</th><th>Lyrics Vote</th><th>Vocals</th><th>Vocals Vote</th></tr>";
  while( $row = $result->fetch_assoc() ){
    tr_song($row);
  }
  echo "</table>";
}

// Display a particular song as a row
function tr_song($row) {
  echo "<tr>";
  echo "<td>" . a_round($row['round']) . "</td>";
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
function a_round($number) {
  return "<a href='index.php?view=round&round=".$number."'>".$number."</a>";
}
function a_round_details($details) {
  return "<a href='index.php?view=round&round=".$details['number']."'> Round ".$details['number']." - ".$details['theme']."</a>.";
}
function a_song($row) {
  return "<a href='index.php?view=song&song=".$row['id']."'>".$row['name']."</a>";
}


