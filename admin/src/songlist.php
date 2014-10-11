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
<strong><a href="/admin/addsong">Add Song</a></strong>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th style="width: 300px;">Name</th>
            <th>URL</th>
            <th>Lyricalist</th>
            <th>Musician</th>
            <th>Vocalist</th>
            <th title="Track Votes" data-medium="Track" data-small="T"></th>
            <th title="Lyric Votes" data-medium="Lyric" data-small="L"></th>
            <th title="Music Votes" data-medium="Music" data-small="M"></th>
            <th title="Vocal Votes" data-medium="Vocal" data-small="V"></th>
            <th title="Song won the round" data-medium="Won" data-small="&#10004;"></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($songs as $song) {
        	$classes = ($i++%2==0)? '' : 'odd';
        	//$classes .= ((isset($round) && isset($song['round'])) && ($round != $song['round'])) ? ' newround' : '';  // Has no effect
            
        	echo '<tr' . (($classes) ? " class=\"$classes\"" :'') . '>';
                echo "<td>" . $song['round'] . "</td>";
                echo '<td><a title="Edit Song" href="/admin/editsong&id='.$song['id'].'">' . $song['name'] . '</td></a>';
                echo '<td><a href="' . $song['url'] . '" target="_blank">Listen</a></td>';
                echo "<td>" . p($song['lyrics']) . "</td>";
                echo "<td>" . p($song['music']) . "</td>";
                echo "<td>" . p($song['vocals']) . "</td>";
                echo "<td>" . $song['votes'] . "</td>";
                echo "<td>" . $song['lyricsvote'] . "</td>";
                echo "<td>" . $song['musicvote'] . "</td>";
                echo "<td>" . $song['vocalsvote'] . "</td>";
                echo "<td>" . (($song['winner']) ? '&#10004;' : '&nbsp;') . "</td>";
            echo "</tr>";
            
            //$round = $song['round'];
        }
        ?>
    </tbody>
</table>

<?php 
function p($name){
	if(bandit_song_count($name) > 0){
		return a_bandit($name);
	}else{
		return "<span class=\"bad\" title=\"This bandit's name isn't in the database\">$name</span>";
	}
}

?>