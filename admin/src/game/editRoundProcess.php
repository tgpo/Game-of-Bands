<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../src/secrets.php' );
require_once( '../../../src/query.php' );

mod_check();

if( isset($_POST['editRound']) ){
  editRound();

}

function redirect($pagename){
    header('Location: ../../index.php?view=' . $pagename);
}

function editRound(){
    $db  = database_connect();

    $id = $_POST["id"];
    $theme = $_POST["theme"];
    $signupID = $_POST["signupID"];
    $musiciansSignupID = $_POST["musiciansSignupID"];
    $lyricistsSignupID = $_POST["lyricistsSignupID"];
    $vocalistSignupID = $_POST["vocalistSignupID"];
    $consolidationID = $_POST["consolidationID"];
    $themeID = $_POST["themeID"];
    $songvotingthreadID = $_POST["songvotingthreadID"];

    if( isset($_POST['delete_round']) )
    {
        $query = $db->prepare('DELETE FROM rounds WHERE number = :id');
        $query->execute(array('id' => $id));

    } else {

        $query = $db->prepare('UPDATE rounds SET theme = :theme, signupID = :signupID, musiciansSignupID = :musiciansSignupID, lyricistsSignupID = :lyricistsSignupID, vocalistSignupID = :vocalistSignupID, consolidationID = :consolidationID, themeID = :themeID, songvotingthreadID = :songvotingthreadID WHERE number = :id');
        $query->execute(array('theme' => $theme, 'signupID' => $signupID, 'musiciansSignupID' => $musiciansSignupID, 'lyricistsSignupID' => $lyricistsSignupID, 'vocalistSignupID' => $vocalistSignupID, 'consolidationID' => $consolidationID, 'themeID' => $themeID, 'songvotingthreadID' => $songvotingthreadID, 'id' => $id));

    }
    
    redirect('roundlist');

}

?>