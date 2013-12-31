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

echo '<h2>/u/GameofBands Reddit Inbox</h2>';
echo '<ul id="inbox">';
    foreach ($messages as $message) {
        echo '<li class="message';

        if($message->data->new){
            echo ' new';
        }

        echo '" id="' . $message->data->name . '">';

            echo '<strong>' . $message->data->subject . '</strong><br />';
            
            $timestamp = $message->data->created_utc;

            echo '<strong class="author">From: </strong>' . $message->data->author;
            echo '<small class="date">Sent: ' . date('m/d/Y', $timestamp) . '</small> ';
            echo '<small class="actions"><a href="#" class="reply">Reply</a></small>';
            echo '<p class="text">' . $message->data->body . '</p>';

            if($message->data->replies){
                echo '<ul>';
                foreach ($message->data->replies->data->children as $reply) {
                    echo '<li class="message';

                    if($reply->data->new){
                        echo ' new';
                    }

                    echo '" id="' . $reply->data->name . '">';
                        echo '<strong class="author">From: </strong>' . $reply->data->author;
                        $timestamp = $reply->data->created_utc;
                        echo '<small class="date">Sent: ' . date('m/d/Y', $timestamp) . '</small> ';
                        echo '<small class="actions"><a href="#" class="reply">Reply</a></small>';
                        echo '<p class="text">' . $reply->data->body . '</p>';
                    echo '</li>';
                }

                echo '</ul>';
            }

            ?>
            <form class="replyform" method="post" action="src/reddit/replyToPM.php" style="display: none;">
                <input type="hidden" name="name" id="name" value="<?php echo $message->data->name; ?>">

                <label>Message</label><br />
                <textarea rows="10" cols="50" name="text"></textarea>
                <br />    
                
                <input type="submit" value="Reply" name="Reply">
            </form>
            <?php

            echo '</li>';

    }

echo "</ul>";

?>
<script>
$(document).ready(function(){
    $("#inbox").on("click", "a.reply", function(event){
        event.stopPropagation();

        $(this).parents('.message').find('.replyform').show();

        return false;
    });
});
</script>