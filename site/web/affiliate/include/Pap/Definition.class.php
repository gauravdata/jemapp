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

class Pap_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'PapCore';
        $this->name = 'Pap Core Plugin';
        $this->description = 'Pap Core functionality.';
        $this->version = Gpf_Application::getInstance()->getVersion();
        $this->pluginType = self::PLUGIN_TYPE_SYSTEM;

        $this->initDefines();
    }

    protected function initDefines() {
        $this->addDefine('PostAffiliate.beforeAllActions', 'Pap_Contexts_Action');
        $this->addDefine('PostAffiliate.afterAllActions', 'Pap_Contexts_Action');
        $this->addDefine('PostAffiliate.signup.before', 'Pap_Contexts_Signup');
        $this->addDefine('PostAffiliate.signup.after', 'Pap_Contexts_Signup');
        $this->addDefine('PostAffiliate.signup.afterFail', 'Pap_Contexts_Signup');
        $this->addDefine('PostAffiliate.affiliate.firsttimeApproved','Pap_Affiliates_User');
        $this->addDefine('PostAffiliate.affiliate.userStatusChanged','Gpf_Plugins_ValueContext');
        $this->addDefine('PostAffiliate.merchant.menu', 'Gpf_Menu');
        $this->addDefine('PostAffiliate.iconSet', 'Pap_Common_IconSet');
        $this->addDefine('PostAffiliate.UsersTable.constraints', 'Pap_Db_Table_Users');

        $this->addDefine('PostAffiliate.CampaignsGrid.initDataColumns', 'Gpf_View_GridService');
        $this->addDefine('PostAffiliate.CampaignsGrid.initViewColumns', 'Gpf_View_GridService');
        $this->addDefine('PostAffiliate.CampaignsGrid.initDefaultView', 'Gpf_View_GridService');
        $this->addDefine('PostAffiliate.CampaignsGrid.afterExecute', 'Gpf_Data_Record');
        $this->addDefine('PostAffiliate.CampaignForm.insertDefaultCommissionTypes', 'Pap_Merchants_Campaign_CampaignForm');

        $this->addDefine('PostAffiliate.CommissionTypeForm.saveSettings', 'Pap_Merchants_Campaign_CommissionTypeRpcForm');
        $this->addDefine('PostAffiliate.CommissionTypeForm.loadSettings', 'Pap_Merchants_Campaign_CommissionTypeRpcForm');

        $this->addDefine('PostAffiliate.CommissionTypeForm.loadAllCommissions', 'Pap_Merchants_Campaign_CommissionTypeRpcForm');
        $this->addDefine('PostAffiliate.CommissionTypeForm.saveAllCommissions', 'Pap_Merchants_Campaign_CommissionTypeRpcForm');

        $this->addDefine('PostAffiliate.BannerFactory.getBannerObjectFromType', 'Pap_Common_Banner_BannerRequest');
        $this->addDefine('PostAffiliate.BannersGrid.afterExecute', 'Gpf_Data_RecordSet');
        $this->addDefine('PostAffiliate.BannersGrid.buildWhere', 'Gpf_SqlBuilder_SelectBuilder');
        $this->addDefine('PostAffiliate.Campaigns.getCampaignsForAffiliate', 'Gpf_Data_Record');
        $this->addDefine('PostAffiliate.RecognizeCommGroup.getCommissionGroup', 'Pap_Contexts_Tracking');
        $this->addDefine('PostAffiliate.BannerViewer.show', 'Pap_Tracking_BannerViewerRequest');
        $this->addDefine('PostAffiliate.BannerForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.Banner.replaceBannerConstants', 'Gpf_Plugins_ValueContext');
        $this->addDefine('PostAffiliate.RebrandPdfBanner_Variables.getAll', 'Gpf_Plugins_ValueContext');
        $this->addDefine('PostAffiliate.RebrandPdfBanner_Variables.getValue', 'Gpf_Plugins_ValueContext');

        $this->addDefine('PostAffiliate.BannerListbox.getBannerSelect', 'Gpf_SqlBuilder_SelectBuilder');
        $this->addDefine('PostAffiliate.Campaigns.getCampaignsSelect', 'Gpf_SqlBuilder_SelectBuilder');

        $this->addDefine('PostAffiliate.UserInCommissionGroup.addUser', 'Pap_Common_Campaign');
        $this->addDefine('PostAffiliate.UserInCommissionGroup.changeStatus', 'Pap_Features_PrivateCampaigns_MailContext');

        $this->addDefine('PostAffiliate.AffiliateGeneralSettingsForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.AffiliateGeneralSettingsForm.save', 'Gpf_Rpc_Form');
        
        $this->addDefine('PostAffiliate.AffiliateGeneralSettingsForm.loadGeneral', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.AffiliateGeneralSettingsForm.saveGeneral', 'Gpf_Rpc_Form');
        
        $this->addDefine('PostAffiliate.click.beforeSaveVisitorAffiliate', 'Pap_Common_VisitorAffiliateCacheCompoundContext');

        $this->addDefine('Tracker.saveCommissions.beforeSaveTransaction', 'Pap_Common_TransactionCompoundContext');
        $this->addDefine('Tracker.saveCommissions', 'Pap_Contexts_Tracking');
        $this->addDefine('Tracker.saveAllCommissions', 'Pap_Common_SaveCommissionCompoundContext');
        $this->addDefine('Tracker.saveCommissions.saveTransaction', 'Pap_Common_Transaction');

        $this->addDefine('Tracker.impression.afterSave', 'Pap_Contexts_Impression');

        // $this->addDefine('Tracker.click.check', 'Pap_Contexts_Click'); // removed in newTracking
        $this->addDefine('Tracker.click.recognizeParameters', 'Pap_Contexts_Click');
        $this->addDefine('Tracker.click.beforeSaveClick', 'Pap_Contexts_Click');
        $this->addDefine('Tracker.click.afterSaveClick', 'Pap_Contexts_Click');
        $this->addDefine('Tracker.click.beforeSaveCommissions', 'Pap_Contexts_Click');
        $this->addDefine('Tracker.click.afterSaveCommissions', 'Pap_Contexts_Click');
        $this->addDefine('Tracker.click.fillClickParams', 'Pap_Db_ClickImpression');

        $this->addDefine('Tracker.request.getCountryCode', 'Gpf_Data_Record');

        $this->addDefine('Tracker.action.recognizeParametersStarted', 'Pap_Common_VisitorAffiliateCacheCompoundContext');
        $this->addDefine('Tracker.action.recognizeParametersEnded', 'Pap_Common_VisitorAffiliateCacheCompoundContext');
        $this->addDefine('Tracker.action.recognizeAfterFraudProtection', 'Pap_Common_VisitorAffiliateCacheCompoundContext');
        $this->addDefine('Tracker.action.beforeSaveCommissions', 'Pap_Contexts_Action');
        $this->addDefine('Tracker.action.afterSaveCommissions', 'Pap_Contexts_Action');
        $this->addDefine('Tracker.action.computeTotalCost', 'Pap_Contexts_Action');
        $this->addDefine('Tracker.action.computeFixedCost', 'Pap_Contexts_Action');
        $this->addDefine('Tracker.action.computeCommission', 'Pap_Contexts_Action');

        $this->addDefine('Tracker.action.saveCommissions', 'Pap_Common_ActionProcessorCompoundContext');

        $this->addDefine('FraudProtectionClicksForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('FraudProtectionClicksForm.save', 'Gpf_Rpc_Form');
        $this->addDefine('FraudProtectionSalesForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('FraudProtectionSalesForm.save', 'Gpf_Rpc_Form');
        $this->addDefine('FraudProtectionSignupsForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('FraudProtectionSignupsForm.save', 'Gpf_Rpc_Form');

        $this->addDefine('FraudProtection.Action.check', 'Pap_Contexts_Action');
        $this->addDefine('FraudProtection.Click.check', 'Pap_Contexts_Click');
        $this->addDefine('FraudProtection.Signup.check', 'Pap_Signup_SignupFormContext');

        $this->addDefine('PostAffiliate.Transaction.afterSave', 'Pap_Common_Transaction');

        $this->addDefine('PostAffiliate.CurrencyForm.save', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.CurrencyForm.load', 'Gpf_Rpc_Form');

        $this->addDefine('PostAffiliate.AffiliateSignupForm.save', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.AffiliateSignupForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.AffiliateForm.fillAdd', 'Pap_Common_User');
        $this->addDefine('PostAffiliate.AffiliateForm.assignParent', 'Pap_Common_User');
        $this->addDefine('PostAffiliate.AffiliateForm.afterSave', 'Pap_Common_User');

        $this->addDefine('PostAffiliate.CampaignDetailsAdditionalForm.initFields', 'Pap_Merchants_Campaign_CampaignDetailsAdditionalForm');
        $this->addDefine('PostAffiliate.CampaignDetailsAdditionalForm.save', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.CampaignDetailsAdditionalForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.Transaction.beforeSave', 'Pap_Common_Transaction');

        $this->addDefine('PostAffiliate.PayoutOption.getValue', 'Gpf_Plugins_ValueContext');
        $this->addDefine('PostAffiliate.PayoutOption.setValue', 'Gpf_Plugins_ValueContext');

        $this->addDefine('PostAffiliate.User.onInsert', 'Pap_Db_User');
        $this->addDefine('PostAffiliate.User.onUpdate', 'Pap_Db_User');
        $this->addDefine('PostAffiliate.User.beforeSave', 'Pap_Common_User');
        $this->addDefine('PostAffiliate.User.afterSave', 'Pap_Common_User');
        $this->addDefine('PostAffiliate.User.afterDelete', 'Pap_Common_User');
        $this->addDefine('PostAffiliate.User.afterInsert', 'Pap_Common_User');        
                
        $this->addDefine('PostAffiliate.User.generatePrimaryKey', 'Pap_Db_User');
        
        $this->addDefine('PostAffiliate.CommissionType.beforeSaveCheck', 'Pap_Db_CommissionType');

        $this->addDefine('PostAffiliate.UserMail.initTemplateVariables', 'Gpf_Mail_Template');
        $this->addDefine('PostAffiliate.UserMail.setVariableValues', 'Pap_Mail_UserMail');

        $this->addDefine('PostAffiliate.EmailNotificationsForm.save', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.EmailNotificationsForm.load', 'Gpf_Rpc_Form');

        $this->addDefine('PostAffiliate.Countries.getDefaultCountry', 'Gpf_Plugins_ValueContext');

        $this->addDefine('PostAffiliate.CommissionTypeEditAdditionalForm.initFields', 'Pap_Merchants_Campaign_CommissionTypeEditAdditionalForm');
        $this->addDefine('PostAffiliate.CommissionTypeEditAdditionalForm.save', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.CommissionTypeEditAdditionalForm.load', 'Gpf_Rpc_Form');

        $this->addDefine('PostAffiliate.StatisticsBase.initDataTypes', 'Pap_Common_Reports_StatisticsBase');
        $this->addDefine('PostAffiliate.StatisticsBase.getDefaultDataType', 'Gpf_Plugins_ValueContext');
        $this->addDefine('PostAffiliate.Banner.replaceUrlConstants', 'Pap_Common_BannerReplaceVariablesContext');

        $this->addDefine('PostAffiliate.Affiliates.Campaigns.initViewColumns', 'Pap_Merchants_Campaign_CampaignsGrid');
        $this->addDefine('PostAffiliate.Affiliates.Campaigns.initDefaultView', 'Pap_Merchants_Campaign_CampaignsGrid');
        $this->addDefine('PostAffiliate.Affiliates.Campaigns.buildWhere', 'Pap_Affiliates_Promo_SelectBuilderCompoundFilter');
        $this->addDefine('PostAffiliate.AffiliatePrivileges.initDefault', 'Pap_Privileges_Affiliate');

        $this->addDefine('PostAffiliate.Affiliates.Transactions.initDefaultView', 'Pap_Affiliates_Reports_TransactionsGrid');

        $this->addDefine('PostAffiliate.AffiliateForm.setDefaultDbRowObjectValues', 'Pap_Affiliates_User');
        $this->addDefine('PostAffiliate.AffiliateForm.checkRefidIsValid', 'Gpf_Rpc_Form');

        $this->addDefine('PostAffiliate.Stats.initTransactionTypes', 'Pap_Stats_TransactionTypeStats');

        $this->addDefine('PostAffiliate.AdminsGrid.initViewColumns', 'Pap_Features_MultipleMerchants_AdminsGrid');
        $this->addDefine('PostAffiliate.AdminsGrid.initDefaultView', 'Pap_Features_MultipleMerchants_AdminsGrid');
        $this->addDefine('PostAffiliate.Application.registerRolePrivileges', 'Pap_Application');
        $this->addDefine('PostAffiliate.ApplicationSettings.loadSetting', 'Pap_ApplicationSettings');

        $this->addDefine('AffiliateNetwork.modifyWhere', 'Gpf_Common_SelectBuilderCompoundRecord');
        $this->addDefine('AffiliateNetwork.modifyFrom', 'Gpf_Common_SelectBuilderCompoundRecord');

        $this->addDefine('TransactionsGrid.initViewColumns', 'Pap_Merchants_Transaction_TransactionsGrid');
        $this->addDefine('TransactionsGrid.initDataColumns', 'Pap_Merchants_Transaction_TransactionsGrid');
        $this->addDefine('TransactionsGrid.initSearchAffiliateCondition', 'Pap_Merchants_Transaction_TransactionsGrid');

        $this->addDefine('CampaignGrid.modifyWhere', 'Pap_Affiliates_Promo_SelectBuilderCompoundFilter');
        
        $this->addDefine('PostAffiliate.BannersGrid.initViewColumns', 'Gpf_View_GridService');
        $this->addDefine('PostAffiliate.BannersGrid.initDataColumns', 'Gpf_View_GridService');
        $this->addDefine('PostAffiliate.BannersGrid.initDefaultView', 'Gpf_View_GridService');

        $this->addDefine('PostAffiliate.VisitorAffiliatesGrid.initViewColumns', 'Gpf_View_GridService');
        $this->addDefine('PostAffiliate.VisitorAffiliatesGrid.initDataColumns', 'Gpf_View_GridService');
        $this->addDefine('PostAffiliate.VisitorAffiliatesGrid.initDefaultView', 'Gpf_View_GridService');

        $this->addDefine('PostAffiliate.ClicksGrid.initRawClicksSelect', 'Gpf_SqlBuilder_SelectBuilder');
        $this->addDefine('PostAffiliate.ClicksGrid.initDataColumns', 'Pap_Merchants_Reports_ClicksGrid');

        $this->addDefine('PostAffiliate.Pap_Stats_Computer_Base.initSelectBuilder', 'Pap_Common_Reports_SelectBuilderCompoundParams');
        $this->addDefine('PostAffiliate.Pap_Stats_Computer_Transactions.initSelectClause', 'Gpf_SqlBuilder_SelectClause');
        $this->addDefine('PostAffiliate.Pap_Stats_Computer_Transactions.initGroupBy', 'Gpf_SqlBuilder_GroupByClause');
        $this->addDefine('PostAffiliate.Pap_Stats_Computer_Transactions.processResult', 'Gpf_SqlBuilder_SelectBuilder');

        $this->addDefine('Pap_Affiliate.assignTemplateVariables', 'Gpf_Templates_Template');
        $this->addDefine('PostAffiliate.AffiliatesGrid.createResultSelect', 'Gpf_Common_SelectBuilderCompoundRecord');
        $this->addDefine('PostAffiliate.MassMailAffiliatesGrid.addFilter', 'Gpf_Common_SelectBuilderCompoundRecord');

        $this->addDefine('PostAffiliatePro.Pap_Mail_Reports_Report.setAccountId', 'Gpf_Data_Record');

        $this->addDefine('Tracker.ImpressionProcessor.getAllImpressions', 'Gpf_SqlBuilder_SelectBuilder');
        $this->addDefine('PostAffiliate.Pap_Common_StatsGrid.initStatsColumns', 'Pap_Common_StatsColumnsContext');
        $this->addDefine('PostAffiliate.Pap_Common_StatsGrid.buildStatsFrom', 'Pap_Common_StatsGridParamsContext');
        $this->addDefine('PostAffiliate.Pap_Common_Reports_TransactionsGridBase.getCustomFilterFields', 'Gpf_View_CustomFilterFields');
        $this->addDefine('PostAffiliate.Pap_Common_Reports_TransactionsGridBase.addFilter', 'Gpf_Plugins_ValueContext');
        $this->addDefine('PostAffiliate.Pap_Common_StatsGrid.addAllActionsViewColumns', 'Pap_Common_StatsGrid');
                        
        $this->addDefine('Pap_Common_Campaign_CampaignForAffiliateRichListBox.getCampaignRecordSetForAffiliate', 'Pap_Affiliates_Promo_SelectBuilderCompoundFilter');
        $this->addDefine('Pap_Tracking_Action_ActionProcessor.processAccount', 'Pap_Contexts_Action');
        
        $this->addDefine('PostAffiliate.Pap_Merchants_Config_AffEmailNotificationsForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.Pap_Merchants_Config_AffEmailNotificationsForm.save', 'Gpf_Rpc_Form');
        
        $this->addDefine('PostAffiliate.Pap_Account.onStatusChange', 'Pap_Account');
        
        $this->addDefine('PostAffiliate.OfflineSaleForm.createSale', 'Pap_Tracking_ActionObject');
        $this->addDefine('PostAffiliate.OfflineSaleForm.load', 'Gpf_Rpc_Form');
        $this->addDefine('PostAffiliate.Pap_Db_Table_ClicksImpressions.getStatsSelect', 'Pap_Stats_StatsSelectContext');  
        
        $this->addDefine('PostAffiliate.PapTrackingVisitProcessor.processVisit', 'Pap_Db_Visit');
        $this->addDefine('PostAffiliate.Pap_Merchants_Payout_PayAffiliatesForm.payAffiliates', 'Pap_Common_User');
        
        $this->addDefine('Pap_Stats_Computer_TransactionsStatsBuilder.buildGroupBy', 'Pap_Stats_Computer_TransactionsStatsBuilderContext');

        //features extension points
        $this->addDefine('PostAffiliate.Features.PerformanceRewards.Action.createActionList', 'Pap_Features_PerformanceRewards_ActionList');
        
    }
}
?>
