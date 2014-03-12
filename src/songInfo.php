<?php
    $song=$_GET['song'];
        if (!$song) {
        header("Location: /index.php"); // revert to index
        exit();
    }

    require_once('query.php');

    $db    = database_connect();
    $query = $db->prepare('SELECT * FROM songs WHERE id=:song and approved=1');
    $query->execute(array('song' => $song));
    $song  = $query->fetch();

    $query = $db->prepare('SELECT theme FROM rounds WHERE number=:round');
    $query->execute(array('round' => $song['round']));
    $round  = $query->fetch();

    function fixAscii($string) {
        $map = Array(
            '’' => "'"
        );

        $search = Array();
        $replace = Array();

        foreach ($map as $s => $r) {
            $search[] = $s;
            $replace[] = $r;
        }

        return str_replace($search, $replace, $string); 
    }

$lyricsheet = fixAscii(nl2br($song['lyricsheet']));


echo json_encode( array( "name"=>$song['name'],"url"=>$song['url'],"round"=>$song['round'],"lyricsheet"=>$lyricsheet,"theme"=>$round['theme'],"banditLyrics"=>$song['lyrics'],"banditMusic"=>$song['music'],"banditVocals"=>$song['vocals'] ) );
?>