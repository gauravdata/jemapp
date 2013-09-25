<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_ActionCommission_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'ActionCommission';
        $this->name = $this->_('Action commissions');
        $this->description = $this->_('Action Commissions feature allows you to define per action, or per "anything" commissions.<br/>This way you can define commissions for example per user registration, per download, per visiting specific page, etc.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/action-commissions.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.Stats.initTransactionTypes', 'Pap_Features_ActionCommission_Main', 'initTransactionTypes');
        $this->addImplementation('PostAffiliate.StatisticsBase.initDataTypes', 'Pap_Features_ActionCommission_Main', 'initDataTypes');
        $this->addImplementation('PostAffiliate.Pap_Common_StatsGrid.initStatsColumns', 'Pap_Features_ActionCommission_Main', 'initStatsColumns');
        $this->addImplementation('PostAffiliate.Pap_Common_StatsGrid.buildStatsFrom', 'Pap_Features_ActionCommission_Main', 'buildStatsFrom');
        $this->addImplementation('PostAffiliate.Pap_Common_StatsGrid.addAllActionsViewColumns', 'Pap_Features_ActionCommission_Main', 'addAllActionsViewColumns');
        $this->addImplementation('PostAffiliate.CommissionTypeForm.saveSettings', 'Pap_Features_ActionCommission_CommissionsForm', 'save');
        $this->addImplementation('PostAffiliate.Pap_Common_Reports_TransactionsGridBase.getCustomFilterFields', 'Pap_Features_ActionCommission_Main', 'getCustomFilterFields');
        $this->addImplementation('PostAffiliate.Pap_Common_Reports_TransactionsGridBase.addFilter', 'Pap_Features_ActionCommission_Main', 'addFilter');
    }

    /**
     * Method will be called, when plugin is deactivated. e.g. drop some tables needed by plugin.
     *
     */
    public function onDeactivate() {
        $this->disableActionCommissionInCampaigns();
    }

    private function disableActionCommissionInCampaigns() {
        $campaign = new Pap_Db_Campaign();
        $campaigns = $campaign->loadCollection();
        foreach ($campaigns as $campaign) {
            $commissionType = new Pap_Db_CommissionType();
            $commissionType->setCampaignId($campaign->getId());
            $commissionType->setType(Pap_Common_Constants::TYPE_ACTION);
            $commTypeCollection = $commissionType->loadCollection();
            foreach ($commTypeCollection as $commTypeRow) {
                $commTypeRow->setStatus(Pap_Db_CommissionType::STATUS_DISABLED);
                $commTypeRow->save();
            }
        }
    }
}
?>
