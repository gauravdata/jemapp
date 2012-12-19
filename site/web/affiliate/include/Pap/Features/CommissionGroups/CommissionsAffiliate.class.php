<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CustomersGridForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Features_CommissionGroups_CommissionsAffiliate extends Gpf_Object {
    
    /**
     * @service commission_group write
     *
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to save %s field(s) in %s(s)', '%s', $this->_('commission group')));
        $action->setInfoMessage($this->_('%s field(s) in %s(s) successfully saved', '%s', $this->_('commission group')));

        $userId = $action->getParam('id');
        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($action->getParam("fields"));

        foreach ($fields as $field) {
        	$this->saveCommissionGroup($field->get('id'), $userId, $field->get("value"));
            $action->addOk();
        }

        return $action;
    }
    
    private function saveCommissionGroup($campaignId, $userId, $newCommissionGroupId) {
    	$cgTable = Pap_Db_Table_CommissionGroups::getInstance();
    	$oldCommissionGroupId = $cgTable->getUserCommissionGroup($campaignId, $userId);
    	$status = Pap_Common_Constants::STATUS_APPROVED;
    	
        if ($oldCommissionGroupId != null) {
            try {
                $status = Pap_Db_Table_UserInCommissionGroup::getStatus($campaignId, $userId);
            } catch (Gpf_DbEngine_NoRowException $e) {
                Gpf_Log::debug('Row in userincommissiongroup should exist for userId: '.$userId.' and campaignId: '.$campaignId);
            }
        	$this->deleteUserInCommissionGroups($oldCommissionGroupId, $userId);
        }
        
        $commissionGroup = new Pap_Db_CommissionGroup();
        $commissionGroup->setPrimaryKeyValue($newCommissionGroupId);
        $commissionGroup->load();
        
        if ($commissionGroup->getIsDefault() != Gpf::YES) {
        	$this->addUserInCommissionGroup($newCommissionGroupId, $userId, $status);
        }
    }
    
    private function deleteUserInCommissionGroups($commissionGroupId, $userId) {
    	$delete = new Gpf_SqlBuilder_DeleteBuilder();
    	$delete->from->add(Pap_Db_Table_UserInCommissionGroup::getName());
    	$delete->where->add(Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID, '=', $commissionGroupId);
    	$delete->where->add(Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $userId);
    	$delete->execute();
    }
    
    private function addUserInCommissionGroup($commissionGroupId, $userId, $status) {
        $userInCommissionGroup = new Pap_Db_UserInCommissionGroup();
        $userInCommissionGroup->setCommissionGroupId($commissionGroupId);
        $userInCommissionGroup->setUserId($userId);
        $userInCommissionGroup->setStatus($status);
        $userInCommissionGroup->setDateAdded(Gpf_Common_DateUtils::getDateTime(time()));      
        $userInCommissionGroup->save();	
    }
}

?>
