<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: PayoutOption.class.php 19104 2008-07-14 08:23:51Z mfric $
*
*   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement, 
*   Version 1.0 (the "License"); you may not use this file except in compliance 
*   with the License. You may obtain a copy of the License at
*   http://www.qualityunit.com/licenses/license
* 
*/

/**
 * @package GwtPhpFramework
 */
class Gpf_Db_FieldGroup extends Gpf_DbEngine_Row {

    const ENABLED = "E";
    const DISABLED = "D";
    
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_FieldGroups::getInstance());
        parent::init();
    }
    
    public function setName($name) {
    	$this->set(Gpf_Db_Table_FieldGroups::NAME, $name);
    }
    
    public function getName() {
        return $this->get(Gpf_Db_Table_FieldGroups::NAME);
    }
    
    public function setType($type) {
        $this->set(Gpf_Db_Table_FieldGroups::TYPE, $type);
    }
    
    public function setStatus($status) {
        $this->set(Gpf_Db_Table_FieldGroups::STATUS, $status);
    }
    
    public function setOrder($value) {
        $this->set(Gpf_Db_Table_FieldGroups::ORDER, $value);
    }
        
    public function setID($id) {
        $this->set(Gpf_Db_Table_FieldGroups::ID, $id);
    }
    
    public function getID() {
        return $this->get(Gpf_Db_Table_FieldGroups::ID);
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_FieldGroups::ACCOUNTID, $accountId);
    }
}

?>
