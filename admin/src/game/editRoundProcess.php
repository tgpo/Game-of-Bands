<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../src/secrets.php' );
require_once( '../../../src/query.php' );

mod_check();

if( isset($_POST['editRound']) ){
  editRound();

}

function redirect($pagename){
    header('Location: /admin/' . $pagename);
}

function editRound(){
    $db  = database_connect();

    $id = $_POST["id"];
    $theme = $_POST["theme"];
    $signupID = $_POST["signupID"];
    $musiciansSignupID = $_POST["musiciansSignupID"];
    $lyricistsSignupID = $_POST["lyricistsSignupID"];
    $vocalistSignupID = $_POST["vocalistSignupID"];
    $announceID = $_POST["announceID"];
    $consolidationID = $_POST["consolidationID"];
    $themeID = $_POST["themeID"];
    $songvotingthreadID = $_POST["songvotingthreadID"];
    $congratsID = $_POST["congratsID"];
    
    $start_round = (isset($_POST['start_round'])) ? ', start= NOW() ': '';
    $end_round = (isset($_POST['end_round'])) ? ', end= NOW() ': '';
  

    if( isset($_POST['delete_round']) )
    {
        $query = $db->prepare('DELETE FROM rounds WHERE number = :id');
        $query->execute(array('id' => $id));
    	
    }else{
		//TODO: add our start/end datetime fields.. assuming timepicker or now()?

        $query = $db->prepare('UPDATE rounds SET theme = :theme, signupID = :signupID, musiciansSignupID = :musiciansSignupID, lyricistsSignupID = :lyricistsSignupID, vocalistSignupID = :vocalistSignupID, announceID = :announceID, consolidationID = :consolidationID, themeID = :themeID, songvotingthreadID = :songvotingthreadID, congratsID = :congratsID ' . $start_round . $end_round . ' WHERE number = :id');
        $query->execute(array('theme' => $theme, 'signupID' => $signupID, 'musiciansSignupID' => $musiciansSignupID, 'lyricistsSignupID' => $lyricistsSignupID, 'vocalistSignupID' => $vocalistSignupID, 'announceID' => $announceID, 'consolidationID' => $consolidationID, 'themeID' => $themeID, 'songvotingthreadID' => $songvotingthreadID, 'congratsID' => $congratsID, 'id' => $id));

    }
    
    redirect('roundlist');

}

?>