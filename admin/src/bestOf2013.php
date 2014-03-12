<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );

function writeBestOfNominations(){
    $db    = database_connect();

    $bestOf = $db->query("SELECT bestSong FROM bestof2013 GROUP BY bestSong ORDER BY count(bestSong) DESC");
    echo "<h2>Best Song</h2>";
    foreach ($bestOf as $nomination) {
        if(!is_null($nomination['bestSong'])){
            $count = $db->query("SELECT count(bestSong) FROM bestof2013 WHERE bestSong = " . $nomination['bestSong'] )->fetchColumn();
            
            $song = $db->query("SELECT name FROM songs WHERE id = " . $nomination['bestSong']);
            $songname = $song ->fetch();
            echo '<ul><li>' . $songname['name'] . ' (' . $count . ')</li></ul>';
        }
    }

    $bestOf = $db->query("SELECT bestLyricist FROM bestof2013 GROUP BY bestLyricist ORDER BY count(bestLyricist) DESC");
    echo "<h2>Best Lyricist</h2>";
    foreach ($bestOf as $nomination) {
        if(!is_null($nomination['bestLyricist'])){
            $count = $db->query("SELECT count(bestLyricist) FROM bestof2013 WHERE bestLyricist = " . $nomination['bestLyricist'] )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestLyricist']);
            $songname = $song ->fetch();

            echo '<ul><li>' . $songname['name'] . ' - ' . $songname['lyrics'] . ' (' . $count . ')</li></ul>';
        }
    }

    $bestOf = $db->query("SELECT bestMusician FROM bestof2013 GROUP BY bestMusician ORDER BY count(bestMusician) DESC");
    echo "<h2>Best Musician</h2>";
    foreach ($bestOf as $nomination) {
        if(!is_null($nomination['bestMusician'])){
            $count = $db->query("SELECT count(bestMusician) FROM bestof2013 WHERE bestMusician = " . $nomination['bestMusician'] )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestMusician']);
            $songname = $song ->fetch();

            echo '<ul><li>' . $songname['name'] . ' - ' . $songname['music'] . ' (' . $count . ')</li></ul>';
        }
    }

    $bestOf = $db->query("SELECT bestVocalist FROM bestof2013 GROUP BY bestVocalist ORDER BY count(bestVocalist) DESC");
    echo "<h2>Best Vocalist</h2>";
    foreach ($bestOf as $nomination) {
        if(!is_null($nomination['bestVocalist'])){
            $count = $db->query("SELECT count(bestVocalist) FROM bestof2013 WHERE bestVocalist = " . $nomination['bestVocalist'] )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestVocalist']);
            $songname = $song ->fetch();

            echo '<ul><li>' . $songname['name'] . ' - ' . $songname['vocals'] . ' (' . $count . ')</li></ul>';
        }
    }

    $bestOf = $db->query("SELECT bestSave FROM bestof2013 GROUP BY bestSave ORDER BY count(bestSave) DESC");
    echo "<h2>Best Save</h2>";
    foreach ($bestOf as $nomination) {
        if(!is_null($nomination['bestSave'])){
            $pieces = explode(" | ", $nomination['bestSave']);
            $bandit = $pieces[0];
            $songID = $pieces[1];

            $count = $db->query("SELECT count(bestSave) FROM bestof2013 WHERE bestSave = '" . $nomination['bestSave'] . "'" )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $songID);
            $songname = $song ->fetch();

            echo '<ul><li>' . $bandit . " for " . $songname['name'] . ' (' . $count . ')</li></ul>';
        }
    }

    $bestOf = $db->query("SELECT underAppreciatedSong FROM bestof2013 GROUP BY underAppreciatedSong ORDER BY count(underAppreciatedSong) DESC");
    echo "<h2>Under Appreciated Song</h2>";
    foreach ($bestOf as $nomination) {
        if(!is_null($nomination['underAppreciatedSong'])){
            $count = $db->query("SELECT count(underAppreciatedSong) FROM bestof2013 WHERE underAppreciatedSong = " . $nomination['underAppreciatedSong'] )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['underAppreciatedSong']);
            $songname = $song ->fetch();

            echo '<ul><li>' . $songname['name'] . ' (' . $count . ')</li></ul>';
        }
    }

    $bestOf = $db->query("SELECT underAppreciatedBandit FROM bestof2013 GROUP BY underAppreciatedBandit ORDER BY count(underAppreciatedBandit) DESC");
    echo "<h2>Under Appreciated Bandit</h2>";
    foreach ($bestOf as $nomination) {
        if(!is_null($nomination['underAppreciatedBandit'])){
            $pieces = explode(" | ", $nomination['underAppreciatedBandit']);
            $bandit = $pieces[0];
            $songID = $pieces[1];

            $count = $db->query("SELECT count(underAppreciatedBandit) FROM bestof2013 WHERE underAppreciatedBandit = '" . $nomination['underAppreciatedBandit'] . "'" )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $songID);
            $songname = $song ->fetch();

            echo '<ul><li>' . $bandit . " for " . $songname['name'] . ' (' . $count . ')</li></ul>';
        }
    }

    $bestOf = $db->query("SELECT bestApplicationRound FROM bestof2013 GROUP BY bestApplicationRound ORDER BY count(bestApplicationRound) DESC");
    echo "<h2>Best Application of Round's Theme</h2>";
    foreach ($bestOf as $nomination) {
        if(!is_null($nomination['bestApplicationRound'])){
            $count = $db->query("SELECT count(bestApplicationRound) FROM bestof2013 WHERE bestApplicationRound = " . $nomination['bestApplicationRound'] )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestApplicationRound']);
            $songname = $song ->fetch();

            echo '<ul><li>' . $songname['name'] . ' (' . $count . ')</li></ul>';
        }
    }

}

writeBestOfNominations();

?>



<div class="clear"></div>