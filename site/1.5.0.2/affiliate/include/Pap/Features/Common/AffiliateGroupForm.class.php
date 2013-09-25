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
class Pap_Features_Common_AffiliateGroupForm extends Gpf_View_FormService {
    
    /**
     * @return Pap_Db_UserInCommissionGroup
     */
    protected function createDbRowObject() {
        return new Pap_Db_UserInCommissionGroup();
    }
    
    /**
     * @return String
     */
    protected function getDbRowObjectName() {
        return $this->_("Affiliate");
    }
    
    /** 
     *
     * @service user_in_commission_group add
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $this->setDefaultDbRowObjectValues($dbRow);

        $form->fill($dbRow);
        $dbRow->setDateAdded(Gpf_Common_DateUtils::getDateTime(time()));
        
        $dbRow->removeUserFromCampaignGroups($form->getFieldValue('campaignid'));
        
        try {
            $dbRow->save();
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($dbRow);
        $form->setField("Id", $dbRow->getPrimaryKeyValue());
        $form->setInfoMessage($this->_("%s was successfuly added", $this->getDbRowObjectName()));
        
        return $form;
    }
    
    /**
     *
     * @service user_in_commission_group write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     *
     * @service user_in_commission_group read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @service user_in_commission_group delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {        
        return parent::deleteRows($params);
    }
    
    /**
     * @service user_in_commission_group write
     *
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }
    
    /**
     *
     * @service user_in_commission_group write
     * @param ids, status
     * @return Gpf_Rpc_Action
     */
    public function changeStatus(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Selected affiliate(s) status is changed"));
        $action->setErrorMessage($this->_("Failed to change status for selected affiliate(s)"));

        foreach ($action->getIds() as $id){
            try {
                $userInCommGroup = $this->createDbRowObject();
                $userInCommGroup->setPrimaryKeyValue($id);
                $userInCommGroup->load();
                if ($userInCommGroup->getStatus() == $action->getParam("status")) {
                    continue;
                }
                $userInCommGroup->setStatus($action->getParam("status"));
                $userInCommGroup->save();
                $this->sendMail($userInCommGroup);
                $action->addOk();
            } catch(Gpf_DbEngine_NoRowException $e) {
                $action->addError();
            }
        }
        return $action;
    } 
    
    /**
     * @throws Gpf_DbEngine_NoRowException
     * @param $userInCommGroup
     */
    private function sendMail(Pap_Db_UserInCommissionGroup $userInCommGroup) {
    	$mailContext = new Pap_Features_PrivateCampaigns_MailContext();
        $mailContext->setCampaign(Pap_Common_Campaign::getCampaignById(Pap_Db_CommissionGroup::getCommissionGroupById($userInCommGroup->getCommissionGroupId())->getCampaignId()));
        $mailContext->setUser(Pap_Common_User::getUserById($userInCommGroup->getUserId()));
        $mailContext->setUserInCommissionGroup($userInCommGroup);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.UserInCommissionGroup.changeStatus', $mailContext);
    }
}

?>
