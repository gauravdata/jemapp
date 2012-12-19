<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CustomerForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Features_PerformanceRewards_RuleForm extends Gpf_View_FormService {
    
    /**
     * @return Pap_Db_Rule
     */
    protected function createDbRowObject() {
        return new Pap_Db_Rule();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Rule");
    }
    
    /**
     *
     * @service rule add
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }
    
    /**
     *
     * @service rule read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     *
     * @service rule write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     * @service rule delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
    
    /**
     * @service rule read
     * @return Gpf_Data_RecordSet
     */
    public function loadConditions(Gpf_Rpc_Params $params) {
        return $this->createConditionsRecordSet();
    }
    
    /**
     * @service rule read
     * @return Gpf_Data_RecordSet
     */
    public function loadActions(Gpf_Rpc_Params $params) {
        return $this->createActionsRecordSet();
    }
    
    private function createConditionsRecordSet() {
        $recordset = new Gpf_Data_RecordSet();
        $recordset->setHeader(array('id', 'name'));
        
        foreach(Pap_Features_PerformanceRewards_Condition::getAllConditions() as $code) {
            try {
                $recordset->add(array((string)$code,
                    Pap_Features_PerformanceRewards_Condition::toString($code)));
            } catch (Pap_Features_PerformanceRewards_UnknownRuleException $e) {}
        }
        return $recordset;
    }
    
    private function createActionsRecordSet() {
        $recordset = new Gpf_Data_RecordSet();
        $recordset->setHeader(array('id', 'name'));
        
        foreach(Pap_Features_PerformanceRewards_Action::getAllActions() as $code) {
            try {
                $recordset->add(array((string)$code,
                    Pap_Features_PerformanceRewards_Action::toString($code)));
            } catch (Pap_Features_PerformanceRewards_UnknownRuleException $e) {}
        }
        return $recordset;
    }
}

?>
