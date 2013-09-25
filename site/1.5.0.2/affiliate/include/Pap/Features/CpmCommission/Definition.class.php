<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_CpmCommission_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'CpmCommission';
        $this->name = $this->_('CPM commissions');
        $this->description = $this->_('CPM (cost per mille) commissions are a special type of commissions that are rewarded for 1000 impressions, which means for displaying your banners 1000 times.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/cpm-commissions.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('Tracker.impression.afterSave', 'Pap_Features_CpmCommission_Main', 'saveCommission', 5);
        $this->addImplementation('PostAffiliate.CampaignForm.insertDefaultCommissionTypes',  'Pap_Features_CpmCommission_Main', 'insertDefaultCommissionType');
    }

    /**
     * Method will be called, when plugin is activated. e.g. create some tables required by plugin.
     *
     * @throws Gpf_Exception when plugin can not be activated
     */
    public function onActivate() {
        $this->addCpmCommissionToCampaigns();
    }

    /**
     * Method will be called, when plugin is deactivated. e.g. drop some tables needed by plugin.
     *
     */
    public function onDeactivate() {
        $this->disableCpmCommissionInCampaigns();
    }

    private function disableCpmCommissionInCampaigns() {
        $campaign = new Pap_Db_Campaign();
        $campaigns = $campaign->loadCollection();
        foreach ($campaigns as $campaign) {
            $commissionType = new Pap_Db_CommissionType();
            $commissionType->setCampaignId($campaign->getId());
            $commissionType->setType(Pap_Common_Constants::TYPE_CPM);
            try {
                $commissionType->loadFromData();
                $commissionType->setStatus(Pap_Db_CommissionType::STATUS_DISABLED);
                $commissionType->save();
            } catch (Gpf_DbEngine_NoRowException $e) {
            }
        }
    }

    private function addCpmCommissionToCampaigns() {
        $campaign = new Pap_Db_Campaign();
        $campaigns = $campaign->loadCollection();
        foreach ($campaigns as $campaign) {
            $commissionType = new Pap_Db_CommissionType();
            $commissionType->setCampaignId($campaign->getId());
            $commissionType->setType(Pap_Common_Constants::TYPE_CPM);
            try {
                $commissionType->loadFromData();
            } catch (Gpf_DbEngine_NoRowException $e) {
                $commissionType->setStatus(Pap_Db_CommissionType::STATUS_DISABLED);
                $commissionType->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_NONE);
                $commissionType->setApproval(Pap_Db_CommissionType::APPROVAL_AUTOMATIC);
                $commissionType->setZeroOrdersCommission(Gpf::NO);
                $commissionType->setSaveZeroCommission(Gpf::NO);
                $commissionType->insert();
            }
        }
    }
}
?>
