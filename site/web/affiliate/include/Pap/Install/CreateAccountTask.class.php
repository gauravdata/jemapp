<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package PostAffiliatePro
 *   @since Version 4.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Install_CreateAccountTask extends Gpf_Install_CreateAccountTask {

    protected function createAccountDirectory() {
        parent::createAccountDirectory();
        $accountDirectory = $this->getAccountDirectory();

        $banners = new Gpf_Io_File($accountDirectory->getFileName() . Pap_Merchants_Banner_BannerUpload::BANNERS_DIR);
        $this->createDirectory($banners);

        $sites = new Gpf_Io_File($accountDirectory->getFileName() . Pap_Features_SiteReplication_Replicator::SITES_DIR);
        $this->createDirectory($sites);
    }

    protected function initializeAccount() {
        parent::initializeAccount();

        $this->setupDefaultRecurrencePresets();
        $this->setupDefaultCurrency();
        $this->setupDefaultFormFields();
        $this->createDefaultMailAccount();
        $this->createMailTemplates();
        $this->setupDefaultBannerWrappers();
        $this->createCampaign();
        $this->createAffiliateScreens();
        $this->createDefaultPayoutOptions();
        $this->setupDefaultTrackingParameters();

        $this->createMasterMerchantUser();
        $this->createTestAffiliateUser();

        $this->createImportExportServices();
        $this->setupCountries();
        $this->createReferralCommissionType();

        $this->saveDefaultParamNameSettings();

        $this->setupDefaultTasks();
        
        $brand = new Pap_Install_Brand($this->account);
        $brand->install();
    }

    private function setupDefaultTasks() {
        $impressionProcessor = new Pap_Tracking_Impression_ImpressionProcessor();
        $impressionProcessor->insertTask();

        $visitProcessor = new Pap_Tracking_Visit_Processor();
        $visitProcessor->insertTask();

        $loggingForm = new Pap_Merchants_Config_LoggingForm();
        $loggingForm->insertDeleteSettingsTask();
    }

    public function saveDefaultParamNameSettings() {
        $this->saveDefaultSetting(Pap_Settings::IMPRESSIONS_TABLE_INPUT);
        $this->saveDefaultSetting(Pap_Settings::IMPRESSIONS_TABLE_PROCESS);
        $this->saveDefaultSetting(Pap_Settings::VISITS_TABLE_INPUT);
        $this->saveDefaultSetting(Pap_Settings::VISITS_TABLE_PROCESS);

        $this->saveDefaultSetting(Pap_Settings::PARAM_NAME_USER_ID);
    	$this->saveDefaultSetting(Pap_Settings::PARAM_NAME_BANNER_ID);
    	$this->saveDefaultSetting(Pap_Settings::PARAM_NAME_CAMPAIGN_ID);
    	$this->saveDefaultSetting(Pap_Settings::PARAM_NAME_ROTATOR_ID);
    	$this->saveDefaultSetting(Pap_Settings::PARAM_NAME_DESTINATION_URL);
    	$this->saveDefaultSetting(Pap_Settings::PARAM_NAME_EXTRA_DATA.'1');
    	$this->saveDefaultSetting(Pap_Settings::PARAM_NAME_EXTRA_DATA.'2');
    	$this->saveDefaultSetting(Pap_Settings::P3P_POLICY_COMPACT);
    	$this->saveDefaultSetting(Pap_Settings::URL_TO_P3P);
    }

    private function saveDefaultSetting($name) {
    	Gpf_Settings::set($name, Gpf_Settings::get($name), true);
    }

    public function setupDefaultBannerWrappers(){
        $row = new Pap_Db_BannerWrapper();
        $row->setId('plain');
        $row->setName(Gpf_Lang::_runtime('Plain'));
        $row->setCode('{$'. Pap_Merchants_Config_BannerWrapperService::CONST_HTML.'}');
        $row->save();

        $row = new Pap_Db_BannerWrapper();
        $row->setId('iframe');
        $row->setName(Gpf_Lang::_runtime('Iframe'));
        $row->setCode('<script type="text/javascript">'.
                      'document.write("<iframe name=\'banner\' src=\'{$'.Pap_Merchants_Config_BannerWrapperService::CONST_HTMLCOMPL.'}\''.
                      ' framespacing=\'0\' frameborder=\'no\' scrolling=\'no\' width=\'{$'.Pap_Merchants_Config_BannerWrapperService::CONST_WIDTH.'}\''.
                      ' height=\'{$'.Pap_Merchants_Config_BannerWrapperService::CONST_HEIGHT.'}\' allowtransparency=\'true\'>'.
                      '<a href=\'{$'.Pap_Merchants_Config_BannerWrapperService::CONST_CLICKURL.'}\' target=\'_top\'>{$'.Pap_Merchants_Config_BannerWrapperService::CONST_NAME.'}</a></iframe>");'."\n".
                      '</script>'."\n".
                      '<noscript>'."\n".
                      '<h2><a href="{$'.Pap_Merchants_Config_BannerWrapperService::CONST_TARGETURL.'}">{$'.Pap_Merchants_Config_BannerWrapperService::CONST_NAME.'}</a></h2>'."\n".
                      '</noscript>');
        $row->save();

        $row = new Pap_Db_BannerWrapper();
        $row->setId('popunder');
        $row->setName(Gpf_Lang::_runtime('PopUnder'));
        $row->setCode('<script type="text/javascript">
if (typeof pap_o == "undefined") {var pap_o  = document.onmouseup;if (typeof pap_o == "undefined")pap_o = function(){return true;};function papSetC($Name,$Value,$EndH){var exdate=new Date();$EndH=exdate.getHours()+$EndH;exdate.setHours($EndH);document.cookie=$Name+ "=" +escape($Value)+(($EndH==null) ? "" : ";expires="+exdate.toGMTString()+";path=/;");}function papGetC($Name){if (document.cookie.length>0){$Start=document.cookie.indexOf($Name + "=");if($Start!=-1){$Start=$Start + $Name.length+1;$End=document.cookie.indexOf(";",$Start);if ($End==-1)$End=document.cookie.length;return unescape(document.cookie.substring($Start,$End));}}return "";}}if (navigator.cookieEnabled && !papGetC("pap{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}")){papSetC("pap{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}", 1, 12);var pap_o_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '} = document.onmouseup;if (typeof pap_o_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}  =="undefined")pap_o_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}  = function(){return true;};document.onmouseup=function(){$puw_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}=window.open("{$htmlcompleteurl}","_blank","height={$height}, width={$width}, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no");if($puw_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '})$puw_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}.blur();pap_o_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}();document.onmouseup="pap_o();";}}
</script>');
        $row->save();

        $row = new Pap_Db_BannerWrapper();
        $row->setId('popup');
        $row->setName(Gpf_Lang::_runtime('PopUp'));
        $row->setCode('<script type="text/javascript">
if (typeof pap_o == "undefined") {var pap_o  = document.onmouseup;if (typeof pap_o == "undefined") pap_o = function(){return true;};function papSetC($Name,$Value,$EndH){var exdate=new Date();$EndH=exdate.getHours()+$EndH;exdate.setHours($EndH);document.cookie=$Name+ "=" +escape($Value)+(($EndH==null) ? "" : ";expires="+exdate.toGMTString()+";path=/;");}function papGetC($Name) {if (document.cookie.length>0){$Start=document.cookie.indexOf($Name + "=");if ($Start!=-1) {$Start=$Start + $Name.length+1;$End=document.cookie.indexOf(";",$Start);if ($End==-1) $End=document.cookie.length;return unescape(document.cookie.substring($Start,$End));}}return "";}}if (navigator.cookieEnabled && !papGetC("pap{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}")) {papSetC("pap{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}", 1, 12);var pap_o_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '} = document.onmouseup;if (typeof pap_o_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}  =="undefined") pap_o_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}  = function(){return true;};document.onmouseup=function(){$puw_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}=window.open("{$htmlcompleteurl}","_blank","height={$height}, width={$width}, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no");pap_o_{$' . 
        Pap_Merchants_Config_BannerWrapperService::CONST_BANNERID . '}();document.onmouseup="pap_o();";};}
</script>');
        $row->save();

    }

    public function setupDefaultRecurrencePresets() {
        $this->addRecurrencePreset("varied", Gpf_Lang::_runtime("Varied"));

        $this->addRecurrencePreset("each15m", Gpf_Lang::_runtime("Each 15 minutes"),
        Gpf_Db_RecurrenceSetting::TYPE_EACH, 900, 1);

        $this->addRecurrencePreset(Pap_Db_CommissionType::RECURRENCE_DAILY, Gpf_Lang::_runtime("Daily"),
        Gpf_Db_RecurrenceSetting::TYPE_DAY, Gpf_Db_RecurrenceSetting::NO_PERIOD, 1);

        $this->addRecurrencePreset(Pap_Db_CommissionType::RECURRENCE_WEEKLY, Gpf_Lang::_runtime("Weekly"),
        Gpf_Db_RecurrenceSetting::TYPE_WEEK, Gpf_Db_RecurrenceSetting::NO_PERIOD, 1);

        $this->addRecurrencePreset(Pap_Db_CommissionType::RECURRENCE_MONTHLY, Gpf_Lang::_runtime("Monthly"),
        Gpf_Db_RecurrenceSetting::TYPE_MONTH, Gpf_Db_RecurrenceSetting::NO_PERIOD, 1);

        $this->addRecurrencePreset(Pap_Db_CommissionType::RECURRENCE_QUARTERLY, Gpf_Lang::_runtime("Quarterly"),
        Gpf_Db_RecurrenceSetting::TYPE_MONTH, Gpf_Db_RecurrenceSetting::NO_PERIOD, 3);

        $this->addRecurrencePreset(Pap_Db_CommissionType::RECURRENCE_SEMIANNUALLY, Gpf_Lang::_runtime("Semiannualy"),
        Gpf_Db_RecurrenceSetting::TYPE_MONTH, Gpf_Db_RecurrenceSetting::NO_PERIOD, 6);

        $this->addRecurrencePreset(Pap_Db_CommissionType::RECURRENCE_YEARLY, Gpf_Lang::_runtime("Yearly"),
        Gpf_Db_RecurrenceSetting::TYPE_YEAR, Gpf_Db_RecurrenceSetting::NO_PERIOD, 1);
    }

    private function createImportExportServices() {
        Gpf_Csv_ImportExportService::register(
        new Pap_Merchants_User_AffiliatesImportExport(), $this->account->getId());
        Gpf_Csv_ImportExportService::register(
        new Pap_Merchants_Banner_BannersImportExport(), $this->account->getId());
        Gpf_Csv_ImportExportService::register(
        new Pap_Merchants_Campaign_CommissionsImportExport(), $this->account->getId());
        Gpf_Csv_ImportExportService::register(
        new Gpf_SettingsImportExport(), $this->account->getId());
        Gpf_Csv_ImportExportService::register(
        new Pap_Merchants_Campaign_CampaignsImportExport(), $this->account->getId());
    }

    private function createMasterMerchantUser() {
        $filters = new Pap_Merchants_Filters();
        $filters->addDefaultFilters();

        $merchantUser = new Pap_Merchants_User();
        $merchantUser->setId(Gpf_Settings::get(Pap_Settings::DEFAULT_MERCHANT_ID));
        $merchantUser->setRefId("merchant");
        $merchantUser->setPassword($this->account->getPassword());
        $merchantUser->setUserName($this->account->getEmail());
        $merchantUser->setFirstName($this->account->getFirstname());
        $merchantUser->setLastName($this->account->getLastname());
        $merchantUser->setAccountId($this->account->getId());
        $merchantUser->setStatus(Gpf_Db_User::APPROVED);
        $merchantUser->setNote(Gpf_Lang::_runtime("Hello and welcome to our affiliate program.<br/>I'm your affiliate manager, and I'm here for you if you have ANY questions or problems related to our affiliate program.<br/><br/>I wish you all success in promoting our products, and profitable partnership for both you and us."));
        $merchantUser->setPhoto($this->copyToFileUploads('affiliate-manager.gif'));
        $merchantUser->setData('1', '12345678');
        $merchantUser->setData('6', $this->account->getEmail());
        $merchantUser->setDateInserted(Gpf_Common_DateUtils::getDateTime(time()));
        $merchantUser->save();

        $this->setMerchantNotificationMail($this->account->getEmail());

        $this->setQuickLaunchSettings($merchantUser->getAccountUserId(),
            'showDesktop,Campaigns-Manager,Banner-Manager,Affiliate-Manager,Transaction-Manager,Pay-Affiliates,Reports,Quick-Report,Trends-Report,Clicks-List,Configuration-Manager,Logs-History');

        // set wallpaper to tiled
        $attribute = new Gpf_Db_UserAttribute();
        $attribute->setName('wallpaperPosition');
        $attribute->set(Gpf_Db_Table_UserAttributes::VALUE, 'T');
        $attribute->setAccountUserId($merchantUser->getAccountUserId());
        $attribute->save();
        Gpf_Settings::set(Pap_Settings::DEFAULT_MERCHANT_PANEL_THEME, Pap_Branding::DEFAULT_MERCHANT_PANEL_THEME);
    }

    private function createTestAffiliateUser() {
        $affiliateUser = new Pap_Affiliates_User();
        $affiliateUser->setId('11111111');
        $affiliateUser->setDateInserted(Gpf_Common_DateUtils::now());
        $affiliateUser->setRefId("testaff");
        $affiliateUser->setPassword($this->account->getPassword());
        $affiliateUser->setUserName(Pap_Branding::DEMO_AFFILIATE_USERNAME);
        $affiliateUser->setFirstName("Test");
        $affiliateUser->setLastName("Affiliate");
        $affiliateUser->setAccountId($this->account->getId());
        $affiliateUser->setStatus(Gpf_Db_User::APPROVED);
        $affiliateUser->set('dateapproved', Gpf_Common_DateUtils::Now());
        $affiliateUser->save();

        $this->setQuickLaunchSettings($affiliateUser->getAccountUserId(), 'showDesktop');
        Gpf_Settings::set(Pap_Settings::DEFAULT_AFFILIATE_PANEL_THEME, Pap_Branding::DEFAULT_AFFILIATE_PANEL_THEME);
    }

    private function setQuickLaunchSettings($accountUserId, $value) {
        $attribute = new Gpf_Db_UserAttribute();
        $attribute->setName('quickLaunchSetting');
        $attribute->set(Gpf_Db_Table_UserAttributes::VALUE, $value);
        $attribute->setAccountUserId($accountUserId);
        $attribute->save();
    }

    private function setMerchantNotificationMail($mail) {
        Gpf_Settings::set(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL, $mail);
    }

    private function createCampaign() {
        $campaign = Pap_Db_Table_Campaigns::createDefaultCampaign($this->account->getId(), 'First campaign', '11111111');

        $this->createSampleBanners($campaign->getId());
        $this->createSampleChannel();
        $this->createSampleDirectLinkUrl();
    }

    private function createCommission($commissionGroupId, $commissionTypeId, $tier, $type, $value) {
        $c = new Pap_Db_Commission();

        $c->set("tier", $tier);
        $c->set("subtype", 'N');
        $c->set("commissiontype", $type);
        $c->set("commissionvalue", $value);
        $c->set("commtypeid", $commissionTypeId);
        $c->set("commissiongroupid", $commissionGroupId);

        $c->insert();

        return $c->get("commissionid");
    }

    /**
     * @return Pap_Common_Campaign
     */
    private function createFirstCampaign() {
        $campaign = new Pap_Common_Campaign();
        $campaign->setId('11111111');
        $campaign->setName('First campaign');
        $campaign->setCampaignStatus(Pap_Common_Campaign::CAMPAIGN_STATUS_ACTIVE);
        $campaign->setCampaignType(Pap_Common_Campaign::CAMPAIGN_TYPE_PUBLIC);
        $campaign->setCookieLifetime(0);
        $campaign->resetOverwriteCookieToDefault();
        $campaign->setAccountId($this->account->getId());
        $campaign->save();

        return $campaign;
    }

    protected function createMailTemplates() {
        parent::createMailTemplates();

        $this->setupTemplate(new Pap_Mail_NewUserSignupBeforeApproval());
        $this->setupTemplate(new Pap_Mail_NewUserSignupApproved());
        $this->setupTemplate(new Pap_Mail_NewUserSignupDeclined());
        $this->setupTemplate(new Pap_Mail_MerchantNewUserSignup());
        $this->setupTemplate(new Pap_Mail_MerchantOnSale());
        $this->setupTemplate(new Pap_Mail_MerchantOnContactUs());
        $this->setupTemplate(new Pap_Mail_OnPayout());
        $this->setupTemplate(new Pap_Mail_OnVATPayout());
        $this->setupTemplate(new Pap_Mail_AffiliateChangeCommissionStatus());
        $this->setupTemplate(new Pap_Mail_AffiliateOnNewSale());
        $this->setupTemplate(new Pap_Mail_OnSubAffiliateSignup());
        $this->setupTemplate(new Pap_Mail_OnSubAffiliateSale());
        $this->setupTemplate(new Pap_Mail_OnAffiliateJoinToCampaign());
        $this->setupTemplate(new Pap_Mail_OnMerchantApproveAffiliateToCampaign());
        $this->setupTemplate(new Pap_Mail_OnMerchantDeclineAffiliateForCampaign());
        $this->setupTemplate(new Pap_Mail_MerchantOnCommissionApproved());
        $this->setupTemplate(new Pap_Mail_AffiliateDirectLinkNotification());
        $this->setupTemplate(new Pap_Mail_MerchantNewDirectLinkNotification());
        $this->setupTemplate(new Pap_Mail_InviteToCampaign());
        $this->setupTemplate(new Pap_Mail_Reports_DailyReport());
        $this->setupTemplate(new Pap_Mail_Reports_WeeklyReport());
        $this->setupTemplate(new Pap_Mail_Reports_MonthlyReport());
        $this->setupTemplate(new Pap_Mail_Reports_AffDailyReport());
        $this->setupTemplate(new Pap_Mail_Reports_AffWeeklyReport());
        $this->setupTemplate(new Pap_Mail_Reports_AffMonthlyReport());
        $this->setupTemplate(new Pap_Mail_MerchantInvoice());
        $this->setupTemplate(new Pap_Mail_AffiliateInvoice());
        $this->setupTemplate(new Pap_Mail_PayDayReminder_PayDayReminder());
    }

    private function setupDefaultCurrency() {
        $currency = new Gpf_Db_Currency();
        Pap_Branding::initDefaultCurrency($currency);
        $currency->setExchangeRate(1);
        $currency->setIsDefault(Gpf_Db_Currency::DEFAULT_CURRENCY_VALUE);
        $currency->setAccountId($this->account->getId());
        $currency->save();
    }

    private function setupDefaultFormFields() {
        $affiliateForm = new Pap_Merchants_Config_AffiliateFormDefinition($this->account->getId());
        $affiliateForm->check();

        $merchantForm = new Pap_Merchants_Config_MerchantFormDefinition($this->account->getId());
        $merchantForm->check();
    }

    private function createAffiliateScreens() {
        $this->addAffiliateScreen('5d569506', 'Home', Gpf_Lang::_runtime('Home'), null, Pap_Db_AffiliateScreen::HEADER_HIDE);
        $this->addAffiliateScreen('bc353dac', 'Custom-Page', Gpf_Lang::_runtime('Getting started'), '{"template":"custom/getting_started"}');

        $this->addAffiliateScreen('c65d7c8e', 'Promotion', Gpf_Lang::_runtime('Promotion'), null);
        $this->addAffiliateScreen('ff0de1d4', 'Campaigns-List-Wide', Gpf_Lang::_runtime('Campaigns'), null);
        $this->addAffiliateScreen('581b530f', 'Promo-Materials', Gpf_Lang::_runtime('Banners & Links'), null);
        $this->addAffiliateScreen('52d04ac0', 'Channels', Gpf_Lang::_runtime('Ad Channels'), null);
        $this->addAffiliateScreen('df559b42', 'Advanced-Functionality', Gpf_Lang::_runtime('Advanced tools'), null);

        $this->addAffiliateScreen('27366e9a', 'Reports', Gpf_Lang::_runtime('Reports'), null);

        $this->addAffiliateScreen('84943ff4', 'Payouts', Gpf_Lang::_runtime('Payouts to me'), null);
        $this->addAffiliateScreen('373881fc', 'Raw-Clicks', Gpf_Lang::_runtime('Raw Clicks'), null);
        $this->addAffiliateScreen('abd5a555', 'Quick-Stats', Gpf_Lang::_runtime('Quick Stats'), null);
        $this->addAffiliateScreen('ad67466d', 'Transactions-List', Gpf_Lang::_runtime('Commissions'), null);
        $this->addAffiliateScreen('7c6a26a1', 'Trends-Report', Gpf_Lang::_runtime('Trends report'), null);
        $this->addAffiliateScreen('ea2534c9', 'Subaffiliate-Sale-Stats', Gpf_Lang::_runtime('Subaffiliate Sale Stats'), null);
        $this->addAffiliateScreen('8d02c36a', 'Subaffiliates-Tree', Gpf_Lang::_runtime('Tree of subaffiliates'), null);
        $this->addAffiliateScreen('e7dc308e', 'Channel-Stats-Report', Gpf_Lang::_runtime('Channel stats report'), null);
        $this->addAffiliateScreen('b4r85r9w', 'Top-Referrer-URLs', Gpf_Lang::_runtime('Top referrer URL'), null);

        $this->addAffiliateScreen('9a136deb', 'My-Profile', Gpf_Lang::_runtime('My profile'), null);

        $this->addAffiliateScreen('9782bb5e', 'Contact-Us', Gpf_Lang::_runtime('ContactUs'), null);

        $this->addAffiliateScreen('f7fcdf21', 'Custom-Page', Gpf_Lang::_runtime('Promotion tips'), '{"template":"custom/promotion_tips"}');
        $this->addAffiliateScreen('799930ce', 'Custom-Page', Gpf_Lang::_runtime('Frequently asked questions'), '{"template":"custom/frequently_asked_questions"}');
        $this->addAffiliateScreen('48d2fe53', 'Custom-Page', Gpf_Lang::_runtime('Advanced tracking'), '{"template":"custom/advanced_tracking"}');
        $this->addAffiliateScreen('8411f431', 'Custom-Page', Gpf_Lang::_runtime('DirectLinks explained'), '{"template":"custom/directlink_explained"}');
        Gpf_Settings::set(Pap_Settings::AFFILIATE_MENU, '[{"items":[],"data":{"code":"5d569506"}},{"items":[],"data":{"code":"bc353dac"}},{"items":[{"items":[],"data":{"code":"ff0de1d4"}},{"items":[],"data":{"code":"581b530f"}},{"items":[],"data":{"code":"52d04ac0"}},{"items":[],"data":{"code":"df559b42"}}],"data":{"code":"c65d7c8e"}},{"items":[{"items":[],"data":{"code":"abd5a555"}},{"items":[],"data":{"code":"7c6a26a1"}},{"items":[],"data":{"code":"ad67466d"}},{"items":[],"data":{"code":"373881fc"}},{"items":[],"data":{"code":"84943ff4"}},{"items":[],"data":{"code":"ea2534c9"}},{"items":[],"data":{"code":"8d02c36a"}},{"items":[],"data":{"code":"e7dc308e"}},{"items:":[],"data":{"code":"b4r85r9w"}}],"data":{"code":"27366e9a"}},{"items":[],"data":{"code":"9a136deb"}},{"items":[],"data":{"code":"9782bb5e"}}]');
        Gpf_Settings::set(Pap_Settings::WELCOME_MESSAGE, Gpf_Lang::_runtime('<strong>Welcome to our affiliate program</strong><br/><br/>Use the menu to navigate through your panel.<br/>The <strong>Promotion</strong> menu contains campaigns, banners and other tools to help you in promotion.<br/>In <strong>Reports</strong> you can run various reports showing you the results - traffic sent by you, commissions you earned and so on.<br/><br/>'));
    }

    private function addAffiliateScreen($screenId, $code, $title, $params, $showHeader = Pap_Db_AffiliateScreen::HEADER_SHOW) {
        $screen = new Pap_Db_AffiliateScreen();
        $screen->setId($screenId);
        $screen->setAccountId($this->account->getId());
        $screen->setCode($code);
        $screen->setTitle($title);
        $screen->setShowHeader($showHeader);

        if($params != null) {
            $screen->setParams($params);
        }

        $screen->save();
    }

    private function setupDefaultTrackingParameters() {
        Gpf_Settings::set(Pap_Settings::MAIN_SITE_URL, 'http://www.examplesite.com/');
        Gpf_Settings::set(Pap_Settings::SUPPORT_DIRECT_LINKING, Gpf::YES);
    }

    private function createDefaultPayoutOptions() {
        $option = $this->createPayoutOption('8444af30','E',1,'PayPal');
        $option->setExportFileName('paypal_masspay.txt');
        $option->setExportRowTemplate('{$pp_email}\t{$amount}\t{$currency}\n');
        $option->insert();
        $this->addPayoutField('payout_option_8444af30','pp_email',Gpf_Lang::_runtime('PayPal Email'),'T','M');

        $this->addPayoutOption('8667d045','E',2,Gpf_Lang::_runtime('Check_payout_type'));
        $this->addPayoutField('payout_option_8667d045','payableto',Gpf_Lang::_runtime('Payable to'),'T','M');

        $this->addPayoutOption('5b868cd3','E',3,Gpf_Lang::_runtime('Moneybookers'));
        $this->addPayoutField('payout_option_5b868cd3','mb_email',Gpf_Lang::_runtime('Moneybookers Email'),'T','M');

        $this->addPayoutOption('dcc2ffa7','E',4,Gpf_Lang::_runtime('Bank / Wire transfer'));
        $this->addPayoutField('payout_option_dcc2ffa7','accnt_name',Gpf_Lang::_runtime('Bank account name'),'T','M');
        $this->addPayoutField('payout_option_dcc2ffa7','accnt_number',Gpf_Lang::_runtime('Bank account number'),'T','M');
        $this->addPayoutField('payout_option_dcc2ffa7','bank_name',Gpf_Lang::_runtime('Bank name'),'T','M');
        $this->addPayoutField('payout_option_dcc2ffa7','bank_code',Gpf_Lang::_runtime('Bank code'),'T','M');
        $this->addPayoutField('payout_option_dcc2ffa7','bank_address',Gpf_Lang::_runtime('Bank address'),'T','M');
        $this->addPayoutField('payout_option_dcc2ffa7','swift',Gpf_Lang::_runtime('Bank SWIFT code'),'T','M');

        Gpf_Settings::set(Pap_Settings::DEFAULT_PAYOUT_METHOD, '8444af30');
    }

    /**
     * @return Pap_Db_PayoutOption
     */
    private function createPayoutOption($id, $status, $order, $name) {
        $option = new Pap_Db_PayoutOption();
        $option->setId($id);
        $option->setStatus($status);
        $option->setOrder($order);
        $option->setName($name);
        $option->setAccountId($this->account->getId());
        return $option;
    }

    private function addPayoutOption($id, $status, $order, $name) {
        $option = $this->createPayoutOption($id, $status, $order, $name);
        $option->insert();
    }

    private function addPayoutField($formId, $code, $name, $type, $status) {
        $payoutField = new Gpf_Db_FormField();
        $payoutField->set('formid', $formId);
        $payoutField->set('code', $code);
        $payoutField->set('name', $name);
        $payoutField->set('rtype', $type);
        $payoutField->set('rstatus', $status);
        $payoutField->setAccountId($this->account->getId());
        $payoutField->insert();
    }

    

    private function copyToFileUploads($fileName) {
        $source = new Gpf_Io_File(Gpf_Paths::getInstance()->getResourcePath($fileName, Gpf_Paths::IMAGE_DIR));
        $targetRelativePath = Gpf_Paths::getInstance()->getAccountDirectoryRelativePath() .
        Gpf_Paths::FILES_DIRECTORY . $fileName;
        $target = new Gpf_Io_File('../' . $targetRelativePath);
        try {
            Gpf_Io_File::copy($source, $target, 0777);
        } catch (Exception $e) {
            throw new Gpf_Exception($this->_('Error during copy of sample image %s.', $source->getFileName()));
        }
        return Gpf_Paths::getInstance()->getFullBaseServerUrl() . $targetRelativePath;
    }

    private function createSampleBanners($campaignId) {
        $sampleBanners = new Pap_Install_SampleBanners($this->account);
        $sampleBanners->createSampleBanners($campaignId);
    }

    private function createSampleChannel() {
        $channel = new Pap_Db_Channel();
        $channel->setId('11111111');
        $channel->setPapUserId('11111111');
        $channel->setName('Sample advertising channel');
        $channel->setValue('testchnl');
        $channel->save();
    }

    private function createSampleDirectLinkUrl() {
        $directLinkUrl = new Pap_Db_DirectLinkUrl();
        $directLinkUrl->setId('11111111');
        $directLinkUrl->setPapUserId('11111111');
        $directLinkUrl->setUrl('*tests/banners_linking.*');
        $directLinkUrl->setStatus('A');
        $directLinkUrl->save();

        $directLinksBase = Pap_Tracking_DirectLinksBase::getInstance();
        $directLinksBase->regenerateDirectLinksFile();
    }

    private function setupCountries() {
        Gpf_Country_Countries::insertCountriesToDB(Gpf_Db_Country::STATUS_ENABLED);
        Gpf_Settings::set(Gpf_Settings_Gpf::DEFAULT_COUNTRY, "US");
    }

    private function createReferralCommissionType() {
        $commissionType = new Pap_Db_CommissionType();
        $commissionType->setType(Pap_Common_Constants::TYPE_REFERRAL);
        $commissionType->setStatus(Pap_Db_CommissionType::STATUS_ENABLED);
        $commissionType->setApproval(Pap_Db_CommissionType::APPROVAL_AUTOMATIC);
        $commissionType->setZeroOrdersCommission(Gpf::NO);
        $commissionType->setSaveZeroCommission(Gpf::NO);
        $commissionType->insert();
    }
}
?>
