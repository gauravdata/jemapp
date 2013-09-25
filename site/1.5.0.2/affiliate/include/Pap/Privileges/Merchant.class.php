<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: Paths.class.php 20126 2008-08-25 13:20:39Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * This class can be generated using parsePrivileges.php script in applications scripts folder
 *
 * @package PostAffiliatePro
 */
class Pap_Privileges_Merchant extends Pap_Privileges {
	protected function initDefaultPrivileges() {
		// Framework privileges
		$this->addPrivilege(Gpf_Privileges::AUTHENTICATION, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::PASSWORD_CONSTRAINTS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::CURRENCY, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::DB_FILE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::EMAIL_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::EXPORT_FILE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::FILTER, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::FORM_FIELD, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::GADGET, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::GRID_VIEW, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::IMPORT_EXPORT, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::LANGUAGE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::LOG, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::MAIL_OUTBOX, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::MAIL_TEMPLATE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::ONLINE_USER, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::PLUGIN, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::PROXY_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::REGIONAL_SETTINGS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::QUICKLAUNCH, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::ROLE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::SIDEBAR, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::TEMPLATE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::THEME, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::UPLOADED_FILE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::MYPROFILE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::WALLPAPER, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::WINDOW, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::RECURRENCE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::USER, Gpf_Privileges::P_READ);
		$this->addPrivilege(Gpf_Privileges::TASKS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::NEWSLETTER, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::IMPORT, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Gpf_Privileges::EXPORT, Gpf_Privileges::P_ALL);

		// Application privileges
		$this->addPrivilege(Pap_Privileges::AFFIILIATE_SCREEN, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_INVOICE, Pap_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_LOGIN_FORM, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_PANEL_SETTINGS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_SETTINGS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_SIGNUP_FORM, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_SIGNUP_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_TREE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::BANNER, Pap_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::BANNER, Pap_Privileges::P_WRITE);
		$this->addPrivilege(Pap_Privileges::BANNER, Pap_Privileges::P_ADD);
		$this->addPrivilege(Pap_Privileges::BANNER, Pap_Privileges::P_DELETE);
		$this->addPrivilege(Pap_Privileges::BANNER, Pap_Privileges::P_EXPORT);
		$this->addPrivilege(Pap_Privileges::BANNER_FORMAT_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::BANNER_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::BRANDING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::CAMPAIGN, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::CAMPAIGN_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::CAMPAIGNS_CATEGORIES, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::CHANNEL, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::CLICK, Pap_Privileges::P_DELETE);
		$this->addPrivilege(Pap_Privileges::CLICK, Pap_Privileges::P_EXPORT);
		$this->addPrivilege(Pap_Privileges::CLICK, Pap_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::CLICK, Pap_Privileges::P_WRITE);
		$this->addPrivilege(Pap_Privileges::COMMISSION, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::COMPRESSED_COMMISSION_PLACEMENT_MODEL, Gpf_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::COMPRESSED_COMMISSION_PLACEMENT_MODEL, Gpf_Privileges::P_WRITE);
		$this->addPrivilege(Pap_Privileges::COOKIES_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::CRONJOB, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::CURRENCY, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::DATABASE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::DIRECT_LINK, Pap_Privileges::P_ALL);
//		$this->addPrivilege(Pap_Privileges::DIRECT_LINK, Pap_Privileges::P_EXPORT);
//		$this->addPrivilege(Pap_Privileges::DIRECT_LINK, Pap_Privileges::P_WRITE);
//		$this->addPrivilege(Pap_Privileges::DIRECT_LINK, Pap_Privileges::P_ADD);
//		$this->addPrivilege(Pap_Privileges::DIRECT_LINK, Pap_Privileges::P_DELETE);
		$this->addPrivilege(Pap_Privileges::FEATURE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::FINANCIAL_OVERVIEW, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::FRAUD_PROTECTION, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::GENERAL_LINK, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::GENERAL_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::GETTING_STARTED, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_privileges::INTEGRATION_METHODS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::INVOICE_FORMAT, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::LINK_CLOAKER, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::LOGGING_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::MAIL_TEMPLATE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::MASS_EMAIL, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::MENU, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::MERCH_EMAIL_NOTIFICATION, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_EMAIL_NOTIFICATION, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::MERCHANT, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::PARAMETER_NAMES, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::PAY_AFFILIATE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::PAYOUT, Pap_Privileges::P_EXPORT);
		$this->addPrivilege(Pap_Privileges::PAYOUT, Pap_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::PAYOUT_HISTORY, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::PAYOUT_OPTION, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::PENDING_TASK, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::PERIOD_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::RECURRING_TRANSACTION, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::REPORT_PROBLEM, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::QUICK_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::SUB_AFF_SALE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::SUB_AFF_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::DAILY_REPORT, Pap_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::TRACKING_MOD_REWRITE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::TRACKING_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::TRAFFIC_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::TRANSACTION, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::TRANSACTION_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::TREND_STATS, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::URL_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::USER_COMM_GROUP, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::VAT_SETTING, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::GEOIP, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::COUNTRY, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::COMMISSION_GROUP, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::USER_IN_COMMISSION_GROUP, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::RULE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::COUPON, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::AFFILIATE_TRACKING_CODE, Gpf_Privileges::P_ALL);
		$this->addPrivilege(Pap_Privileges::PAY_AFFILIATE_STATS, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::CAMPAIGNS_OVERVIEW, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::BANNERS_OVERVIEW, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::AFFILIATES_OVERVIEW, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::TRANSACTIONS_OVERVIEW, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::REPORTS_OVERVIEW, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::PAYOUTS_OVERVIEW, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::CONFIGURATION_OVERVIEW, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::CLICK_INTEGRATION, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::TROUBLESHOOTING, Gpf_Privileges::P_READ);
		$this->addPrivilege(Pap_Privileges::INTEGRATION_OVERVIEW, Gpf_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::MAPOVERLAY, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Pap_Privileges::ACCOUNT, Gpf_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::ACCOUNT, Gpf_Privileges::P_ADD);
        $this->addPrivilege(Pap_Privileges::ACCOUNT, Gpf_Privileges::P_DELETE);
        $this->addPrivilege(Pap_Privileges::ACCOUNT, Gpf_Privileges::P_WRITE);
        $this->addPrivilege(Pap_Privileges::INVOICE, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Pap_Privileges::ACCOUNTING_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::NETWORK_INVOICE_FORMAT, Pap_Privileges::P_ALL);
        $this->addPrivilege(Pap_Privileges::ACCOUNT_SIGNUP_SETTING, Pap_Privileges::P_ALL);
        $this->addPrivilege(Pap_Privileges::ACCOUNT_NOTIFICATION, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::ACCOUNT_NOTIFICATION, Pap_Privileges::P_WRITE);
        $this->addPrivilege(Pap_Privileges::VISITOR_AFFILIATES, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::VIEWS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::VIEWS, Pap_Privileges::P_WRITE);
	}
}
?>
