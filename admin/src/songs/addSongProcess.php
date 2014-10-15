<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../src/secrets.php' );
require_once( '../../../src/query.php' );

mod_check();

if( isset($_POST['addSong']) ){
  addSong();

}

function redirect($pagename){
    header('Location: /admin/' . $pagename);
}

function addSong(){
    $db  = database_connect();

    $round = $_POST["round"];
    $name = $_POST["name"];
    $url = $_POST["url"];
    $lyrics = $_POST["lyrics"];
    $music = $_POST["music"];
    $vocals = $_POST["vocals"];

    $lyricsheet = $_POST["lyricsheet"];
    $votes = $_POST["votes"];
    $lyricsvote = $_POST["lyricsvote"];
    $musicvote = $_POST["musicvote"];
    $vocalsvote = $_POST["vocalsvote"];
    $teamnumber = $_POST["teamnumber"];

    if(isset($_POST['winner']) && 
       $_POST['winner'] == 'Yes') 
    {
        $winner = true;
    } else {
        $winner = false;
    }
    
    $approved = true;
    
    $query = $db->prepare('INSERT INTO songs (name, url, music, lyrics, vocals, lyricsheet, round, votes, winner, rating, musicvote, lyricsvote, vocalsvote, teamnumber, approved) VALUES (:name, :url, :music, :lyrics, :vocals, :lyricsheet, :round, :votes, :winner, NULL, :musicvote, :lyricsvote, :vocalsvote, :teamnumber, :approved)');
    $query->execute(array('name' => $name, 'url' => $url, 'music' => $music, 'lyrics' => $lyrics, 'vocals' => $vocals, 'lyricsheet' => $lyricsheet, 'round' => $round, 'votes' => $votes, 'winner' => $winner, 'musicvote' => $musicvote, 'lyricsvote' => $lyricsvote, 'vocalsvote' => $vocalsvote, 'teamnumber' => $teamnumber, 'approved' => $approved));

    redirect('songlist');

}

?>