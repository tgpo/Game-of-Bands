<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../src/secrets.php' );
require_once( '../../../src/query.php' );

mod_check();

if( isset($_POST['editTeam']) ){
  editTeam();

}

function redirect($pagename){
    header('Location: /admin/' . $pagename);
}

function editTeam(){
    $db  = database_connect();

    $id = $_POST["id"];
    $round = $_POST["round"];
    $teamnumber = $_POST["teamnumber"];
    $musician = $_POST["musician"];
    $lyricist = $_POST["lyricist"];
    $vocalist = $_POST["vocalist"];

    if( isset($_POST['delete_team']) )
    {
        $query = $db->prepare('DELETE FROM teams WHERE id = :id');
        $query->execute(array('id' => $id));

    } else {

        $query = $db->prepare('UPDATE teams SET round = :round, teamnumber = :teamnumber, musician = :musician, lyricist = :lyricist, vocalist = :vocalist WHERE id = :id');
        $query->execute(array('round' => $round, 'teamnumber' => $teamnumber, 'musician' => $musician, 'lyricist' => $lyricist, 'vocalist' => $vocalist, 'id' => $id));

    }
    
    redirect('teamlist');

}

?>