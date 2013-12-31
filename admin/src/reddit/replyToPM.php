<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../lib/reddit.php' );
require_once( '../../../src/secrets.php' );

$reddit = new reddit($reddit_user, $reddit_password);

mod_check();

if( isset($_POST['Reply']) ){

    replyToPM($reddit);

}

function redirect($pagename){
    header('Location: ../../index.php?view=' . $pagename);

}

function replyToPM($reddit){

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_SPECIAL_CHARS);

    $response = $reddit->addComment($name, $text);

    redirect('inbox');

}

?>