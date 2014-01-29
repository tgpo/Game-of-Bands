<?php

require_once( 'secrets.php' );
require_once( 'query.php' );
require_once( 'gob_user.php' );

if( isset($_POST['nominateSong']) ){
  nominateSong();

}

function nominateSong(){
    $db = database_connect();

    $bandit = get_username();
    $catagory = filter_input(INPUT_POST, 'catagory', FILTER_SANITIZE_SPECIAL_CHARS);
    $nomination = filter_input(INPUT_POST, 'nomination', FILTER_SANITIZE_SPECIAL_CHARS);

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
    $query = $db->prepare("SELECT * FROM bestof2013 WHERE bandit = :bandit");
    $query->execute(array( 'bandit' => $bandit ));
    $row = $query->fetch();
    
    if(!$row['bandit']){
        $query = $db->prepare('INSERT INTO bestof2013 (bandit, ' . $catagory . ') VALUES (:bandit, :nomination)');
        $query->execute(array( 'bandit' => $bandit, 'nomination' => $nomination ));

    } else {
        $query = $db->prepare('UPDATE bestof2013 SET ' . $catagory . '=:nomination WHERE bandit=:bandit');
        $query->execute(array( 'nomination' => $nomination, 'bandit' => $bandit ));

    }

}

?>