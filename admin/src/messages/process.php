<?php

require_once( '../../includes/gob_admin.php' );
require_once( '../../../src/secrets.php' );
require_once( '../../../src/query.php' );

if( isset($_POST['postMessage']) ){
  postMessage();

} elseif ( isset($_POST['markMessageRead']) ) {
  readMessage();

} elseif( isset($_POST['deleteMessage']) ){
  deleteMessage();

}

function postMessage(){
    $db = database_connect();
    $to = $_POST["user_to"];
    $from = $_POST["user_from"];
    $body = $_POST["body"];
    $date = date('Y-m-d');

    switch ($to) {
        case "allmods":
            $result = $db->query('SELECT * FROM bandits WHERE is_mod = 1');

            while($bandits = mysql_fetch_array($result)){
                $user_to = $bandits['name'];

                $messages = $db->prepare('INSERT INTO messages (user_to, user_from, body, date_sent) VALUES (:to, :from, :body, :date)');
                $messages->execute(array('to' => $to, 'from' => $from, 'body' => $body, 'date' => $date));
            }

            break;

        case "everyone":
            if( is_mod() ) {
                $result = $db->query('SELECT * FROM bandits');

                while($bandits = mysql_fetch_array($result)){
                    $user_to = $bandits['name'];

                    $messages = $db->prepare('INSERT INTO messages (user_to, user_from, body, date_sent) VALUES (:to, :from, :body, :date)');
                    $messages->execute(array('to' => $to, 'from' => $from, 'body' => $body, 'date' => $date));
                }
            }

            break;

        default:
            $messages = $db->prepare('INSERT INTO messages (user_to, user_from, body, date_sent) VALUES (:to, :from, :body, :date)');
            $messages->execute(array('to' => $to, 'from' => $from, 'body' => $body, 'date' => $date));
    }

    echo $db->lastInsertId();

}

function readMessage(){
    $db = database_connect();
    $id = $_POST['id'];

    $messages = $db->prepare('UPDATE messages SET new = :new WHERE id = :id');
    $messages->execute(array('new' => false, 'id' => $id));

}

function deleteMessage(){
    $db = database_connect();
    $id = $_POST['id'];

    $messages = $db->prepare('DELETE FROM messages WHERE id = :id');
    $messages->execute(array('id' => $id));

}

?>