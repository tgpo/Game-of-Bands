<?php 
require_once('../src/query.php');

class Song {
	private $id;
	private $data;
	
	public function __construct($id) {
		if(!is_numeric($id)){
			$id = Song::id($id);
		}
		$this->id = $id;
	}
	private function load() {
		$this->data = pdo_query ( "SELECT * FROM songs WHERE id=:id", 
				array (
						'id' => $this->id 
				) );
	}
	
	public function get_round(){
		if(!is_array($this->data))
			$this->load();
		return $this->data['round'];
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
				"lyricsheet" => nl2br ( $this->data ['lyricsheet'] ),
				"banditLyrics" => $this->data ['lyrics'],
				"banditMusic" => $this->data ['music'],
				"banditVocals" => $this->data ['vocals'] 
		));
	}

	
	/* Admin voting functions */
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
	
	/********** Static functions */
	
	/**
	 * We don't save the name of the song in the votes table..
	 * so we need to get it now.
	 * 
	 * @param
	 *        	int ID number of the song.
	 * @return String
	 */
	public static function name($id) {
		return get_one ( "SELECT name FROM songs WHERE id=$id" )['name'];
	}
	
	/**
	 * Get the ID number of the Song from its Name.
	 * 
	 * @param string $name        	
	 * @return int
	 */
	public static function id($name) {
		return get_one ( "SELECT id FROM songs WHERE name=$name" )['id'];
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
		return get_one ( "SELECT $type FROM songs WHERE id=$songID" )[$type];
	}
}