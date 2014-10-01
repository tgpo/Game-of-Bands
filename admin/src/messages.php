<?php
if( !defined('INDEX') ) {
    header( 'Location: ../index.php' );
    die;

}

function displayMessages($currentuser){
    $db = database_connect();
    $messagecount = $db->prepare('SELECT COUNT(*) FROM messages WHERE user_to=:currentuser order by date_sent desc');
    $messagecount->execute(array('currentuser' => $currentuser));

    if( $messagecount->fetchColumn() > 0 ) {
    $messages = $db->prepare('SELECT * FROM messages WHERE user_to=:currentuser order by date_sent desc');
    $messages->execute(array('currentuser' => $currentuser));

    foreach ($messages as $message) {
      echo '<li data-id="' .$message['id']  . '"';

      if(isset($message['new']) && $message['new']) echo ' class="new"';
  
      echo ">" . $message['body'] . "<br /><small>From: </small>" . $message['user_from'] . " <small>Sent: </small>" . $message['date_sent'] . "<br /><a href='#' class='reply'>Reply</a> <a href='#' class='delete'>Delete</a></li>";
    }

  } else {
    echo '<li id="noMessages">No Messages</li>';
  }
  
}

function displayBanditDropdown(){
  $db = database_connect();
  $moderators = $standards = ''; 
  $bandits = $db->query('SELECT * FROM bandits order by name asc');
  foreach ($bandits as $bandit) {
    $bandit['is_mod'] ? $moderators .= '<option value="' . $bandit['name'] . '">' . $bandit['name'] . '</option>' : $standards .= '<option value="' . $bandit['name'] . '">' . $bandit['name'] . '</option>';
  }
  
  $selectHTML = '<select id="user_to" name="user_to" />';
  $selectHTML .= '<option value="allmods">All Moderators</option>';
  if ( is_mod() ) {
    $selectHTML .= '<option value="everyone">Everyone</option>';
  }
  $selectHTML .= '<optgroup label="Moderators">' . $moderators . '</optgroup>';
  $selectHTML .= '<optgroup label="Bandits">' . $standards . '</optgroup>';
  $selectHTML .= '</select>';
  
  echo $selectHTML;
  
}

?><!--  /admin/src/messages.php -->
<script>
$(document).ready(function(){
    $("#showMessages").click(function(){
      $('#messagesContainer').slideToggle();
    })

    $("#messages #postReply").click(function(event){
        event.stopPropagation();

        var parent_id = $(this).attr('data-parent_id');
        var body = $('#messages #messageBody #body').val();

        $.ajax({
            url: '/admin/src/messages/process.php',
            data: {replyMessage: 'replyMessage', parent_id: parent_id, body: body},
            type: 'post',
            success: function(output) {

                $('#messageSent').remove();
                $("#messageBody").after('<li id="messageSent" style="display: none; ">Message Sent</li>');
                $('#messageSent').fadeIn();
            }
        });

        return false;
    });

    $("#messages #postMessage").click(function(event){
        event.stopPropagation();

        var user_to = $('#messages #user_to').val();
        var body = $('#messages #messageBody #body').val();

        $.ajax({
            url: '/admin/src/messages/process.php',
            data: {postMessage: 'postMessage', user_to: user_to, body: body},
            type: 'post',
            success: function(output) {

                $('#messageSent').remove();

                if(user_to == "<?php write_username(); ?>" || user_to == "allmods" || user_to == "everyone") {
                    var messageHTML =  '<li style="display:none;" data-id="' + output  + '" class="new justAdded">' + body + "<br /><small>From: </small> <? echo $_SESSION['GOB']['name'] ?> <small>Sent: </small> <?php echo date('Y-m-d') ?>  <br /><a href='#' class='delete'>Delete</a></li>";
  
                    $("#messagelist").prepend(messageHTML);
                    $("#messagelist li.justAdded").fadeIn().removeClass("justAdded");
                    $('#messages #user_to').val("allmods");
                    $('#messages #body').val("");
                    if($('#noMessages')){
                        $('#noMessages').remove();
                    }
                    $('#messageCount').text( parseInt($('#messageCount').text()) + 1);
                } else {
                    $("#messageBody").after('<li id="messageSent" style="display: none; ">Message Sent</li>');
                    $('#messageSent').fadeIn();
                }
            }
        });

        return false;
    });

    $("#messages").on("click", "#cancelReply", function(event){
        event.stopPropagation();

        //Reply canceled. Clean up.
        $('#postReply').removeAttr('data-parent_id');

        $('#messages .hide').removeClass('hide');
        $('#messageReply, #cancelReply, #postReply').addClass('hide');

    });

    $("#messagelist").on("click", "a.reply", function(event){
        event.stopPropagation();

        var messageID = $(this).parent().attr('data-id');

        //Mark message as read
        $(this).removeClass('new');
        $.ajax({
            url: '/admin/src/messages/process.php',
            data: {markMessageRead: 'markMessageRead', id: messageID},
            type: 'post',
            success: function(output) {
              $('#messageCount').text( parseInt($('#messageCount').text()) - 1);
            }
        });

        //Save the ID to be used as parent_id
        $('#postReply').attr('data-parent_id',messageID);

        //Switch form to reply mode
        $('#messages .hide').removeClass('hide');
        $('#postMessage, #messageTo').addClass('hide');

        return false;

    });

    $("#messagelist").on("click", "a.delete", function(event){
        event.stopPropagation();
        $(this).parent().fadeOut(300, function() {
            if(($("#messagelist li").length - 1) == 0) {
                $("#messagelist").prepend('<li id="noMessages">No Messages</li>');
            }
            
            if($(this).hasClass("new")){
              $('#messageCount').text( parseInt($('#messageCount').text()) - 1);
            }

            $(this).remove();
        });

        var messageID = $(this).parent().attr('data-id');

        $.ajax({
            url: '/admin/src/messages/process.php',
            data: {deleteMessage: 'deleteMessage', id: messageID},
            type: 'post',
            success: function(output) {
            }
        });

        return false;
    });
    
    $("#messagelist").on("click", "li", function(){
        $(this).removeClass('new');
        var messageID = $(this).attr('data-id');
        $.ajax({
            url: '/admin/src/messages/process.php',
            data: {markMessageRead: 'markMessageRead', id: messageID},
            type: 'post',
            success: function(output) {
              $('#messageCount').text( parseInt($('#messageCount').text()) - 1);
            }
        });
    });

});
</script>
<div id="messages">
    <h2>Messages</h2>
    <ul id="messagelist">
        <?php displayMessages(get_username()); ?>
    </ul>
    <div id="messageReply" class="hide">
        <h5>Reply</h5>
    </div>
    <div id="messageTo">
        <h5>Post New Message</h5>
        <label for="user_to">To</label>
        <?php displayBanditDropdown(); ?>
    </div>
    <div id="messageBody">
        <textarea id="body" rows="15" cols="25" name="body"></textarea>
    </div>
    
    <button id="cancelReply" class="left hide">Cancel</button>
    <button id="postReply" class="hide">Post Reply</button>
    <button id="postMessage">Post Message</button>
</div><!--  END /admin/src/messages.php -->