<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsForm.class.php 19023 2008-07-08 12:50:59Z mfric $
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
class Gpf_Auth_UserForm extends Gpf_View_FormService {
    
    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_Db_AuthUser();
    }
    
    protected function getId(Gpf_Rpc_Form $form) {
       return Gpf_Session::getAuthUser()->getAuthUserId(); 
    }
        
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("User");
    }
    
    /**
     * special handling - if password is empty, don't save it
     * 
     * @service myprofile read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = parent::load($params);
        $form->setField('Id', $this->getId($form));
        return $form;
    }
    
    /**
     * special handling - if password is empty, don't save it
     * 
     * @service myprofile write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($this->getId($form));

        try {
            $dbRow->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            $form->setErrorMessage($this->getDbRowObjectName().$this->_(" does not exist"));
            return $form;
        }

        $oldPassword = $dbRow->getPassword();
        $oldUsername = $dbRow->getUsername();
        $form->fill($dbRow);
        $newPassword = $dbRow->getPassword();
        
        if($newPassword == '') {
        	$dbRow->setPassword($oldPassword);
        }
        
        if (Gpf_Application::isDemo()) {
            $dbRow->setPassword($oldPassword);
            $dbRow->setUsername($oldUsername);
        }

        if(!$this->checkBeforeSave($dbRow, $form, self::EDIT)) {
        	return $form;
      	}
        try {
            $dbRow->save();
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }
        Gpf_Plugins_Engine::extensionPoint('Gpf_Auth_UserForm.save', $dbRow);
        $form->load($dbRow);
        $form->setInfoMessage($this->_("%s saved", $this->getDbRowObjectName()));
        return $form;
    }    
}

?>
