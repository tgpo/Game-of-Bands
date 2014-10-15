<?php
use OAuth2\InvalidArgumentException;
require_once ('query.php');
/**
 * Ensure database abstraction for sub-classes
 *
 * @author grizly
 *
 */
abstract class GOB_Abstract {

    /**
     * Each sub-class must override this method.
     */
    abstract function getList();
    /**
     * Every entity should have a corresponding row in a table, this is the ID that references that row.
     *
     * @var int
     */
    protected $id;

    /**
     * Each row has a bunch of fields..
     * this is that data! key = field_name, value = field_value
     *
     * @var mixed
     */
    protected $data;

    /**
     * Each Entity is stored in a unique table, this is that table name.
     * IMPORTANT!
     *
     * @var string
     */
    protected static $table;

    /**
     * If we change the entity, we should trigger a save before it is destroyed.
     *
     * @var bool
     */
    protected $changed;

    /**
     * Begin!
     *
     * @param unknown $id
     */
    public function __construct($id) {
        $this->changed = false;
        if ($id = false) {
            // Creating new
            if (DEBUG)
                error_log ( "New!" );
        } elseif (! is_numeric ( $id )) {
            $id = self::id ( $id );
        }
        $this->id = $id;

        if (DEBUG) {
            error_log ( "Instatiated " . __CLASS__ . " from id: " . $id );
        }
    }
    protected function load() {
        $this->data = pdo_query ( "SELECT * FROM " . static::$table . " WHERE id=:id", array ('id' => $this->id ) );
    }
    public function getId() {
        return ($this->get ( 'id' )) ? $this->get ( 'id' ) : false;
    }
    public function getName() {
        return ($this->get ( 'name' )) ? $this->get ( 'name' ) : '';
    }
    public function setName($n) {
        $this->set ( 'name', $n );
    }
    protected function get($field) {
        if (! is_array ( $this->data ))
            $this->load ();
        return $this->data [$field];
    }
    protected function set($field, $value) {
        $this->data [$field] = $value;
        $this->changed = true;
    }
    public function hasChanged() {
        return $this->changed;
    }
    public function save() {
        // See if we already have one.. does a query. :-( There has to be a better way.. but.. well..
        if (static::id ( $this->getName () )) {
            if (DEBUG)
                error_log ( "UPDATING: " . __CLASS__ . ' -> ' . $this->getName () );
            $this->update ();
        } else {
            if (DEBUG)
                error_log ( "CREATING: " . __CLASS__ . ' -> ' . $this->getName () );
            $this->id = $this->insert ();
            $this->data ['id'] = $this->id;
        }
    }

    /**
     * Create a new row, should only be called by $obj->save();
     *
     * @param unknown $keys
     * @param unknown $values
     * @return number
     */
    private function insert($keys, $values) {
        $sql = "INSERT INTO `" . static::$table . "` " . "(`" . implode ( "`,`", $keys ) . "`) " . "VALUES (";
        $temp = array ();
        foreach ( $values as $v )
            if (strcmp ( $v, "NULL" ) == 0)
                $temp [] = 'NULL';
            else
                $temp [] = "'" . $v . "'";
        $sql .= implode ( ",", $temp ) . ")";
        return insert_query ( $sql );
    }

    /**
     * Save any changes, should only be called by $obj->save();
     */
    private function update() {
        $keys = array_keys ( $this->data );
        $values = array_values ( $this->data );

        $sql = "UPDATE `" . static::$table . "` SET ";
        for($i = 0; $i < count ( $keys ); $i ++) {
            $k = $keys [$i];
            $v = $values [$i];
            if ($k == 'id')
                continue; // skip ID's, we will not change them.
            if (strcmp ( $v, 'NULL' ) == 0) {
                $sql .= "`$k`=NULL";
                throw new InvalidArgumentException ( "$v for $k is incorrect." );
            } else
                $sql .= "`$k`='$v'";
            $sql .= (($i == count ( $keys ) - 1) ? "" : " , ");
        }
        $sql .= " WHERE id=:id";
        return pdo_query ( $sql, array ('id' => $this->id ) );
    }

    /**
     * Permanently remove the Entity.
     */
    public function delete() {
        pdo_query ( "DELETE FROM " . static::$table . " WHERE id=:id LIMIT 1", array ('id' => $this->id ), false );
    }

    /**
     * ******************** Static functions
     */
    public static function id($name) {
        $a = pdo_query ( "SELECT id FROM " . static::$table . " WHERE name=:name LIMIT 1", array ('name' => $name ) );
        return $a ['id'];
    }
    public static function name($id) {
        return static::dbget ( 'name', $id );
    }

    /**
     * Convert's an ID number from the class database table into the specified field's value.
     *
     * Can be called: Charity::name(2); etc.
     *
     * @param string $field
     *            the name of the field to retrieve
     * @param int $id
     *
     * @throws InvalidArgumentException on non-int
     */
    public static function dbget($field, $id) {
        if (! is_int ( $id )) {
            throw new InvalidArgumentException ( "Unable to select `id` with non-integer value." );
        }
        $a = pdo_query ( "SELECT {$field} FROM " . static::$table . " WHERE id=:id LIMIT 1", array ('id' => $id ) );
        return $a [$field];
    }

    /**
     * Object Factory method..
     * Creates an object from scratch, saves it instantly.
     * Called like: $charity = Charity::create('pigs in space');, or $team = XmasTeam::create('New Team on The Rock');
     *
     * @param string $name
     * @return Object
     */
    public static function create($name) {
        $type = __CLASS__;
        $c = new $type ( false ); // We don't have an ID number for new ones.
        $c->setName ( $name ); // Need some data to save with the object, might as well be the name.
        $c->save ();
        return $c;
    }
}