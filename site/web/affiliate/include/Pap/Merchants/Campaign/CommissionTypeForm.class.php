<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
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
class Pap_Merchants_Campaign_CommissionTypeForm extends Gpf_Object {
    
    /**
     * @service commission read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = $this->getFormObject($params);
        $form->loadForm();
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionTypeForm.loadSettings', $form);
        
        return $form;
    }
    
    /**
     * @service commission write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = $this->getFormObject($params);
        if (!$form->existsField(Pap_Db_Table_CommissionTypes::NAME)) {
            $form->setField(Pap_Db_Table_CommissionTypes::NAME, '');
        }
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionTypeForm.saveSettings', $form);
        
        try {
            $form->saveForm();
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_("Error while saving commission: %s", $e->getMessage()));
            return $form;
        }
        $form->setInfoMessage($this->_("Commissions saved successfuly"));
        return $form;
    }
    
    /**
     * @service commission write
     * @param $fields
     */
    public function addSignup(Gpf_Rpc_Params $params) {
        $form = $this->getFormObject($params, false);
        try {
            $form->addSignupForm();
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_("Error while adding commission: %s", $e));
            return $form;
        }
        $form->setInfoMessage($this->_("Commissions added successfuly"));
        return $form;
    }
    
    /**
     * @service commission write
     * @param $fields
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = $this->getFormObject($params, false);

        try {
            $form->addForm();
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_("Error while adding commission: %s", $e->getMessage()));
            return $form;
        }
        $form->setInfoMessage($this->_("Commissions added successfuly"));
        return $form;
    }
    
    /**
     * @param Gpf_Rpc_Params $params
     * @param boolean $edit
     * @return Pap_Merchants_Campaign_CommissionTypeRpcForm
     */
    protected function getFormObject(Gpf_Rpc_Params $params, $edit = true) {
    	return new Pap_Merchants_Campaign_CommissionTypeRpcForm($params, $edit);
    }
}
?>
