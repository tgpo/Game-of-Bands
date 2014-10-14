<?php 
require_once('../src/query.php');

/**
 * Utility functions that are unique to a Round.
 * 
 */
class Round extends GOB_Abstract{	
	protected static $table = 'rounds';
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function getData(){
		if(!is_array($this->data))
			$this->load();
		return $this->data;
	}
	
	/**
	 * Checks if this round is currently votable.
	 * Used by SoundCloud songfetching JSON interface
	 * @return boolean
	 */
	public function is_active(){
		return voting_is_active($this->id);
	}
	
	/**
	 * Gets the theme
	 * @return String
	 */
	public static function get_theme($id){
		return $this->get('theme',$id);
	}
}
