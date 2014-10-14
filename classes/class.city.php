<?php

require_once('class.abstract.php');

class City extends GOB_Abstract{
	
	public function City($id){
		$this->table_name = 'cities';
		parent::__construct($id);
	}
	
	public function getCharity(){
		return $this->get('nominated_charity');
	}
}