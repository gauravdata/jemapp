<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Merchants_User_UserInCommisonGroupsForm extends Gpf_View_FormService {
	
	const DEFAULT_STATUS = "P";
	const DEFAULT_NOTE = "New note";

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Db_UserInCommissionGroup();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Commision group");
    }
    
    /** 
     *
     * @service user_comm_group add
     * @param $fields
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);     

        $dbRow = $this->createDbRowObject();
        $this->setDefaultDbRowObjectValues($dbRow);

        $form->fill($dbRow);
        $dbRow->set(Pap_Db_Table_UserInCommissionGroup::STATUS, self::DEFAULT_STATUS);
        $dbRow->set(Pap_Db_Table_UserInCommissionGroup::NOTE, self::DEFAULT_NOTE);
        $dbRow->set(Pap_Db_Table_UserInCommissionGroup::DATE_ADDED, Gpf_Common_DateUtils::now());

        try {
            $dbRow->save();
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($dbRow);
        $form->setField("Id", $dbRow->getPrimaryKeyValue());
        $form->setInfoMessage($this->getDbRowObjectName().$this->_(" added"));
        return $form;
    }
    
    /**
     *
     * @service user_comm_group write
     * @param ids, status
     * @return Gpf_Rpc_Action
     */
    public function changeStatus(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Selected commission group(s) are changed"));
        $action->setErrorMessage($this->_("Failed to change status"));

        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->set->add(Pap_Db_Table_UserInCommissionGroup::STATUS, $action->getParam("status"));
        $update->from->add(Pap_Db_Table_UserInCommissionGroup::getName());

        foreach ($action->getIds() as $id) {
            $update->where->add(Pap_Db_Table_UserInCommissionGroup::ID, "=", $id, "OR");
        }

        try {
            $update->execute();
            $action->addOk();
        } catch(Gpf_DbEngine_NoRowException $e) {
            $action->addError();
        }

        return $action;
    }
    
    /**
     * @service user_comm_group read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }
    
    /**
     * @service user_comm_group write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     * @service user_comm_group write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }
    
     /**
     * @service user_comm_group delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
}

?>
