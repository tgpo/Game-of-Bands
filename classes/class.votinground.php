<?php
require_once('../src/query.php');
require_once 'class.song.php';

mod_check();

//TEST 
//print_r ( new VotingRound ( 48 ) );

die("You probably didn't want this..");

/**
 * Calculates the highest voted bandits and song for the round from the round_id alone.
 * 
 * EG:
 * $r = new VotingRound ( 48 );
 * print "Best Track = {$r->song} \nBest Vocalist = {$r->vocals}\nBest Musician = {$r->music}\nBest Lyricist = {$r->lyrics}\n";
 * or: print_r(new VotingRound(48));
 * VotingRound Object
 * (
 * [round] => 48
 * [song] => The Blackest Reaches of My Sole Are Shod Only in The Pale Twilight Of Regret
 * [songvotes] => 3
 * [music] => scottpontiac
 * [musicvotes] => 3
 * [vocals] => evil_weevil
 * [vocalsvotes] => 2
 * [lyrics] => EDR7
 * [lyricsvotes] => 3
 * )
 * 
 * @author Aaron Were
 * @todo extend query class instead!
 */
class VotingRound {
	public $round;
	public $song, $songvotes, $music, $musicvotes, $vocals, $vocalsvotes, $lyrics, $lyricsvotes;
	/**
	 * Facilitates the simple vote calculations at the end of a round, determines WINNERS only.
	 * Keeps the data & queries separate from the HTML!
	 * Note: Each category is distinct and counted separately.
	 * 
	 * @param int $id
	 *        	the round ID you want votes counted for.
	 */
	public function __construct($id) {
		$this->round = $id;
		// Process song.
		$s = $this->get ( 'bestSong' );
		$this->songvotes = $s ['count'];
		$this->song = Song::name($s['id']);
		
		// Other types of votes.
		$m = $this->get ( 'bestMusician' );
		$this->music = Song::bandit ( $m ['id'], 'music' );
		$this->musicvotes = $m ['count'];
		
		$v = $this->get ( 'bestVocalist' );
		$this->vocals = Song::bandit ( $v ['id'], 'vocals' );
		$this->vocalsvotes = $v ['count'];
		
		$l = $this->get ( 'bestLyricist' );
		$this->lyrics = Song::bandit ( $l ['id'], 'lyrics' );
		$this->lyricsvotes = $l ['count'];
	}
	
	/**
	 * Simply gets the song_id from the votes table to match the category for the round you are after.
	 * Query sorts by number of votes for that category, and returns the first one.
	 * Could simply run SongVotes first and interrogate the songs table.. yes, that is probably better.
	 * 
	 * @param string $type        	
	 * @throws Exception
	 * @return array id='winning song ID', 'count' = number of votes.
	 */
	private function get($type = 'bestSong') {
		if (! $this->round) {
			throw new Exception ( "Use the beeps!" );
		}
		return get_one ( 
				"SELECT COUNT(*) as count, songID as id FROM votes WHERE type =:type AND roundID=:round ORDER BY count DESC ", 
				array (
						'type' => $type,
						'round' => $this->round 
				) );
	}
}