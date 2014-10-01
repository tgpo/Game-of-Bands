<?php
require_once('../src/query.php');
require_once ('class.song.php');

mod_check();

class SongVotes {
	private $round;
	private $vote_types = array (
			'bestSong' => false,
			'bestMusician' => 'music',
			'bestLyricist' => 'lyrics',
			'bestVocalist' => 'vocals' //Another throwback to non-normalized data.. :-(
	);
	/**
	 * Go through all songs in the round that have received votes, tally its votes in each category.
	 * Update the vote counts in the songs table for each type based on votes received for the round specified.
	 * 
	 * @param int $round_id        	
	 */
	public function __construct($round_id) {
		$this->round = $round_id;
		
		/* Iterate over every song with votes in the round */
		$songs = pdo_query ( 
				"SELECT DISTINCT songID from votes WHERE roundID=$round_id" );
		
		foreach ( $songs as $s ) {
			// We'll use a Song object to associate the votes, and use its methods to save them.
			$song = new Song ( $s ['songID'] );

			// Calculate each type of vote for the song and save.
			foreach ( $this->vote_types as  $vote_type => $type ) {
				$votes = pdo_query("
						SELECT COUNT(*) as count 
						FROM votes 
						WHERE type =:type 
						AND roundID=:round 
						AND songID=:song 
						ORDER BY count DESC", 
						array(
								'type' => $vote_type, 
								'round' => $round_id, 
								'song' => $song->id)
				);
				if($type === false){
					$song->add_song_votes($votes['count']);
				}else{
					$song->add_votes($type,$votes['count']);
				}
			}
		}
	}
}