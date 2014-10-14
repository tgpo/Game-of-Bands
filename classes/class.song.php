<?php 
require_once('class.abstract.php');

class Song extends GOB_Abstract {
	
	public function Song($id){
		$this->table_name = 'songs';
		parent::__construct($id);
	}
	
	public function get_round(){
		$this->get('round');
	}
	
	/**
	 * Used by JSON fetcher for SoundCloud..
	 * @return array:
	 */
	public function getInfo() {
		if(!is_array($this->data))
			$this->load ();
		
		return array_map('fix_quotes',array (
				"name" => $this->data ['name'],
				"url" => $this->data ['url'],
				"round" => $this->data ['round'],
				"lyricsheet" => preg_replace("/(<br\s*\/?>\s*)+/", "<br/>", nl2br ( $this->data ['lyricsheet'] )), //Ensure nl2br doesn't go overboard if user actually entered <br>'s and return.
				"banditLyrics" => $this->data ['lyrics'],
				"banditMusic" => $this->data ['music'],
				"banditVocals" => $this->data ['vocals'] 
		));
	}

	
	/* Voting functions */
	public function add_votes($type, $count) {
		insert_query ( 
				"UPDATE songs SET {$type}vote = {$type}vote + $count WHERE id=:id", 
				array (
						'id' => $this->id 
				) );
	}
	public function add_song_votes($count) {
		insert_query ( "UPDATE songs SET votes = votes + $count WHERE id=:id", 
				array (
						'id' => $this->id 
				) );
	}
	
	/**
	 * Bandit names are associated with what they did, for instance,
	 * "music" = The musician for the song, so, by getting "music", we are getting the bandit's name.
	 * 
	 * @param int $songID        	
	 * @param string $type
	 *        	the role played (music,lyrics,vocals)
	 * @return string The name of the bandit.
	 */
	public static function bandit($songID, $type = 'music') {
		$a = get_one ( "SELECT $type FROM songs WHERE id=$songID" );
		return $a[$type];
	}
}
