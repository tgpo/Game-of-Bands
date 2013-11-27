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
  echo "<thead><tr><th>Round</th><th>Song Title</th><th>Votes</th><th>Music</th><th>Music Vote</th><th>Lyrics</th><th>Lyrics Vote</th><th>Vocals</th><th>Vocals Vote</th></tr></thead>";
  foreach ($result as $row) {
    tr_song($row);
  }
  echo "</table>";
}

// Display a particular song as a row
function tr_song($row) {
  echo "<tr>";
  echo '<td class="round">' . a_round($row['round'],$row['round']) . "</td>";
  echo '<td class="songname">' . a_song($row)  . "</td>";
  echo '<td class="songvotes">' . $row['votes'] . "</td>";
  td_bandit('music' ,$row);
  td_bandit('lyrics',$row);
  td_bandit('vocals',$row);
  echo "</tr>";
}

// make two <td> tags for a bandit's name
function td_bandit($type, $row) {
  echo '<td class="' . $type . 'name">' . a_bandit($row[$type]) . "</td>";
  echo '<td class="' . $type . 'votes">' . $row[$type.'vote']    . "</td>";
}

/* ************************************************************************
  Making links
************************************************************************ */
function a_bandit($name) {
  return "<a class='banditname' href='/bandit/".$name."'>".$name."</a>";
}
function a_round($number,$name) {
  return "<a class='round' href='/round/".$number."'>".$name."</a>";
}
function a_round_details($details) {
  return "<a class='round' href='/round/".$details['number']."'> Round ".$details['number']." - ".$details['theme']."</a>.";
}
function a_song($row) {
  return "<a class='song' href='/song/".$row['id']."'>".$row['name']."</a>";
}
