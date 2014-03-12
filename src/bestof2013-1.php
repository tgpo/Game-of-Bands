<?php
if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
}

loggedin_check();

function writeBestOfNomination(){
    $db    = database_connect();
    $songID = filter_input(INPUT_GET, 'song', FILTER_SANITIZE_NUMBER_INT);
    $bandit = $_SESSION['GOB']['name'];
    
    $query = $db->prepare('SELECT * FROM songs WHERE (lyrics=:lyrics OR music=:music OR vocals=:vocals) AND approved=1 ORDER BY round DESC');
    $query->execute(array('music' => $bandit, 'lyrics' => $bandit, 'vocals' => $bandit));
    $row = $query->fetch();
    
    if(!$row){
        $GOBPlayer = false;
    } else {
        $GOBPlayer = true;
    }

    $bestOf = $db->query("SELECT bestSong FROM bestof2013 GROUP BY bestSong ORDER BY count(bestSong) DESC");
    $bestOfResults = $bestOf->fetchAll();
    shuffle($bestOfResults);
    echo "<h2>Best Song</h2><ul>";
    foreach ($bestOfResults as $nomination) {
        if(!is_null($nomination['bestSong'])){
            $count = $db->query("SELECT count(bestSong) FROM bestof2013 WHERE bestSong = " . $nomination['bestSong'] )->fetchColumn();
            
            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestSong']);
            $songname = $song ->fetch();
            echo '<li><a href="#" rel="bestSong" class="button" data-id="' . $songname['id'] . '">' . $songname['name'] . '</a> <a data-id="' . $songname['id'] . '" data-url="' . $songname['url'] . '" href="#" class="listen">Listen</a></li>';
        }
    }
    
    echo "</ul>";

    $bestOf = $db->query("SELECT bestLyricist FROM bestof2013 GROUP BY bestLyricist ORDER BY count(bestLyricist) DESC");
    $bestOfResults = $bestOf->fetchAll();
    shuffle($bestOfResults);
    echo "<h2>Best Lyricist</h2><ul>";
    foreach ($bestOfResults as $nomination) {
        if(!is_null($nomination['bestLyricist'])){
            $count = $db->query("SELECT count(bestLyricist) FROM bestof2013 WHERE bestLyricist = " . $nomination['bestLyricist'] )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestLyricist']);
            $songname = $song ->fetch();

            echo '<li><a href="#" rel="bestLyricist" class="button" data-id="' . $songname['id'] . '">' . $songname['lyrics'] . ' for ' . $songname['name'] . '</a><a data-id="' . $songname['id'] . '" data-url="' . $songname['url'] . '" href="#" class="listen">Listen</a></li>';
        }
    }
    
    echo "</ul>";

    $bestOf = $db->query("SELECT bestMusician FROM bestof2013 GROUP BY bestMusician ORDER BY count(bestMusician) DESC");
    $bestOfResults = $bestOf->fetchAll();
    shuffle($bestOfResults);
    echo "<h2>Best Musician</h2><ul>";
    foreach ($bestOfResults as $nomination) {
        if(!is_null($nomination['bestMusician'])){
            $count = $db->query("SELECT count(bestMusician) FROM bestof2013 WHERE bestMusician = " . $nomination['bestMusician'] )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestMusician']);
            $songname = $song ->fetch();

            echo '<li><a href="#" rel="bestMusician" class="button" data-id="' . $songname['id'] . '">' . $songname['music'] . ' for ' . $songname['name'] . '</a><a data-id="' . $songname['id'] . '" data-url="' . $songname['url'] . '" href="#" class="listen">Listen</a></li>';
        }
    }
    
    echo "</ul>";

    $bestOf = $db->query("SELECT bestVocalist FROM bestof2013 GROUP BY bestVocalist ORDER BY count(bestVocalist) DESC");
    $bestOfResults = $bestOf->fetchAll();
    shuffle($bestOfResults);
    echo "<h2>Best Vocalist</h2><ul>";
    foreach ($bestOfResults as $nomination) {
        if(!is_null($nomination['bestVocalist'])){
            $count = $db->query("SELECT count(bestVocalist) FROM bestof2013 WHERE bestVocalist = " . $nomination['bestVocalist'] )->fetchColumn();

            $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestVocalist']);
            $songname = $song ->fetch();

            echo '<li><a href="#" rel="bestVocalist" class="button" data-id="' . $songname['id'] . '">' . $songname['vocals'] . ' for ' . $songname['name'] . '</a><a data-id="' . $songname['id'] . '"data-url="' . $songname['url'] . '" href="#" class="listen">Listen</a></li>';
        }
    }
    
    if($GOBPlayer){
        echo "</ul>";

        $bestOf = $db->query("SELECT bestSave FROM bestof2013 GROUP BY bestSave ORDER BY count(bestSave) DESC");
        $bestOfResults = $bestOf->fetchAll();
        shuffle($bestOfResults);
        echo "<h2>Best Save</h2><ul>";
        foreach ($bestOfResults as $nomination) {
            if(!is_null($nomination['bestSave'])){
                $pieces = explode(" | ", $nomination['bestSave']);
                $bandit = $pieces[0];
                $songID = $pieces[1];

                $count = $db->query("SELECT count(bestSave) FROM bestof2013 WHERE bestSave = '" . $nomination['bestSave'] . "'" )->fetchColumn();

                $song = $db->query("SELECT * FROM songs WHERE id = " . $songID);
                $songname = $song ->fetch();

                echo '<li><a href="#" rel="bestSave" class="button" data-id="' . $bandit . ' | ' . $songname['id'] . '">' . $bandit . ' for ' . $songname['name'] . '</a><a data-id="' . $songname['id'] . '" data-url="' . $songname['url'] . '" href="#" class="listen">Listen</a></li>';
            }
        }

        echo "</ul>";

        $bestOf = $db->query("SELECT underAppreciatedSong FROM bestof2013 GROUP BY underAppreciatedSong ORDER BY count(underAppreciatedSong) DESC");
        $bestOfResults = $bestOf->fetchAll();
        shuffle($bestOfResults);
        echo "<h2>Under Appreciated Song</h2><ul>";
        foreach ($bestOfResults as $nomination) {
            if(!is_null($nomination['underAppreciatedSong'])){
                $count = $db->query("SELECT count(underAppreciatedSong) FROM bestof2013 WHERE underAppreciatedSong = " . $nomination['underAppreciatedSong'] )->fetchColumn();

                $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['underAppreciatedSong']);
                $songname = $song ->fetch();

                echo '<li><a href="#" rel="underAppreciatedSong" class="button" data-id="' . $songname['id'] . '">' . $songname['name'] . '</a><a data-id="' . $songname['id'] . '" data-url="' . $songname['url'] . '" href="#" class="listen">Listen</a></li>';
            }
        }

        echo "</ul>";

        $bestOf = $db->query("SELECT underAppreciatedBandit FROM bestof2013 GROUP BY underAppreciatedBandit ORDER BY count(underAppreciatedBandit) DESC");
        $bestOfResults = $bestOf->fetchAll();
        shuffle($bestOfResults);
        echo "<h2>Under Appreciated Bandit</h2><ul>";
        foreach ($bestOfResults as $nomination) {
            if(!is_null($nomination['underAppreciatedBandit'])){
                $pieces = explode(" | ", $nomination['underAppreciatedBandit']);
                $bandit = $pieces[0];
                $songID = $pieces[1];

                $count = $db->query("SELECT count(underAppreciatedBandit) FROM bestof2013 WHERE underAppreciatedBandit = '" . $nomination['underAppreciatedBandit'] . "'" )->fetchColumn();

                $song = $db->query("SELECT * FROM songs WHERE id = " . $songID);
                $songname = $song ->fetch();

                echo '<li><a href="#" rel="underAppreciatedBandit" class="button" data-id="' . $bandit . ' | ' . $songname['id'] . '">' . $bandit . ' for ' . $songname['name'] . '</a><a data-id="' . $songname['id'] . '" data-url="' . $songname['url'] . '" href="#" class="listen">Listen</a></li>';

            }
        }
        echo "</ul>";

        $bestOf = $db->query("SELECT bestApplicationRound FROM bestof2013 GROUP BY bestApplicationRound ORDER BY count(bestApplicationRound) DESC");
        $bestOfResults = $bestOf->fetchAll();
        shuffle($bestOfResults);
        echo "<h2>Best Application of Round's Theme</h2><ul>";
        foreach ($bestOfResults as $nomination) {
            if(!is_null($nomination['bestApplicationRound'])){
                $count = $db->query("SELECT count(bestApplicationRound) FROM bestof2013 WHERE bestApplicationRound = " . $nomination['bestApplicationRound'] )->fetchColumn();

                $song = $db->query("SELECT * FROM songs WHERE id = " . $nomination['bestApplicationRound']);
                $songname = $song ->fetch();

                echo '<li><a href="#" rel="bestApplicationRound" class="button" data-id="' . $songname['id'] . '">' . $songname['name'] . '</a><a data-id="' . $songname['id'] . '" data-url="' . $songname['url'] . '" href="#" class="listen">Listen</a></li>';
            }
        }

        echo "</ul>";
    }
    
    $query = $db->prepare("SELECT * FROM finalBestof2013 WHERE bandit = :bandit");
    $query->execute(array( 'bandit' => $_SESSION['GOB']['name'] ));
    $row = $query->fetch();
    
    $validColumns = array(
        'bestSong',
        'bestLyricist',
        'bestMusician',
        'bestVocalist',
        'bestSave',
        'underAppreciatedSong',
        'underAppreciatedBandit',
        'bestApplicationRound'
    );
    ?>

    <script type="text/javascript">
$(document).ready(function(){
    <?php 
    //If the bandit has previously voted, highlight their saved votes
    if($row['bandit']){
        foreach ($validColumns as $votes) {
        ?>
            $("a[rel='<?php echo $votes; ?>'][data-id$='<?php echo $row[$votes] ?>']").addClass('done').parents('ul').addClass('done');
        <?php
        }
    }
    ?>
    });
        
</script>
<?php

}

?>
<div id="votingWidget">
    <div id="titleBlock"></div>
    <div id="roundBlock"></div>
    <div id="soundcloudBlock"></div>
    <div id="bandBlock"><ul><li class="lyrics"></li><li class="music"></li><li class="vocals"></li></ul><div class="clear"></div></div>
    <div id="lyricsBlock"></div>
</div>

<h2>Game of Bands - Best of 2013</h2>
<p>Vote for Best of the Best of 2013</p>

<aside id="otherviews">
  <a href='/' class="homepage">View All Songs</a> <br />
</aside>

<section id='bestOfVoting'>
<?php
   writeBestOfNomination();
?>
</section>
