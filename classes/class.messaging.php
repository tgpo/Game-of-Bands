<?php
abstract class GOB_Message {
	private $id, $recipient, $recipient_id, $type, $title, $body,$ref;
	
	abstract function send(); //enforce contract
	
	function save(){
		// Save the message into our database.
		$id = insert_query ( "INSERT INTO sent_messages 
				SET type=:type, ref=:ref, recipient=:recipient, subject=:subject, text=:text, 
				mod_id=:mod_id, recipient_id=:recipient_id"
			, array (
				'recipient' => $this->recipient,
				'subject' => $this->title,
				'text' => $this->body,
				'mod_id' => bandit_id (), // Save the mod who sent it
				'recipient_id' => $this->recipient_id,
				'type' => $this->type,
				'ref' => $this->ref
		) );
		if (! $id) {
			// Something borked.. shit. We've already sent it!
			file_put_contents ( 'reddit_message_error_log', print_r ( $this, true ), FILE_APPEND );
		} else {
			ok ( 'Message sent.', $this->ref );
		}
	}
	function validate(){
		// Test each input to see if its valid, sends immediate fail on any error.
		if(!strlen($this->recipient)){
			fail("Invalid recipient.");
		}elseif(!strlen($this->subject)){
			fail("Invalid subject. ");
		}elseif(!strlen($this->text)){
			fail("Empty body text.");
		}elseif(!strlen($type)){
			fail("No type specified.");
		}
	}
}
class GOB_Email extends GOB_Message{
	public function GOB_Email($recipient, $title, $body, $recipient_id, $type) {
		$this->recipient = filter_var($recipient,FILTER_VALIDATE_EMAIL);  
		$this->title = $title;
		$this->body = $body;
		$this->recipient_id = $recipient_id; // the recipients id in our system
		$this->type = $type; // the type of the message.
		$this->validate();
	}
	
	function send(){
		//TODO: Implement email sending.. for sending messages to charities etc.
		// TODO: Test, need to determine the From: header, probably need to override it.
		if(mail($this->recipient,$this->title,$this->body)){
			// we sent it.
			ok();
		}else{
			fail("Message not sent.");
		}
	}
}
class GOB_Reddit extends GOB_Message {
	public function GOB_Message($recipient, $title, $body, $recipient_id, $type) {
		$this->recipient = $recipient; // We send messages to the mods via #subreddit
		$this->title = substr ( $title, 0, 100 ); // API needs a title of 100 chars max http://www.reddit.com/dev/api#POST_api_compose
		$this->body = substr ( $body, 0, 10000 ); // limited to 10k chars for body text.
		$this->recipient_id = $recipient_id; // the recipients id in our system
		$this->type = $type; // the type of message.. should be
		$this->validate();
	}
	function send() {
		global $reddit_user, $reddit_password;
		require_once ('../../lib/reddit.php');
		$reddit = new reddit ( $reddit_user, $reddit_password );
		$r = false;
		if ($this->type == 'city_message' || $this->type == 'bandit_pm') {
			if (DEBUG) {
				$this->recipient = '#' . SUBREDDIT;
			}
			// send a private message
			$r = $reddit->sendMessage ( $this->recipient, $this->title, $this->text );
			if (DEBUG) {
				error_log ( ">Saved REDDIT RESPONSE TO MOD_MESSAGE into reddit_message_response.txt" );
				file_put_contents ( 'reddit_message_response.txt', json_encode ( $r ), FILE_APPEND );
			}
		} elseif ($this->type == 'city_post') {
			if (DEBUG) {
				$this->recipient = SUBREDDIT;
			}
			// we'll create the post
			$r = $reddit->createStory( $this->title, null, $this->recipient, $this->text );
			if (DEBUG) {
				error_log ( ">Saved REDDIT RESPONSE TO POST_Message into reddit_post_response.txt" );
				file_put_contents ( 'reddit_post_response.txt', json_encode ( $r ), FILE_APPEND );
			}
		}
		if (! $r) {
			file_put_contents ( 'reddit_message_error_log', print_r ( $this, true ), FILE_APPEND );
			fail('Invalid type specifier, or complete failure to send. (MessageLogged)');
		} else {
			$this->ref = ''; // TODO: We need to get the reference to the message/post from the reddit API response.
			
		}
	}
}
