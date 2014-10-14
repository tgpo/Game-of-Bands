<?php
require_once('class.abstract.php');

class XmasTeam extends GOB_Abstract{
	
	private $team;
	
	public function XmasTeam($id){
		$this->table_name = 'xmas_teams';
		parent::__construct($id);
		$this->team = array();
		foreach(sql_to_array('SELECT name FROM bandits WHERE xmas_team_id='.$this->id) as $t){
			$this->team [] = $t['name'];
		}
	}
	
	public function inTeam($bandit){
		return in_array($bandit,$this->team);
	}
	
	public function getTeam(){
		return $this->team;
	}
	
	public function getCityId(){
		return $this->get('city_id');
	}
	public function setCityId($i){
		$this->set('city_id',$i);
	}
	public function getCityName(){
		return convert_id_to_name($this->getCityId(),'cities');
	}
	
	public function getStatus(){
		return $this->get('status');
	}
	public function setStatus($s){
		$this->set('status',$s);
	}
	
	public function getCreator(){
		return $this->get('creator');
	}
	public function setCreator($c){
		$this->set('creator',$c);
	}
	
	public function getCreatorName(){
		return convert_id_to_name($this->getCreator());
	}
	/**
	 * Set the teams creator by bandit name.
	 * @param unknown $n
	 */
	public function setCreatorName($n){
		$this->set('creator',get_bandit_id($n));
	}
	
	public function created(){
		return $this->get('created');
	}
	
	public function getCharity(){
		return $this->get('nominated_charity');
	}
	public function hasCharity(){
		return ($this->getCharity());
	}
	
	public function getUrl(){
		return $this->get('song_url');
	}
	public function hasUrl(){
		return ($this->get('song_url'));
	}
	public function setUrl($u){
		$this->set('song_url',$u);
	}
	
	public function getLyrics(){
		return $this->get('lyrics');
	}
	public function setLyrics($l){
		$this->set('lyrics',$l);
	}
	
	public function getSongName(){
		return $this->get('song_name');
	}
	public function setSongName($s){
		$this->set('song_name',$s);
	}
	
	public function getFilename(){
		return $this->get('filename');
	}
	public function setFilename($f){
		$this->set('filename',$f);
	}
	
}