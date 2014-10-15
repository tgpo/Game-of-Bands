<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../lib/reddit.php' );
require_once( '../../../src/secrets.php' );

$reddit = new reddit($reddit_user, $reddit_password);

mod_check();

if( isset($_POST['postmessage']) ){

    postmessage($reddit);

}

function redirect($pagename){
    header('Location: /admin/' . $pagename);

}

function postmessage($reddit){
    $mainsubreddit = 'waitingforgobot';

    $title = $_POST["title"];
    $link = $_POST["link"];
    $message = $_POST["message"];
    
    $response = $reddit->createStory($title, $link, $mainsubreddit, $message);

    redirect('dashboard');

}

?>