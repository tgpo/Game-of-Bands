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

} elseif( isset($_POST['replyMessage']) ){
  replyMessage();

}

function postMessage(){
    $db = database_connect();
    $user_to = $_POST["user_to"];
    $from = $_SESSION['GOB']['name'];
    $body = $_POST["body"];
    $date = date('Y-m-d');

    switch ($user_to) {
        case "allmods":
            $bandits = $db->query('SELECT * FROM bandits WHERE is_mod = 1');

            foreach ($bandits as $bandit) {
                $user_to = $bandit['name'];

                $messages = $db->prepare('INSERT INTO messages (user_to, user_from, body, date_sent) VALUES (:user_to, :from, :body, :date)');
                $messages->execute(array('user_to' => $user_to, 'from' => $from, 'body' => $body, 'date' => $date));
            }

            break;

        case "everyone":
            if( is_mod() ) {
                $bandits = $db->query('SELECT * FROM bandits');

                foreach ($bandits as $bandit) {
                    $user_to = $bandit['name'];

                    $messages = $db->prepare('INSERT INTO messages (user_to, user_from, body, date_sent) VALUES (:user_to, :from, :body, :date)');
                    $messages->execute(array('user_to' => $user_to, 'from' => $from, 'body' => $body, 'date' => $date));
                }
            }

            break;

        default:
            $messages = $db->prepare('INSERT INTO messages (user_to, user_from, body, date_sent) VALUES (:user_to, :from, :body, :date)');
            $messages->execute(array('user_to' => $user_to, 'from' => $from, 'body' => $body, 'date' => $date));
    }

    echo $db->lastInsertId();

}

function replyMessage(){
    $db = database_connect();
    $parent_id = $_POST["parent_id"];
    $from = $_SESSION['GOB']['name'];
    $body = $_POST["body"];
    $date = date('Y-m-d');

    $query = $db->prepare('SELECT user_from FROM messages WHERE id=:parent_id limit 1');
    $query->execute(array('parent_id' => $parent_id));
    $user_to  = $query->fetch();
    $user_to = $user_to['user_from'];

    $messages = $db->prepare('INSERT INTO messages (user_to, user_from, parent_id, body, date_sent) VALUES (:user_to, :from, :parent_id, :body, :date)');
    $messages->execute(array('user_to' => $user_to, 'from' => $from, 'parent_id' => $parent_id, 'body' => $body, 'date' => $date));

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