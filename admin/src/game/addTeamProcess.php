<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../src/secrets.php' );
require_once( '../../../src/query.php' );

mod_check();

if( isset($_POST['addTeam']) ){
  addTeam();

}

function redirect($pagename){
    header('Location: ../../index.php?view=' . $pagename);
}

function addTeam(){
    $db  = database_connect();

    $round = $_POST["round"];
    $teamnumber = $_POST["teamnumber"];
    $musician = $_POST["musician"];
    $lyricist = $_POST["lyricist"];
    $vocalist = $_POST["vocalist"];

    $query = $db->prepare('INSERT INTO teams (round, teamnumber, musician, lyricist, vocalist) VALUES (:round, :teamnumber, :musician, :lyricist, :vocalist)');
    $query->execute(array('round' => $round, 'teamnumber' => $teamnumber, 'musician' => $musician, 'lyricist' => $lyricist, 'vocalist' => $vocalist));
    
    redirect('teamlist');

}

?>