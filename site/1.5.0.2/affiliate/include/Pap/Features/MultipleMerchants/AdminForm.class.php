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
class Pap_Features_MultipleMerchants_AdminForm extends Pap_Common_UserForm {
    
    /**
     * @return Pap_Merchants_User
     */
    protected function createDbRowObject() {
    	$this->user = new Pap_Merchants_User();
        return $this->user;
    }
    
    protected function getDefaultUserRole() {
        return Pap_Application::DEFAULT_ROLE_MERCHANT;
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Admin");
    }
    
    protected function checkBeforeSave(Gpf_DbEngine_RowBase $row, Gpf_Rpc_Form $form, $operationType = self::EDIT) {
      return $this->checkUsernameIsValidEmail($form, $operationType);          
    }   
    
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_RowBase $dbRow) {
    	$dbRow->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
    	$actualDate = Gpf_Common_DateUtils::getDateTime(time());
        $dbRow->setDateInserted($actualDate);
        $dbRow->setDateApproved($actualDate);
        $dbRow->setStatus(Gpf_Db_User::APPROVED);
    }
    
    protected function fillAdd(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
        parent::fillAdd($form, $dbRow);
        $dbRow->setData('6', $dbRow->getUserName());
    }
    
    /** 
     *
     * @service merchant add
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }
    
    /**
     *
     * @service merchant read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = parent::load($params);
        $form->setField('retypepassword', $form->getFieldValue('rpassword'));
        return $form;
    }

    /**
     *
     * @service merchant write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     * @service merchant delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to delete %s %s(s)', '%s', $this->getDbRowObjectName()));
        $action->setInfoMessage($this->_('%s %s(s) successfully deleted', '%s', $this->getDbRowObjectName()));
        
        foreach ($action->getIds() as $id) {
            try {
                $row = $this->createDbRowObject();
                $row->setPrimaryKeyValue($id);
                $row->load();
                if ($row->getId() == Gpf_Session::getAuthUser()->getPapUserId()) {
                    $action->setErrorMessage($this->_('Could not delete logged %s', 
                       $this->getDbRowObjectName()));
                	throw new Gpf_Exception('');
                }
                $row->delete();
                $action->addOk();
            } catch (Exception $e) {
                $action->addError();
            }
        }
        return $action;
    }
    
    /**
     * @service merchant write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */    
    public function setAsDefault(Gpf_Rpc_Params $params) {
    	$action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to set default merchant'));
        $action->setInfoMessage($this->_('Default merchant changed'));      
        foreach ($action->getIds() as $id) {
            try {
                Gpf_Settings::set(Pap_Settings::DEFAULT_MERCHANT_ID, $id);
                $action->addOk();
                return $action;
            } catch (Exception $e) {
            }
        }
        $action->addError();
        return $action;
    }
}

?>
