<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_UserPayoutOption extends Gpf_DbEngine_Row {    
        
    public function __construct(){
        parent::__construct();      
    }
    
    protected function init() {
        $this->setTable(Pap_Db_Table_UserPayoutOptions::getInstance());
        parent::init();
    }
    
    public function setUserId($userId) {
    	$this->set(Pap_Db_Table_Users::ID, $userId);
    }
    
    public function setFormFieldId($formFieldId) {
        $this->set(Gpf_Db_Table_FormFields::ID, $formFieldId);
    }
    
    public function getValue() {
        $value = new Gpf_Plugins_ValueContext($this->get(Pap_Db_Table_UserPayoutOptions::VALUE));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.PayoutOption.getValue', $value);
        return $value->get();
    }
    
    public function setValue($value) {
        $value = new Gpf_Plugins_ValueContext($value);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.PayoutOption.setValue', $value);
        $this->set(Pap_Db_Table_UserPayoutOptions::VALUE, $value->get());
    }
    
    public function save() {
        if ($this->isSetRow()) {
            $this->update();
        } else {
            $this->insert();
        }
    }
    
    private function isSetRow() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Pap_Db_Table_UserPayoutOptions::VALUE, "payoutDataValue");
        $select->from->add(Pap_Db_Table_UserPayoutOptions::getName());
        $select->where->add(Pap_Db_Table_Users::ID, "=", $this->get(Pap_Db_Table_Users::ID));
        $select->where->add(Gpf_Db_Table_FormFields::ID, "=", $this->get(Gpf_Db_Table_FormFields::ID));
        
        $recordSet = $select->getAllRows();
        
        if ($recordSet->getSize() > 0) {
        	return true;
        }
        
        return false;
    }
    
    public function update() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_UserPayoutOptions::getName());
        $update->set->add(Pap_Db_Table_UserPayoutOptions::VALUE, $this->get(Pap_Db_Table_UserPayoutOptions::VALUE));
        $update->where->add(Pap_Db_Table_Users::ID, "=", $this->get(Pap_Db_Table_Users::ID));
        $update->where->add(Gpf_Db_Table_FormFields::ID, "=", $this->get(Gpf_Db_Table_FormFields::ID));
        $update->updateOne();
    }
}

?>
