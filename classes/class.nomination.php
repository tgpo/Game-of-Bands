<?php
class Nomination extends GOB_Abstract {
    protected static $table = 'nominations';
    public function Nomination($name) {
        parent::__construct ( $name );
    }
    public function getList() {
        return sql_to_array ( 'SELECT * FROM ' . static::$table . ' ORDER BY timestamp ASC' );
    }
}