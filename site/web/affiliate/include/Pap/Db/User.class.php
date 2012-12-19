<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Andrej Harsani
*   @since Version 1.0.0
*   $Id: User.class.php 29208 2010-09-03 11:25:50Z iivanco $
*
*   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
*   Version 1.0 (the "License"); you may not use this file except in compliance
*   with the License. You may obtain a copy of the License at
*   http://www.qualityunit.com/licenses/license
*
*/

/**
 * @package PostAffiliatePro
 */
class Pap_Db_User extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Pap_Db_Table_Users::getInstance());
        parent::init();
    }

    public function getType() {
        return $this->get(Pap_Db_Table_Users::TYPE);
    }

    public function getId() {
        return $this->get(Pap_Db_Table_Users::ID);
    }

    public function getParentUserId() {
        return $this->get(Pap_Db_Table_Users::PARENTUSERID);
    }
    
    public function getOriginalParentUserId() {
        return $this->get(Pap_Db_Table_Users::ORIGINAL_PARENT_USERID);
    }

    public function setParentUserId($userid) {
        $this->set(Pap_Db_Table_Users::PARENTUSERID, $userid);
    }
    
    public function setType($type) {
        $this->set(Pap_Db_Table_Users::TYPE, $type);
    }

    public function setOriginalParentUserId($userid) {
        $this->set(Pap_Db_Table_Users::ORIGINAL_PARENT_USERID, $userid);
    }

    public function setId($userid) {
        $this->set(Pap_Db_Table_Users::ID, $userid);
    }

    public function insert() {
        parent::insert();
    }
    
    protected function generatePrimaryKey() {
        parent::generatePrimaryKey();
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.generatePrimaryKey', $this);
    }

    public function update($updateColumns = array()) {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.onUpdate', $this);
        parent::update($updateColumns);
    }

    public function setDeleted($deleted) {
        $this->set(Pap_Db_Table_Users::DELETED, $deleted);
    }

    public function setMinimumPayout($minimupPayout) {
        $this->set(Pap_Db_Table_Users::MINIMUM_PAYOUT, $minimupPayout);
    }

    public function getRefId() {
        return $this->get(Pap_Db_Table_Users::REFID);
    }

    public function setRefId($refid) {
        $this->set(Pap_Db_Table_Users::REFID, $refid);
    }

    public function getAccountUserId() {
        return $this->get(Pap_Db_Table_Users::ACCOUNTUSERID);
    }
    
    public function setAccountUserId($accountUserId) {
        $this->set(Pap_Db_Table_Users::ACCOUNTUSERID, $accountUserId);
    }

    public function getPayoutOptionId() {
        return $this->get(Pap_Db_Table_Users::PAYOUTOPTION_ID);
    }

    public function getMinimumPayout() {
        return $this->get(Pap_Db_Table_Users::MINIMUM_PAYOUT);
    }
    
    public function getDeleted(){
        return $this->get(Pap_Db_Table_Users::DELETED);
    }
}

?>
