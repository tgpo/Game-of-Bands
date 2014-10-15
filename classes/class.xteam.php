<?php
require_once ('class.abstract.php');
require_once ('class.bandit.php');
class XmasTeam extends GOB_Abstract {
    private $team;
    private $team_data;
    public static $table = 'xmas_teams';
    public function XmasTeam($id) {
        parent::__construct ( $id );
        $this->team = array ();
        foreach ( sql_to_array ( 'SELECT * FROM ' . Bandit::$table . ' WHERE xmas_team_id=' . $this->id ) as $t ) {
            $this->team [] = $t ['name'];
            $this->team_data [] = $t;
        }
    }
    public function hasTeamAgreedToTermsAndCondtions() {
        foreach ( $this->team_data as $t ) {
            if ($t ['xmas_tc'] == '') {
                return false;
            }
        }
        return true;
    }
    public function hasTeamAgreedToShareChanges() {
        foreach ( $this->team_data as $t ) {
            if ($td ['xmas_share_change'] == 0) {
                return false;
            }
        }
        return true;
    }
    public function getTeamApprovalButtons() {
        foreach ( $this->team_data as $t ) {
            if ($td ['xmas_team_status'] == 'pending') {
                echo a_bandit ( $t ['name'] ) . ' is still pending. <input type="button" value="Approve" class="approve_member"/> <br />';
            }
        }
    }
    public function getShare($name) {
        foreach ( $this->team_data as $t ) {
            if ($t ['name'] == $name) {
                return $t ['xmas_share'];
            }
        }
        return false;
    }
    public function inTeam($bandit) {
        return in_array ( $bandit, $this->team );
    }
    public function getTeam() {
        return $this->team;
    }
    public function getCityId() {
        return $this->get ( 'city_id' );
    }
    public function setCityId($i) {
        $this->set ( 'city_id', $i );
    }
    public function getCityName() {
        return City::name ( $this->getCityId () );
    }
    public function getStatus() {
        return $this->get ( 'status' );
    }
    public function setStatus($s) {
        if (! $s == 'pending') {
            mod_check ();
        }
        $this->set ( 'status', $s );
    }
    public function getCreator() {
        return $this->get ( 'creator' );
    }
    public function setCreator($c) {
        $this->set ( 'creator', $c );
    }
    public function getCreatorName() {
        return Bandit::name ( $this->getCreator () );
    }
    /**
     * Set the teams creator by bandit name.
     *
     * @param unknown $n
     */
    public function setCreatorName($n) {
        $this->set ( 'creator', get_bandit_id ( $n ) );
    }
    public function created() {
        return $this->get ( 'created' );
    }
    public function getCharity() {
        return $this->get ( 'nominated_charity' );
    }
    public function setCharity($s) {
        $this->set ( 'nominated_charity', $s );
        // Record each nomination, some charities might be nominated several times.
        $n = new Nomination(false);
        $n->setName($this->getName()); //name nomination after the team name, could be team_id..
        $n->setBandit(get_bandit_id());
        $n->setCharity($s);
        $n->save();
    }
    public function hasCharity() {
        return ($this->getCharity ());
    }
    public function getUrl() {
        return $this->get ( 'song_url' );
    }
    public function hasUrl() {
        return ($this->get ( 'song_url' ));
    }
    public function setUrl($u) {
        $this->set ( 'song_url', $u );
    }
    public function getLyrics() {
        return $this->get ( 'lyrics' );
    }
    public function setLyrics($l) {
        $this->set ( 'lyrics', $l );
    }
    public function getSongName() {
        return $this->get ( 'song_name' );
    }
    public function setSongName($s) {
        $this->set ( 'song_name', $s );
    }
    public function getFilename() {
        return $this->get ( 'filename' );
    }
    public function setFilename($f) {
        $this->set ( 'filename', $f );
    }
    public static function create_team() {
        $city = City::getCity ();

        $team_name = filter_input ( INPUT_GET, 'team_name', FILTER_SANITIZE_STRING );

        $team = parent::create ( $team_name );
        $team->setCreator ( bandit_id () );
        $team->setCityId ( $city->getId () );
        $team->save ();
        return $team;
    }

    /**
     * Find teams within a certain distance from latitude and longitude coordinates
     * Contains an array of team ids, names, city ids and city names
     *
     * @param double $lat
     * @param double $lng
     * @param int $distance
     *            in KM's, defaults to 500
     * @param int $count
     *            defaults to 20
     * @return boolean|Ambigous <boolean, multitype:>
     */
    public static function find_teams($lat, $lng, $distance = 500, $count = 20) {
        if (! $lat || ! $lng)
            return false;

            // Attempt to match via latitude/longitude : http://stackoverflow.com/a/574762
            // Modified to join on teams table compared to the city it's linked with.
        return sql_to_array ( '
	SELECT x.id as tid,x.name as team,c.id as cid,c.name as city,
	( 6371 *
		acos(
			cos( radians(' . $lat . ') )
		  * cos( radians(c.lat) )
		  * cos( radians(' . $lng . ') - radians(c.lng) )
		  + sin( radians(' . $lat . ') )
				  * sin( radians(c.lat)) )
	 )   AS distance
	FROM ' . City::$table . ' c JOIN ' . static::$table . ' x ON x.city_id = c.id
	HAVING distance < ' . $distance . '
	ORDER BY distance ASC
	LIMIT 0 , ' . $count )        // Find first 20 teams within 500 kms, ordered by closest
        ;
    }
    public function getList($id) {
        return sql_to_array ( "SELECT id,name FROM " . static::$table . " WHERE city_id=$id ORDER BY name ASC" );
    }
}