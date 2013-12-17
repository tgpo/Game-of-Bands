<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
$db = database_connect();

$rounds = $db->query('SELECT * FROM rounds ORDER BY number DESC');

?>

<h2>Round List</h2>
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
        foreach ($rounds as $round) {
            echo "<tr>";
                echo "<td>" . $round['number'] . "</td>";
                echo "<td>" . $round['theme'] . "</td>";
                echo '<td><a href="index.php?view=editround&id='.$round['number'].'">Edit</a></td>';
            echo "</tr>";
        }
        ?>
    </tbody>
</table>