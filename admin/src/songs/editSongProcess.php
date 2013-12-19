<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../src/secrets.php' );
require_once( '../../../src/query.php' );

mod_check();

if( isset($_POST['editSong']) ){
  editSong();

}

function redirect($pagename){
    header('Location: ../../index.php?view=' . $pagename);
}

function editSong(){
    $db  = database_connect();

    $id = $_POST["id"];
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

    if( isset($_POST['winner'] ) && 
        $_POST['winner'] == 'Yes') 
    {
        $winner = true;

    } else {
        $winner = false;

    }
    
    if( isset($_POST['approved'] ) && 
        $_POST['approved'] == 'Yes') 
    {
        $approved = true;

    } else {
        $approved = false;

    }

    if( isset($_POST['delete_song']) )
    {
        $query = $db->prepare('DELETE FROM songs WHERE id = :id');
        $query->execute(array('id' => $id));

    } else {
    
        $query = $db->prepare('UPDATE songs SET name = :name, url = :url ,music = :music, lyrics = :lyrics, vocals = :vocals, lyricsheet = :lyricsheet, round = :round, votes = :votes, winner = :winner, rating = NULL, musicvote = :musicvote, lyricsvote = :lyricsvote,  vocalsvote = :vocalsvote, teamnumber = :teamnumber, approved = :approved WHERE id = :id');
        $query->execute(array('name' => $name, 'url' => $url, 'music' => $music, 'lyrics' => $lyrics, 'vocals' => $vocals, 'lyricsheet' => $lyricsheet, 'round' => $round, 'votes' => $votes, 'winner' => $winner, 'musicvote' => $musicvote, 'lyricsvote' => $lyricsvote, 'vocalsvote' => $vocalsvote, 'teamnumber' => $teamnumber, 'approved' => $approved, 'id' => $id));
   
    }

    redirect('songlist');

}

?>