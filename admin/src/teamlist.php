<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
$db = database_connect();

$teams = $db->query('SELECT * FROM teams ORDER BY round DESC');

?>

<h2>Team List</h2>
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
        foreach ($teams as $team) {
            echo "<tr>";
                echo "<td>" . $team['round'] . "</td>";
                echo "<td>" . $team['teamnumber'] . "</td>";
                echo "<td>" . $team['musician'] . "</td>";
                echo "<td>" . $team['lyricist'] . "</td>";
                echo "<td>" . $team['vocalist'] . "</td>";
                echo '<td><a href="index.php?view=editteam&id='.$team['id'].'">Edit</a></td>';
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
