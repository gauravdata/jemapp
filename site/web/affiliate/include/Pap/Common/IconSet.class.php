<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Common_IconSet extends Gpf_IconSet  {

    public function initIcons() {
        parent::initIcons();

        /* Merchant panel */
        $this->addIcon('AffiliatesOverview',       'icon-affiliatesoverview');
        $this->addIcon('AffiliateAdd',             'icon-affiliateadd');
        $this->addIcon('AffiliateTree',            'icon-affiliatetree');
        $this->addIcon('AffiliatesList',           'icon-affiliateslist');
        $this->addIcon('DirectLinksManager',       'icon-directlink');
        $this->addIcon('Configuration',            'icon-configuration');
        $this->addIcon('CampaignsOverview',        'icon-campaignsoverview');
        $this->addIcon('CampaignAdd',              'icon-campaignadd');
        $this->addIcon('CampaignsList',            'icon-campaignslist');
        $this->addIcon('BannersOverview',          'icon-bannersoverview');
        $this->addIcon('BannerAdd',                'icon-banneradd');
        $this->addIcon('BannersList',              'icon-bannerslist');
        $this->addIcon('TransactionsOverview',     'icon-transactionsoverview');
        $this->addIcon('RecurringCommissionsList', 'icon-recurringcommissionslist');
        $this->addIcon('CampaignsCategories',      'icon-campaignscategories');
        $this->addIcon('CommissionsList',          'icon-commissionslist');
        $this->addIcon('CommissionAdd',            'icon-commissionadd');
        $this->addIcon('RawClicksList',            'icon-clickslist');
        $this->addIcon('Reports',                  'icon-reports');
        $this->addIcon('PayoutsOverview',          'icon-payoutsoverview');
        $this->addIcon('PayAffiliates',            'icon-payaffiliates');
        $this->addIcon('PayoutsHistory',           'icon-payoutshistory');
        $this->addIcon('PayoutsByAffiliate',       'icon-payoutsbyaff');
        $this->addIcon('CalcCommission',           'icon-calccommission');
        $this->addIcon('CalcSettings',             'icon-calcsettings');

        $this->addIcon('GeneralSettings',          'icon-generalsettings');
        $this->addIcon('AffiliatesSettings',       'icon-affiliatesettings');

        $this->addIcon('MailOutbox',               'icon-outbox');
        $this->addIcon('SendMessage',              'icon-send-message');

        $this->addIcon('Tools',  		   	   	 'icon-tools');
        $this->addIcon('Integration',  			 'icon-integration');
        $this->addIcon('LogsList',  			 'icon-eventlog');
        $this->addIcon('ClicksTracking',  		 'icon-clickstracking');
        $this->addIcon('Troubleshooting',  	     'icon-troubleshooting');
        $this->addIcon('DatabaseStats',          'icon-databasestats');
        $this->addIcon('LanguageTranslations',   'icon-languagetranslations');
        $this->addIcon('Features',               'icon-features');
        $this->addIcon('ReportProblems',         'icon-reportproblems');
        $this->addIcon('CronJobIntegration',     'icon-cron');
        $this->addIcon('ApiIntegration',         'icon-apiintegration');

        $this->addIcon('QuickStart',          	'icon-quickstart');
    	$this->addIcon('TrafficStats',          'icon-trafficstats');
        $this->addIcon('PendingTasks',          'icon-pendingtasks');
        $this->addIcon('GettingStarted',        'icon-gettingstarted');

        $this->addIcon('Currency',     			'icon-currency');
        $this->addIcon('Languages',     		'icon-languages');
        $this->addIcon('MapOverlay',            'icon-mapoverlay');
        $this->addIcon('Logging',     			'icon-logging');
        $this->addIcon('ProxyServerConfig',     'icon-proxy');

        $this->addIcon('EmailTemplates',     	'icon-emailtemplates');
        $this->addIcon('MailAccount',     	 	'icon-emailsettings');

        $this->addIcon('TrackingSettings',     	'icon-trackingsettings');
        $this->addIcon('CookiesSettings',     	'icon-cookie');
        $this->addIcon('FraudProtection',     	'icon-fraudprotection');
        $this->addIcon('ParameterNames',     	'icon-parameternames');
        $this->addIcon('BannersLinksFormat',    'icon-linkformats');

        $this->addIcon('PayoutsBalance',     	'icon-payoutsbalance');
        $this->addIcon('PayoutOptions',     	'icon-payoutsettings');

        $this->addIcon('InvoiceFormat',     	'icon-invoiceformat');
        $this->addIcon('VATHandling',     		'icon-vathandling');
        $this->addIcon('IntegrationMethods',    'icon-integrationmethods');

        $this->addIcon('ImpressionCommission',  'icon-impressioncommision');
        $this->addIcon('ClickCommission',  		'icon-clickcommission');
        $this->addIcon('SaleCommission',  		'icon-salecommission');
        $this->addIcon('ActionCommission',  	'icon-actioncommision');
        $this->addIcon('TopReferringURL',		'icon-topreferringurl');

        $this->addIcon('OfflineSale',           'icon-offlinesale');

        $this->addIcon('VisitorAffiliatesList', 'icon-affiliatetree');

        $this->addIcon('ViewsList',             'icon-views');


        /* Affiliate panel */
	    $this->addIcon('Home',                   'icon-home');
        $this->addIcon('Reports',                'icon-reports');
        $this->addIcon('QuickReport',            'icon-quickreport');
        $this->addIcon('TrendsReport',           'icon-trendsreport');
        $this->addIcon('ReportRawClicks',        'icon-clickslist');
        $this->addIcon('ReportCommissions',      'icon-commreport');
        $this->addIcon('ChannelStatsReport',     'icon-channelstatsreport');
        $this->addIcon('Channels',               'icon-channels');
        $this->addIcon('MyProfile',              'icon-userprofile');
        $this->addIcon('PaymentDetails',         'icon-paymentdetails');
        $this->addIcon('Promotion',              'icon-promotion');
        $this->addIcon('SignupSubaffiliates',    'icon-signupsubaff');
        $this->addIcon('PersonalDetails',        'icon-personaldetails');
        $this->addIcon('EmailNotifications',     'icon-emailnotification');
        $this->addIcon('SubaffiliateSaleStats',  'icon-subaffiliatestat');
        $this->addIcon('SubaffiliatesTree',      'icon-subaffiliates');
        $this->addIcon('Campaigns',              'icon-campaignslist');
        $this->addIcon('Banners',                'icon-banners');
        $this->addIcon('PayoutsList',            'icon-payoutslist');
        $this->addIcon('DirectLink',             'icon-directlink');
        $this->addIcon('SubIdTracking',          'icon-subidtracking');
        $this->addIcon('AffLinkProtector',       'icon-afflinkprotector');
        $this->addIcon('AdvancedFunctionality',  'icon-advancedfunc');
        $this->addIcon('ContactUs',              'icon-contactus');
        $this->addIcon('BroadcastEmail',         'icon-broadcastemail');
        $this->addIcon('MapOverlay',             'icon-mapoverlay');

        $this->addIcon('PanelSettings',        	'icon-panelsettings');
        $this->addIcon('Plugins',        		'icon-plugins');

        $this->addIcon('About',        		     'icon-about');
        $this->addIcon('SignupFormSettings', 	 'icon-signupform');
        $this->addIcon('ThemesSettings',         'icon-themes');
        $this->addIcon('AffPanelContents',       'icon-affpanel');
        
        $this->addIcon('Folder',                 'icon-folder');
        $this->addIcon('FolderGray',             'icon-foldergrey');

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.iconSet', $this);
    }

    protected function getImageUrl($imageFile, $size) {
        try {
            return Gpf_Paths::getInstance()->getImageUrl($imageFile['imageName']."-".$size.".".$imageFile['extension'], '', false);
        } catch (Gpf_ResourceNotFoundException $e) {
            try {
                return Gpf_Paths::getInstance()->getImageUrl(
                        $imageFile['imageName']."-".$size.".".$imageFile['extension'],
                        "affiliates");
            } catch (Gpf_ResourceNotFoundException $e) {
            }
        }
    }
}

?>
