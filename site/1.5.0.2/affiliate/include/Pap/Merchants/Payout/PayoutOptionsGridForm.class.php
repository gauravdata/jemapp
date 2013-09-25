<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: PayoutOptionsGridForm.class.php 24052 2009-04-09 07:33:35Z mjancovic $
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
class Pap_Merchants_Payout_PayoutOptionsGridForm extends Gpf_View_FormService {
    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Db_PayoutOption();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Payout option");
    }
       
    /**
     * @param Gpf_DbEngine_Row $dbRow
     */
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_Row $dbRow) {
        $dbRow->set(Gpf_Db_Table_Accounts::ID, Gpf_Session::getAuthUser()->getAccountId());
    }
    
    /**
     * Load default payout method
     *
     * @service payout_option read
     * @param Gpf_Rpc_Params $params 
     */
    public function loadDefaultPayoutMethod(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	
    	$payoutMethod = Gpf_Settings::get(Pap_Settings::DEFAULT_PAYOUT_METHOD);
    	
    	$form->setField("id", $payoutMethod);
    	
    	return $form;
    }
    
    /**
     * Save default payout method
     *
     * @service payout_option write
     * @param Gpf_Rpc_Params $params 
     */
    public function saveDefaultPayoutMethod(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->setInfoMessage($this->_('Default payout option saved'));
        
        Gpf_Settings::set(Pap_Settings::DEFAULT_PAYOUT_METHOD, $form->getFieldValue("id"));
        
        return $form;
    }
    
    /**
     * @service payout_option read
     * @return Gpf_Rpc_Action
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }
    
    /**
     * @service payout_option write
     * @return Gpf_Rpc_Action
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     * @service payout_option write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }
    
     /**
     * @service payout_option delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
    
    protected function checkBeforeSave(Gpf_DbEngine_RowBase $row, Gpf_Rpc_Form $form, $operationType = self::EDIT) {
        $template = new Gpf_Templates_Template($row->getExportRowTemplate(), '', Gpf_Templates_Template::FETCH_TEXT);
        if (!$template->isValid()) {
        	 $form->setErrorMessage($this->_('Invalid Smarty syntax. More information: ') . 
        	 Gpf_Application::getInstance()->getHelpUrl() . Pap_Common_Constants::SMARTY_SYNTAX_URL);
        	 return false;
        }
        return true;
    }   
}

?>
