<?php
require_once ('class.abstract.php');
class City extends GOB_Abstract {
    public static $table = 'cities';
    public function City($id) {
        parent::__construct ( $id );
    }
    public function getSubreddit() {
        return $this->get ( 'subreddit' );
    }
    public function getPostTemplate() {
        return $this->get ( 'post_template_id' );
    }
    public function getMessageTemplate() {
        return $this->get ( 'message_template_id' );
    }
    public function hasMessagedMods() {
        if (strlen ( $this->get ( 'messaged_mods' ) ) > 0) {
            return true;
        }
        return false;
    }
    public function hasPostedThread() {
        if (strlen ( $this->get ( 'post' ) ) > 0) {
            return true;
        }
        return false;
    }
    public function created() {
        return $this->get ( 'created' );
    }
    public function lat() {
        return $this->get ( 'lat' );
    }
    public function setLat($l) {
        $this->set ( 'lat', $l );
    }
    public function lng() {
        return $this->get ( 'lng' );
    }
    public function setLng($l) {
        $this->set ( 'lng', $l );
    }

    /**
     * ****************************************** Static functions
     */
    public static function getList() {
        return sql_to_array ( "SELECT id,name, (SELECT COUNT(id) FROM " . XmasTeam::$table . " WHERE city_id = cities.id) as teams FROM " . static::$table . " HAVING teams > 0 ORDER BY name ASC" ); // add team count?
    }
    public static function getCity() {
        $city_name = filter_input ( INPUT_GET, 'city_name', FILTER_SANITIZE_STRING );
        $lat = filter_input ( INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT );
        $lng = filter_input ( INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT );

        if (! $lat || ! $lng || ! $city_name) {
            return false;
        }
        // If the city isn't in the system yet, we should create it.
        $city_id = City::id ( $city_name ); // get_one('SELECT id FROM '. static::$table .' WHERE name=:name',array('name'=>$city_name));

        if (! $city_id) {
            $city = City::create ( $city_name );
            $city->setLat ( $lat );
            $city->setLng ( $lng );
            return $city;
        } else {
            return new City ( $city_id );
        }
    }
}