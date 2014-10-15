<?php
require_once ('class.abstract.php');
class Charity extends GOB_Abstract {
    public static $table = 'charities';
    public function Charity($id) {
        parent::__construct ( $id );
    }
    public function getEmail() {
        return $this->get ( 'email' );
    }
    public function setEmail($e) {
        $this->set ( 'email', $e );
    }
    public function getLocality() {
        return $this->get ( 'locality' );
    }
    public function setLocality($l) {
        $this->set ( 'locality', $l );
    }
    public function getCharityId() {
        return $this->get ( 'charity_id' );
    }
    public function setCharityId($i) {
        $this->set ( 'charity_id', $i );
    }
    public function getStatus() {
        return $this->get ( 'status' );
    }
    public function setStats($s) {
        $this->set ( 'status', $s );
    }
    public function getModId() {
        return $this->get ( 'mod_id' );
    }
    public function setModId($i) {
        $this->set ( 'mod_id', $i );
    }
    public static function getList() {
        return sql_to_array ( 'SELECT id,name FROM ' . static::$table . ' ORDER BY name ASC' );
    }
}