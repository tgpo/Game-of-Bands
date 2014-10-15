<?php

class Template extends GOB_Abstract {
    protected static $table = 'templates';

    public function Template($id){
        parent::__construct($id);
    }

    public function getList(){
        return sql_to_array('SELECT id,title,text FROM ' . static::$table . ' ORDER BY id ASC');
    }
}