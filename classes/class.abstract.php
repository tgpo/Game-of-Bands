<?php
require_once ('query.php');
/**
 * Ensure database abstraction for sub-classes
 *
 * @author grizly
 *        
 */
abstract class GOB_Abstract {
	protected $id;
	protected $data;
	protected static $table;
	
	public function __construct($id) {
		if (! is_numeric ( $id )) {
			$id = self::id ( $id );
		}
		$this->id = $id;
	}
	protected function load() {
		$this->data = pdo_query ( "SELECT * FROM " . static::$table. " WHERE id=:id", array (
				'id' => $this->id 
		) );
	}
	public function getId(){
		return $this->get('id');
	}
	public function getName(){
		return $this->get('name');
	}
	protected function get($field) {
		if (! is_array ( $this->data ))
			$this->load ();
		return $this->data [$field];
	}
	protected function set($field, $value) {
		if (! is_array ( $this->data ))
			$this->load ();
		$this->data [$field] = $value;
	}
	protected function save() {		
		$resultInsert = pdo_query ( "SHOW COLUMNS FROM ". static::$table . " WHERE Field NOT IN ('id')" );
		print_r ( $resultInsert );
		$field_names = array ();
		foreach ( $resultInsert as $row ) {
			$field_names [] = $row ['Field'];
			$array = array_intersect_key ( $this->data, array_flip ( $field_names ) );
		}
		
		foreach ( $array as $key => $value ) {
			$value = mysql_real_escape_string ( $value );
			$value = "'$value'";
			$updates [] = "$key = $value";
		}
		$implodeArray = implode ( ', ', $updates );
		$sql = sprintf ( "UPDATE %s SET %s WHERE id='%s'", static::$table, $implodeArray, $this->id );
		return insert_query ( $sql );
	}
	
	
	
	/**
	 * ******************** Static functions
	 */
	public static function id($name) {
		$a = pdo_query ( "SELECT id FROM ". static::$table . " WHERE name=:name LIMIT 1", array (
				'name' => $name 
		) );
		return $a ['id'];
	}
	public static function name($id) {
		return static::dbget ( 'name', $id );
	}
	public static function dbget($field, $id) {
		$a = pdo_query ( "SELECT {$field} FROM ". static::$table . " WHERE id=:id LIMIT 1", array (
				'id' => $id 
		) );
		return $a [$field];
	}
}