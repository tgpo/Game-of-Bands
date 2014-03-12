<?php

require_once( 'secrets.php' );
require_once( 'query.php' );
require_once( 'gob_user.php' );

loggedin_check();

if( isset($_POST['voteSong']) ){
  voteSong();

}

function voteSong(){
    $db = database_connect();

    $bandit = get_username();
    $catagory = filter_input(INPUT_POST, 'catagory', FILTER_SANITIZE_SPECIAL_CHARS);
    $vote = filter_input(INPUT_POST, 'vote', FILTER_SANITIZE_SPECIAL_CHARS);
    


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

    if ( ! in_array($catagory, $validColumns)) {
        throw new Exception('Not a valid column name.');
    }

    // Check if this user has already submitted a nomination
    //If so, update rather than add a new row
    $query = $db->prepare("SELECT * FROM finalBestof2013 WHERE bandit = :bandit");
    $query->execute(array( 'bandit' => $bandit ));
    $row = $query->fetch();
    
    if(!$row['bandit']){
        $query = $db->prepare('INSERT INTO finalBestof2013 (bandit, ' . $catagory . ') VALUES (:bandit, :vote)');
        $query->execute(array( 'bandit' => $bandit, 'vote' => $vote ));
        
    } else {
            $query = $db->prepare('UPDATE finalBestof2013 SET ' . $catagory . '=:vote WHERE bandit=:bandit');
            $query->execute(array( 'vote' => $vote, 'bandit' => $bandit ));

    }

}

?>