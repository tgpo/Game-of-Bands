<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once('../lib/reddit.php');

$reddit = new reddit($reddit_user, $reddit_password);

$messages = $reddit->getInboxMessages();
$messages = $messages->data->children;

echo "<h2>Messages</h2>";
echo "<ul>";
    foreach ($messages as $message) {
        echo "<li>";
            echo "<strong>" . $message->data->subject . "</strong><br />";
            
            $timestamp = $message->data->created_utc;

            echo "<strong>From: </strong>" . $message->data->author;
            echo " <small>Sent: " . date('m/d/Y', $timestamp) . "</small>";
            echo "<p>" . $message->data->body . "</p>";
            echo "</li>";

    }

echo "</ul>";

?>