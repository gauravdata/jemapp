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

class Pap_Features_SplitCommissions_Definition extends Gpf_Plugins_Definition  {

    const CODE = 'SplitCommissions';

    const NOTIFICATION_ON_SALE_SUMMARY = 'notification_on_sale_summary';
    const NOTIFICATION_ON_SALE_SUMMARY_STATUS = 'notification_on_sale_summary_status';

    public function __construct() {
        $this->codeName = self::CODE;
        $this->name = $this->_('Split Commissions');
        $this->description = $this->_('Split Commissions feature allows you to split commission between all affiliates from which customers get on your site and buy
            your product. You can also set bonus for first and last affiliate so new rewarding system can be compatible with old.'.
            '<br><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/split-commissions.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addRefuse('LifetimeCommissions');

        $this->addImplementation('Core.defineSettings', 'Pap_Features_SplitCommissions_Config', 'initSettingsEmailNotifications');

        $this->addImplementation('PostAffiliate.EmailNotificationsForm.save',
                                 'Pap_Features_SplitCommissions_Config', 'saveSettingsEmailNotifications');
        $this->addImplementation('PostAffiliate.EmailNotificationsForm.load',
                                 'Pap_Features_SplitCommissions_Config', 'loadSettingsEmailNotifications');
        
        $this->addImplementation('PostAffiliate.CommissionTypeForm.saveSettings',
                                 'Pap_Features_SplitCommissions_SplitCommissionsForm', 'saveSettings');
        $this->addImplementation('PostAffiliate.CommissionTypeForm.loadSettings',
                                 'Pap_Features_SplitCommissions_SplitCommissionsForm', 'loadSettings');

        $this->addImplementation('PostAffiliate.click.beforeSaveVisitorAffiliate',
                                 'Pap_Features_SplitCommissions_SaveVisitorAffiliate', 'saveVisitorAffiliate');

        $this->addImplementation('Tracker.action.afterSaveCommissions',
                                 'Pap_Features_SplitCommissions_SplitCommissions', 'clearData');
        $this->addImplementation('Tracker.action.saveCommissions',
                                 'Pap_Features_SplitCommissions_SplitCommissions', 'saveCommissions');
        $this->addImplementation('Tracker.saveCommissions.beforeSaveTransaction',
                                 'Pap_Features_SplitCommissions_SplitCommissions', 'applySplitCommission', 1);

        $this->addImplementation('PostAffiliate.Pap_Stats_Computer_Transactions.initSelectClause',
                                 'Pap_Features_SplitCommissions_SplitCommissions', 'initSelectClause');
        $this->addImplementation('PostAffiliate.Pap_Stats_Computer_Transactions.initGroupBy',
                                 'Pap_Features_SplitCommissions_SplitCommissions', 'initGroupBy');
        $this->addImplementation('PostAffiliate.Pap_Stats_Computer_Transactions.processResult',
                                 'Pap_Features_SplitCommissions_SplitCommissions', 'processResult');

        $this->addImplementation('Tracker.action.recognizeParametersStarted',
                                 'Pap_Features_SplitCommissions_RecognizeVisitorAffiliate', 'recognize');
        
        $this->addImplementation('Tracker.action.recognizeAfterFraudProtection',
                                 'Pap_Features_SplitCommissions_RecognizeVisitorAffiliate', 'addForcedAffiliateToVisitorAffiliates');

        $this->addImplementation('Pap_Stats_Computer_TransactionsStatsBuilder.buildGroupBy',
                                 'Pap_Features_SplitCommissions_SplitCommissions', 'transactionsStatsBuilderbuildGroupBy');
    }

    public function onActivate() {
        $campaignRow = new Pap_Db_Campaign();
        $campaigns = $campaignRow->loadCollection()->getIterator();

        foreach ($campaigns as $campaign) {
            $this->setSplitCommissionsBonus($campaign);
        }

        $this->insertMailTemplateToDb();
    }

    public function onDeactivate() {
        $this->deleteMailTemplateFromDb();
    }

    private function insertMailTemplateToDb() {
        $template = new Pap_Mail_SplitCommissionsMerchantOnSale();
        $template->setup(Gpf_Session::getAuthUser()->getAccountId());
    }

    private function deleteMailTemplateFromDb() {
        $dbTemplate = new Gpf_Db_MailTemplate();
        $dbTemplate->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
        $dbTemplate->setClassName('Pap_Mail_SplitCommissionsMerchantOnSale');
        try {
            $dbTemplate->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::warning('Mail template Pap_Mail_SplitCommissionsMerchantOnSale was not found during deactivation of split commissions... It should be there.');
            return;
        }
        $dbTemplate->delete();
    }

    private function setSplitCommissionsBonus(Pap_Db_Campaign $campaign) {
        $commissionType = new Pap_Db_CommissionType();
        $commissionType->setCampaignId($campaign->getId());
        $commissionType->setType(Pap_Common_Constants::TYPE_SALE);
        $commissionType->loadFromData(array(Pap_Db_Table_CommissionTypes::CAMPAIGNID, Pap_Db_Table_CommissionTypes::TYPE));

        $overwriteCookie = $campaign->getOverwriteCookie();
        if ($overwriteCookie != Gpf::YES && $overwriteCookie != Gpf::NO) {
            $overwriteCookie = $this->isGeneralOverwriteAllowed();
        }

        if ($overwriteCookie == Gpf::YES) {
            $firstClickBonus = 0;
            $lastClickBonus = 100;
        } else {
            $firstClickBonus = 100;
            $lastClickBonus = 0;
        }

        Pap_Db_Table_CommissionTypeAttributes::getInstance()->setCommissionTypeAttributeValue($commissionType->getId(), Pap_Features_SplitCommissions_SplitCommissionsForm::FIRST_AFF_BONUS, $firstClickBonus);
        Pap_Db_Table_CommissionTypeAttributes::getInstance()->setCommissionTypeAttributeValue($commissionType->getId(), Pap_Features_SplitCommissions_SplitCommissionsForm::LAST_AFF_BONUS, $lastClickBonus);
    }

    private function isGeneralOverwriteAllowed() {
        $overwriteCookie = Gpf_Settings::get(Pap_Settings::OVERWRITE_COOKIE);
        if($overwriteCookie == Gpf::YES) {
            return Gpf::YES;
        }
        return Gpf::NO;
    }
}
?>
