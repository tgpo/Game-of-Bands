<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;
}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
$db = database_connect();

$songs = $db->query('SELECT * FROM songs ORDER BY round DESC');
?>

<h2>Song List</h2>
<a href="index.php?view=addsong">Add Song</a>

<table>
    <thead>
        <tr>
            <th>Round</th>
            <th>SongName</th>
            <th>SongURL</th>
            <th>Lyrics</th>
            <th>Music</th>
            <th>Vocals</th>
            <th>votesSong</th>
            <th>votesLyrics</th>
            <th>votesMusic</th>
            <th>votesVocals</th>
            <th>Winner</th>
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($songs as $song) {
            echo "<tr";
                if($round != $song['round']) echo ' class="newround"';
            echo ">";
                echo "<td>" . $song['round'] . "</td>";
                echo '<td title="' . $song['lyricsheet'] . '">' . $song['name'] . '</td>';
                echo '<td><a href="' . $song['url'] . '" target="_blank">Listen</a></td>';
                echo "<td>" . $song['lyrics'] . "</td>";
                echo "<td>" . $song['music'] . "</td>";
                echo "<td>" . $song['vocals'] . "</td>";
                echo "<td>" . $song['votes'] . "</td>";
                echo "<td>" . $song['lyricsvote'] . "</td>";
                echo "<td>" . $song['musicvote'] . "</td>";
                echo "<td>" . $song['vocalsvote'] . "</td>";
                echo "<td>";
                    if($song['winner']) echo 'Yes!';
                echo "</td>";
                echo '<td><a href="index.php?view=editsong&id='.$song['id'].'">Edit</a></td>';
            echo "</tr>";
            
            $round = $song['round'];

        }
        ?>
    </tbody>
</table>
