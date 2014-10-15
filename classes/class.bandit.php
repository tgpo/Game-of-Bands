<?php
class Bandit extends GOB_Abstract {
    public static $table = 'bandits';
    public function getList() {
        return sql_to_array ( "SELECT id,name FROM " . static::$table . " ORDER BY name ASC" );
    }

    public function isBanned(){
        return $this->get('banned') == 1;
    }
    public function isMod(){
        return $this->get('is_mod') == 1;
    }
    public function getWebsite(){
        return $this->get('website');
    }
    public function getTools(){
        return $this->get('tools');
    }
    public function getSoundcloud(){
        return $this->get('soundcloud_url');
    }
    public function getTeam(){
        return $this->get('xmas_team_id');
    }
    public function setTeam($t){
        $this->set('xmas_team_id',$t);
    }
    public function getStatus(){
        return $this->get('xmas_team_status');
    }
    /**
     * One of: pending,approved or banned.. banned probably isn't necessary.
     * @param unknown $s
     */
    public function setStatus($s){
        $this->set('status',$s);
    }
    public function getRealName(){
        return $this->get('real_name');
    }
    public function setRealName($s){
        $this->set('real_name',$s);
    }
    public function getEmail(){
        return $this->get('email');
    }
    public function getAgreedToTermsAndConditionsDate(){
        return $this->hasAgreedToTermsAndConditions() ? $this->get('xmas_tc') : false;
    }
    public function hasAgreedToTermsAndConditions(){
        return ($this->get('xmas_tc') !== 0)  ? true : false;
    }
    public function setAgreed(){
        $this->set('xmas_tc','NOW()'); //wonder, would this work..
    }
    public function hasPurchased(){
        return ($this->get('xmas_purchased')) ? true : false;
    }
    public function getPaymentAmount(){
        return $this->get('xmas_paid');
    }
    public function getShare(){
        return $this->get('xmas_share');
    }
    public function setShare($s){
        $this->set('xmas_share',$s);
    }
    public function hasAgreedToShareChange(){
        return $this->get('xmas_share_change') == 1;
    }
    public function setAgreedToShareChange(){
        $this->set('xmas_share_change',1);
    }
}