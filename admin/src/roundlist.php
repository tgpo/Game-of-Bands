<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}
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
            <th>Round Started</th>
            <th>Round Ended</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($rounds as $round) {
			$start = (has_round_started($round)) ? format_date( $round['start'] ) : '&nbsp;';
			$end =  (has_round_ended($round)) ? format_date($round['end']) : '&nbsp;';
            echo "<tr>";
                echo '<td class="fat"><a href="/admin/editround&id='.$round['number'].'">#' . $round['number'] . "</a></td>";
                echo "<td>" . $round['theme'] . "</td>";
                echo "<td>" . $start . '</td><td>' . $end . '</td>';
            echo "</tr>";
        }
        ?>
    </tbody>
</table>