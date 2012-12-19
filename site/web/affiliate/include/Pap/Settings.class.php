<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * Settings object that loads settings from database or file
 *
 * @package PostAffiliatePro
 */
class Pap_Settings extends Gpf_Settings_Gpf {

    const PARAM_NAME_USER_ID = 'param_name_user_id';
    const PARAM_NAME_BANNER_ID = 'param_name_banner_id';
    const PARAM_NAME_CAMPAIGN_ID = 'param_name_campaign_id';
    const PARAM_NAME_ROTATOR_ID = 'param_name_rotator_id';
    const PARAM_NAME_EXTRA_DATA = 'param_name_extra_data';
    const PARAM_NAME_DESTINATION_URL = 'param_name_dest_url';

    /* Default param names */
    const PARAM_AFFILIATE_ID_DEFAULT = 'a_aid';
    const PARAM_BANNER_ID_DEFAULT = 'a_bid';
    const PARAM_CAMPAIGN_ID_DEFAULT = 'a_cid';
    const PARAM_ROTATOR_ID_DEFAULT = 'a_rid';
    const PARAM_EXTRA_DATA_DEFAULT = 'data';
    const PARAM_DESTINATION_URL_DEFAULT = 'desturl';

    const PROGRAM_NAME = "programName";
    const PROGRAM_LOGO = "programLogo";
    const DEFAULT_MERCHANT_PANEL_THEME = 'defaultMerchantPanelTheme';
    const DEFAULT_AFFILIATE_PANEL_THEME = 'defaultAffiliatePanelTheme';
    const DEFAULT_AFFILIATE_SIGNUP_THEME = 'defaultAffiliateSignupTheme';
    const DEBUG_TYPES ='debug_types';
    const DELETE_COOKIE ='delete_cookie';
    const P3P_POLICY_COMPACT ='p3p_policy_compact';
    const URL_TO_P3P ='url_to_p3p';
    const OVERWRITE_COOKIE ='overwrite_cookie';
    const MAIN_SITE_URL ='mainSiteUrl';
    const GPF_VERSION = 'gpf_version';
    const PAP_VERSION = 'pap_version';
    const WELCOME_MESSAGE = 'welcomeMessage';
    const MULTIPLE_CURRENCIES = "multipleCurrencies";
    const COOKIE_DOMAIN = 'cookie_domain';

    const BRANDING_TEXT = "brandingText";
    const BRANDING_KNOWLEDGEBASE_LINK = "knowledgebase_url";
    const BRANDING_POST_AFFILIATE_PRO_HELP_LINK = "post_affiliate_pro_help";
    const BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK = "quality_unit_postaffiliate_link";
    const BRANDING_QUALITYUNIT_CHANGELOG_LINK = "qualityunit_changelog";
    const BRANDING_QUALITYUNIT_PAP = "qualityunit_pap";
    const BRANDING_TEXT_POST_AFFILIATE_PRO = "post_affiliate_pro";
    const BRANDING_TUTORIAL_VIDEOS_BASE_LINK = "qualityunit_tutorial_link";
    const BRANDING_TUTORIAL_VIDEOS_ENABLED = "qualityunit_tutorial_videos_enabled";

    const GETTING_STARTED_CHECKS = 'gettingStartedChecks';
    const GETTING_STARTED_SHOW = 'gettingStartedShow';
    const DEFAULT_PAYOUT_METHOD = 'defaultPayoutMethod';
    const AFFILIATE_APPROVAL = 'affiliate_approval';
    const AFFILIATE_LOGOUT_URL = 'affiliate_logout_url';
    const AFFILIATE_MENU = "affiliateMenu";
    const EMPTY_MENU = "[]";
    const SUPPORT_DIRECT_LINKING = 'support_direct_linking';
    const SUPPORT_SHORT_ANCHOR_LINKING = 'support_short_anchor_linking';
    const DEFAULT_MERCHANT_ID = 'default_merchant_id';
    const DEFAULT_MERCHANT_ID_VALUE = '11112222';
    const TIERS_VISIBLE_TO_AFFILIATE = 'tiers_visible_to_affiliate';
    const AFFILIATE_CANNOT_CHANGE_HIS_USERNAME = 'affiliate_cannot_change_his_username';
    const POST_SIGNUP_TYPE_SETTING_NAME = "postSignupType";
    const POST_SIGNUP_URL_SETTING_NAME = "postSignupUrl";
    const SIGNUP_TERMS_SETTING_NAME = "termsAndConditions";
    const FORCE_TERMS_ACCEPTANCE_SETTING_NAME = "forceTermsAcceptance";
    const INCLUDE_PAYOUT_OPTIONS = "includePayoutOptions";
    const PAYOUT_OPTIONS = "payoutOptions";
    const FORCE_PAYOUT_OPTION = "forcePayoutOption";
    const OPTIONAL_PAYOUT_FIELDS = "optionalPayoutFields";
    const ASSIGN_NON_REFERRED_AFFILIATE_TO = "assignNonReferredAffiliateTo";
    const AUTO_DELETE_RAWCLICKS = "deleterawclicks";
    const AUTO_DELETE_EXPIRED_VISITORS = "deleteExpiredVisitors";
    const ALLOW_COMPUTE_NEGATIVE_COMMISSION = "allowComputeNegativeCommission";

    const FLASH_BANNER_DEFAULT_FORMAT = '<object width="{$width}" height="{$height}">
  <param name="movie" value="{$flashurl}?clickTAG={$targeturl_encoded}">
  <param name="menu" value="false"/>
  <param name="quality" value="medium"/>
  <param name="wmode" value="{$wmode}"/>
  <embed src="{$flashurl}?clickTAG={$targeturl_encoded}" width="{$width}" height="{$height}" loop="{$loop}" menu="false" swLiveConnect="FALSE" wmode="{$wmode}" allowscriptaccess="always"></embed>
</object>
{$impression_track}';
    const FLASH_BANNER_FORMAT_SETTING_NAME = "BannerFormatFlash";

    const IMAGE_BANNER_DEFAULT_FORMAT = '<a href="{$targeturl}" target="_top"><img src="{$image_src}" alt="{$alt}" title="{$alt}" width="{$width}" height="{$height}" /></a>{$impression_track}';
    const IMAGE_BANNER_FORMAT_SETTING_NAME = "BannerFormatImagebanner";

    const TEXT_BANNER_DEFAULT_FORMAT = '<a href="{$targeturl}" target="_top"><strong>{$title}</strong><br/>{$description}</a>{$impression_track}';
    const TEXT_BANNER_FORMAT_SETTING_NAME = "BannerFormatTextlink";

    const PAYOUT_INVOICE = 'payout_invoice';
    const GENERATE_INVOICES = 'generate_invoices';
    const SEND_PAYMENT_TO_AFFILIATE = 'send_payment_to_affiliate';
    const SEND_GENERATED_INVOICES_TO_MERCHANT = 'send_generated_invoices_to_merchant';
    const SEND_GENERATED_INVOICES_TO_AFFILIATE = 'send_generated_invoices_to_affiliates';
    const INVOICE_BCC_RECIPIENT = 'invoice_bcc_recipient';
    const DEFAULT_INVOICE = '<b>Invoice Number:</b> {$invoicenumber}<br/>
    <b>Invoice date:</b> {$date}<br/>
    <br/>
    <b>Affiliate Details:</b><br/>
    {$firstname} {$lastname} ({$username})<br/>
    {$data2}{*##Company name##*}<br/>
    {$data3}{*##Street##*}<br/>
    {$data7}{*##Zipcode##*} {$data4}{*##City##*}<br/>
    {$data5}{*##State##*} {$data6}{*##Country##*}<br/>
    <br/>
    <b>Payment Details:</b> Affiliate commissions<br/>
    Amount: {$payoutcurrency}{$payment}<br/>
    VAT ({$vat_percentage}%): {$payoutcurrency}{$payment_vat_part}<br/>
    <br/>
    <b>Note:</b><br/>
    {$affiliate_note}<br/>';

    const AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME = 'aff_notification_on_subaff_sale_default';
    const AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME = 'aff_notification_on_subaff_sale_enabled';

    const AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME = 'aff_notification_on_change_comm_status_default';
    const AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME = 'aff_notification_on_change_comm_status_enabled';
    const AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_OPTION_STATUS = 'aff_notification_on_change_comm_status_option';

    const AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME = 'aff_notification_on_new_sale_enabled';
    const AFF_NOTIFICATION_ON_NEW_SALE_STATUS = 'aff_notification_on_new_sale_status';
    const AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME = 'aff_notification_on_new_sale_default';
    const AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT = 'aff_notification_on_direct_link_default';
    const AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED = 'aff_notification_on_direct_link_enabled';

    const AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME = 'aff_notification_on_subaff_signup_default';
    const AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME = 'aff_notification_on_subaff_signup_enabled';
    
    const RECAPTCHA_PRIVATE_KEY = 'recaptcha_private_key';
    const RECAPTCHA_PUBLIC_KEY = 'recaptcha_public_key';
    const RECAPTCHA_ENABLED = 'recaptcha_enabled';
    const RECAPTCHA_THEME = 'recaptcha_theme';
    
    const AFF_SEND_EMAILS_PER_MINUTE_SETTING_NAME = 'aff_send_emails_per_minute';

    const NOTIFICATION_NEW_USER_SETTING_NAME = 'notification_new_user';
    const NOTIFICATION_ON_SALE = 'notification_on_sale';
    const NOTIFICATION_ON_SALE_STATUS = 'notification_on_sale_status';
    const NOTIFICATION_NEW_DIRECT_LINK = 'notification_new_direct_link';

    const NOTIFICATION_PAY_DAY_REMINDER = 'notification_pay_day_reminder';
    const NOTIFICATION_PAY_DAY_REMINDER_DAY_OF_MONTH = 'notification_pay_day_reminder_day_of_month';
    const NOTIFICATION_PAY_DAY_REMINDER_RECURRENCE_MONTH = 'notification_pay_day_reminder_recurrence_month';

    const NOTIFICATION_DAILY_REPORT = "notification_daily_report";
    const NOTIFICATION_WEEKLY_REPORT = "notification_weekly_report";
    const NOTIFICATION_WEEKLY_REPORT_START_DAY = "notification_weekly_report_start_day";
    const NOTIFICATION_WEEKLY_REPORT_SENT_ON = "notification_weekly_report_sent_on";
    const NOTIFICATION_MONTHLY_REPORT = "notification_monthly_report";
    const NOTIFICATION_MONTHLY_REPORT_SENT_ON = "notification_monthly_report_sent_on";    

    const AFF_NOTIFICATION_DAILY_REPORT_ENABLED = 'aff_notification_daily_report_enabled';
    const AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED = 'aff_notification_weekly_report_enabled';
    const AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED = 'aff_notification_monthly_report_enabled';
    const AFF_NOTIFICATION_DAILY_REPORT_DEFAULT = 'aff_notification_daily_report_default';
    const AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT = 'aff_notification_weekly_report_default';
    const AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT = 'aff_notification_monthly_report_default';
    
    const REPORTS_MAX_TRANSACTIONS_COUNT = 'notification_report_maxtransactions';

    const NOTIFICATION_ON_JOIN_TO_CAMPAIGN = 'notification_on_join_to_campaign';
    const NOTIFICATION_ON_COMMISSION_APPROVED = 'notification_on_commission_approved';
    const AFF_NOTIFICATION_ON_CHANGE_STATUS_FOR_CAMPAIGN = 'aff_notification_on_change_status_for_campaign';
    const AFF_NOTIFICATION_CAMPAIGN_INVITATION = 'aff_notification_campaign_invitation';


    const AFF_NOTOFICATION_BEFORE_APPROVAL = 'aff_notification_before_approval';
    const AFF_NOTOFICATION_SIGNUP_APPROVED_DECLINED = 'aff_notification_signup_approved_declined';

    const IP_VALIDITY_FORMAT_SETTING_NAME = 'ip_validity_format';
    const IP_VALIDITY_SETTING_NAME = 'ip_validity';
    const TRACK_BY_IP_SETTING_NAME = 'track_by_ip';
    const SAVE_UNREFERED_SALE_LEAD_SETTING_NAME = 'save_unrefered_sale_lead';
    const DEFAULT_AFFILIATE_SETTING_NAME = 'default_affiliate';
    const FORCE_CHOOSING_PRODUCTID_SETTING_NAME = 'force_choosing_productid';

    const PAYOUTS_MINIMUM_PAYOUT_SETTING_NAME = 'payouts_minimum_payout';
    const PAYOUTS_PAYOUT_OPTIONS_SETTING_NAME = 'payouts_payout_options';
    const DEFAULT_PAYOUT_OPTIONS = "100,200,300,400,500";
    const DEFAULT_MINIMUM_PAYOUT = "300";

    const MOD_REWRITE_PREFIX_SETTING_NAME = 'modrewrite_prefix';
    const MOD_REWRITE_SEPARATOR_SETTING_NAME = 'modrewrite_separator';
    const MOD_REWRITE_SUFIX_SETTING_NAME = 'modrewrite_suffix';

    const DEFAULT_PREFIX = 'ref/';
    const DEFAULT_SEPARATOR = '/';
    const DEFAULT_SUFFIX = '.html';

    const REPEATING_SIGNUPS_SETTING_NAME = 'repeating_signups';
    const REPEATING_SIGNUPS_ACTION_SETTING_NAME = 'repeating_signups_action';
    const REPEATING_SIGNUPS_SECONDS_SETTING_NAME = 'repeating_signups_seconds';
    const REPEATING_CLICKS_SETTING_NAME = 'repeating_clicks';
    const DUPLICATE_ORDERS_IP_SETTING_NAME = 'duplicate_orders_ip';
    const APPLY_TO_EMPTY_ID_SETTING_NAME = 'aplly_to_empty_orders_id';
    const DUPLICATE_ORDERS_ID_SETTING_NAME = 'duplicate_orders_id';
    const DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME = 'duplicate_orders_id_action';
    const DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME = 'duplicate_orders_ip_seconds';
    const DUPLICATE_ORDERS_ID_MESSAGE_SETTING_NAME = 'duplicate_orders_id_message';
    const DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME = 'duplicate_orders_ip_action';
    const DUPLICATE_ORDERS_IP_SAMECAMPAIGN_SETTING_NAME = 'duplicate_orders_ip_samecampaign';
    const DUPLICATE_ORDERS_IP_SAMEORDERID_SETTING_NAME = 'duplicate_orders_ip_sameorderid';
    const DUPLICATE_ORDER_ID_HOURS_SETTING_NAME = 'duplicate_order_id_hours';
    const DUPLICATE_ORDERS_IP_MESSAGE_SETTING_NAME = 'duplicate_orders_ip_message';
    const REPEATING_CLICKS_ACTION_SETTING_NAME = 'repeating_clicks_action';
    const REPEATING_CLICKS_SECONDS_SETTING_NAME = 'repeating_clicks_seconds';
    const REPEATING_BANNER_CLICKS = 'repeating_banner_clicks';

    const BANNEDIPS_CLICKS_FROM_IFRAME = 'bannedips_clicks_from_iframe';
    const BANNEDIPS_CLICKS = 'bannedips_clicks';
    const BANNEDIPS_LIST_CLICKS = 'bannedips_list_clicks';
    const BANNEDIPS_CLICKS_ACTION = 'bannedips_clicks_action';
    const BANNEDIPS_SALES = 'bannedips_sales';
    const BANNEDIPS_LIST_SALES = 'bannedips_list_sales';
    const BANNEDIPS_SALES_ACTION = 'bannedips_sales_action';
    const BANNEDIPS_SALES_MESSAGE = 'bannedips_sales_message';
    const BANNEDIPS_SIGNUPS = 'bannedips_signups';
    const BANNEDIPS_LIST_SIGNUPS = 'bannedips_list_signups';
    const BANNEDIPS_SIGNUPS_ACTION = 'bannedips_signups_action';

    const GEOIP_CLICKS = 'geoip_clicks';
    const GEOIP_CLICKS_BLACKLIST = 'clicks_countries_blacklist';
    const GEOIP_CLICKS_BLACKLIST_ACTION = 'clicks_countries_blacklist_action';
    const GEOIP_SALES = 'geoip_sales';
    const GEOIP_SALES_BLACKLIST = 'sales_countries_blacklist';
    const GEOIP_SALES_BLACKLIST_ACTION = 'sales_countries_blacklist_action';
    const GEOIP_AFFILIATES = 'geoip_affiliates';
    const GEOIP_AFFILIATES_BLACKLIST = 'affiliates_countries_blacklist';
    const GEOIP_AFFILIATES_BLACKLIST_ACTION = 'affiliates_countries_blacklist_action';

    const DEFAULT_REPEATING_CLICKS = "N";
    const DEFAULT_REPEATING_CLICKS_SECONDS = 0;
    const DEFAULT_REPEATING_CLICKS_ACTION = "D";
    const DEFAULT_REPEATING_SIGNUPS = "N";
    const DEFAULT_REPEATING_SIGNUPS_SECONDS = 0;
    const DEFAULT_REPEATING_SIGNUPS_ACTION = "DS";
    const DEFAULT_DUPLICATE_ORDERS_IP = "N";
    const DEFAULT_DUPLICATE_ORDERS_IP_ACTION = "D";
    const DEFAULT_DUPLICATE_ORDERS_IP_SECONDS = "";
    const DEFAULT_DUPLICATE_ORDERS_IP_MESSAGE = "";
    const DEFAULT_DUPLICATE_ORDERS_IP_SAMECAMPAIGN = "N";
    const DEFAULT_DUPLICATE_ORDERS_IP_SAMEORDERID = "N";
    const DEFAULT_DUPLICATE_ORDERS_ID = "N";
    const DEFAULT_DUPLICATE_ORDERS_ID_ACTION = "D";
    const DEFAULT_DUPLICATE_ORDERS_ID_MESSAGE = "";
    const DEFAULT_DUPLICATE_ORDERS_ID_IN_HOURS = "N";
    const DEFAULT_DUPLICATE_ORDERS_ID_HOURS = "";
    const DEFAULT_APPLY_TO_EMPTY_ORDERS_ID = "";

    const SETTING_LINKING_METHOD = 'linking_method';

    const PAYOUT_INVOICE_WITH_VAT_SETTING_NAME = 'payout_invoice_with_vat';
    const VAT_COMPUTATION_SETTING_NAME = 'vat_computation';
    const VAT_PERCENTAGE_SETTING_NAME = 'vat_percentage';
    const SUPPORT_VAT_SETTING_NAME = 'support_vat';

    const SIGNUP_BONUS = 'signupBonus';

    const MATRIX_HEIGHT = 'matrix_height';
    const MATRIX_WIDTH = 'matrix_width';
    const FULL_FORCED_MATRIX = 'full_forced_matrix';
    const MATRIX_SPILLOVER = 'matrix_spillover';
    const MATRIX_AFFILIATE = 'matrix_affiliate';
    const MATRIX_EXPAND_HEIGHT = 'matrixExpandHeight';
    const MATRIX_EXPAND_WIDTH = 'matrixExpandWidth';
    const MATRIX_FILL_BONUS = 'matrixFillBonus';
    const MATRIX_OTHER_FILL_BONUS = 'matrixOtherFillBonus';

    const MATRIX_HEIGHT_DEFAULT_VALUE = 0;
    const MATRIX_WIDTH_DEFAULT_VALUE = 0;
    const MATRIX_EXPAND_HEIGHT_DEFAULT_VALUE = 1;
    const MATRIX_EXPAND_WIDTH_DEFAULT_VALUE = 0;
    const MATRIX_FILL_BONUS_DEFAULT_VALUE = 0;
    const MATRIX_OTHER_FILL_BONUS_DEFAULT_VALUE = 0;

    const NOT_SET_PARENT_AFFILIATE = 'notSetParentAffiliate';

    const IMPRESSIONS_TABLE_INPUT = 'impTableInput';
    const IMPRESSIONS_TABLE_PROCESS = 'impTableProcess';

    const VISITS_TABLE_INPUT = 'visitsTableInput';
    const VISITS_TABLE_PROCESS = 'visitsTableProcess';
    const VISIT_OFFLINE_PROCESSING_DISABLE = 'offlineVisitProcessingDisabled';

    const MERCHANT_NOTIFICATION_EMAIL = 'merchant_notification_email';
    const LAST_BILLING_DATE = 'last_billing_date';

    protected function defineFileSettings() {
        parent::defineFileSettings();
        $this->addFileSetting(self::PARAM_NAME_USER_ID, self::PARAM_AFFILIATE_ID_DEFAULT);
        $this->addFileSetting(self::PARAM_NAME_BANNER_ID, self::PARAM_BANNER_ID_DEFAULT);
        $this->addFileSetting(self::PARAM_NAME_CAMPAIGN_ID, self::PARAM_CAMPAIGN_ID_DEFAULT);
        $this->addFileSetting(self::PARAM_NAME_ROTATOR_ID, self::PARAM_ROTATOR_ID_DEFAULT);
        $this->addFileSetting(self::PARAM_NAME_EXTRA_DATA, self::PARAM_EXTRA_DATA_DEFAULT);
        $this->addFileSetting(self::PARAM_NAME_EXTRA_DATA . '1', self::PARAM_EXTRA_DATA_DEFAULT . '1');
        $this->addFileSetting(self::PARAM_NAME_EXTRA_DATA . '2', self::PARAM_EXTRA_DATA_DEFAULT . '2');
        $this->addFileSetting(self::PARAM_NAME_DESTINATION_URL, self::PARAM_DESTINATION_URL_DEFAULT);

        $this->addFileSetting(self::DEBUG_TYPES, '');
        $this->addFileSetting(self::DELETE_COOKIE, 'N');
        $this->addFileSetting(self::P3P_POLICY_COMPACT, 'NOI NID ADMa DEVa PSAa OUR BUS ONL UNI COM STA OTC');
        $this->addFileSetting(self::URL_TO_P3P, '');
        $this->addFileSetting(self::OVERWRITE_COOKIE, 'N');
        $this->addFileSetting(self::COOKIE_DOMAIN, $this->getDefaultCookieDomainValidity());

        $this->addFileSetting(self::IMPRESSIONS_TABLE_INPUT, 0);
        $this->addFileSetting(self::IMPRESSIONS_TABLE_PROCESS, 2);

        $this->addFileSetting(self::VISITS_TABLE_INPUT, 0);
        $this->addFileSetting(self::VISITS_TABLE_PROCESS, 2);
        $this->addFileSetting(self::BANNEDIPS_CLICKS_FROM_IFRAME, Gpf::NO);
        $this->addFileSetting(self::VISIT_OFFLINE_PROCESSING_DISABLE, '');
    }

    protected function defineDbSettings() {
        $this->addDbSetting(self::BRANDING_TEXT, Pap_Branding::DEFAULT_BRANDING_TEXT);
        $this->addDbSetting(self::DEFAULT_MERCHANT_PANEL_THEME, Pap_Branding::DEFAULT_MERCHANT_PANEL_THEME);
        $this->addDbSetting(self::DEFAULT_AFFILIATE_PANEL_THEME, Pap_Branding::DEFAULT_AFFILIATE_PANEL_THEME);
        $this->addDbSetting(self::DEFAULT_AFFILIATE_SIGNUP_THEME, Pap_Branding::DEFAULT_SIGNUP_THEME);
        $this->addDbSetting(self::PROGRAM_NAME, Gpf_Lang::_runtime('Affiliate program'));
        $this->addDbSetting(self::PROGRAM_LOGO);
        $this->addDbSetting(self::WELCOME_MESSAGE, Gpf_Lang::_runtime('Welcome to affiliate program'));
        $this->addDbSetting(self::GETTING_STARTED_CHECKS, '');
        $this->addDbSetting(self::GETTING_STARTED_SHOW, GPF::YES);

        $this->addDbSetting(self::MAIN_SITE_URL, '');
        $this->addDbSetting(self::DEFAULT_PAYOUT_METHOD, '');
        //TODO: extract 'M' to const
        $this->addDbSetting(self::AFFILIATE_APPROVAL, 'M');
        $this->addDbSetting(self::AFFILIATE_LOGOUT_URL, '../index.php');
        $this->addDbSetting(self::TIERS_VISIBLE_TO_AFFILIATE, -1);
        $this->addDbSetting(self::AFFILIATE_CANNOT_CHANGE_HIS_USERNAME, Gpf::NO);
        $this->addDbSetting(self::AFFILIATE_MENU, self::EMPTY_MENU);
        $this->addDbSetting(self::SUPPORT_DIRECT_LINKING, Gpf::YES);
        $this->addDbSetting(self::SUPPORT_SHORT_ANCHOR_LINKING, Gpf::NO);
        $this->addDbSetting(self::GPF_VERSION, '');
        $this->addDbSetting(self::PAP_VERSION, '');

        $this->addDbSetting(self::MULTIPLE_CURRENCIES, Gpf::NO);
        $this->addDbSetting(self::SIGNUP_TERMS_SETTING_NAME, '');
        $this->addDbSetting(self::POST_SIGNUP_TYPE_SETTING_NAME, 'page');
        $this->addDbSetting(self::POST_SIGNUP_URL_SETTING_NAME, '');
        $this->addDbSetting(self::FORCE_TERMS_ACCEPTANCE_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::INCLUDE_PAYOUT_OPTIONS, Gpf::NO);
        $this->addDbSetting(self::PAYOUT_OPTIONS, 'A');
        $this->addDbSetting(self::FORCE_PAYOUT_OPTION, Gpf::NO);
        $this->addDbSetting(self::ASSIGN_NON_REFERRED_AFFILIATE_TO, '');

        $this->addDbSetting(self::FLASH_BANNER_FORMAT_SETTING_NAME, self::FLASH_BANNER_DEFAULT_FORMAT);
        $this->addDbSetting(self::IMAGE_BANNER_FORMAT_SETTING_NAME, self::IMAGE_BANNER_DEFAULT_FORMAT);
        $this->addDbSetting(self::TEXT_BANNER_FORMAT_SETTING_NAME, self::TEXT_BANNER_DEFAULT_FORMAT);

        $this->addDbSetting(self::GENERATE_INVOICES, Gpf::NO);
        $this->addDbSetting(self::SEND_GENERATED_INVOICES_TO_MERCHANT, Gpf::NO, true);
        $this->addDbSetting(self::SEND_GENERATED_INVOICES_TO_AFFILIATE, Gpf::NO);
        $this->addDbSetting(self::SEND_PAYMENT_TO_AFFILIATE, Gpf::NO);
        $this->addDbSetting(self::PAYOUT_INVOICE);
        $this->addDbSetting(self::INVOICE_BCC_RECIPIENT, '', true);

        $this->addDbSetting(self::NOTIFICATION_ON_SALE, Gpf::NO, true);
        $this->addDbSetting(self::NOTIFICATION_ON_SALE_STATUS, 'A,P,D', true);
        $this->addDbSetting(self::AFF_NOTOFICATION_BEFORE_APPROVAL, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTOFICATION_SIGNUP_APPROVED_DECLINED, Gpf::YES);
        $this->addDbSetting(self::NOTIFICATION_NEW_DIRECT_LINK, Gpf::NO, true);

        $this->addDbSetting(self::NOTIFICATION_PAY_DAY_REMINDER, Gpf::NO, true);
        $this->addDbSetting(self::NOTIFICATION_PAY_DAY_REMINDER_DAY_OF_MONTH, '15', true);
        $this->addDbSetting(self::NOTIFICATION_PAY_DAY_REMINDER_RECURRENCE_MONTH, '1', true);

        $this->addDbSetting(self::NOTIFICATION_DAILY_REPORT, Gpf::NO, true);
        $this->addDbSetting(self::NOTIFICATION_WEEKLY_REPORT, Gpf::NO, true);
        $this->addDbSetting(self::NOTIFICATION_WEEKLY_REPORT_START_DAY, '0', true);
        $this->addDbSetting(self::NOTIFICATION_WEEKLY_REPORT_SENT_ON, '0', true);
        $this->addDbSetting(self::NOTIFICATION_MONTHLY_REPORT, Gpf::NO, true);
        $this->addDbSetting(self::NOTIFICATION_MONTHLY_REPORT_SENT_ON, '1', true);

        $this->addDbSetting(self::AFF_NOTIFICATION_DAILY_REPORT_ENABLED, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT, Gpf::NO);
        
        $this->addDbSetting(self::REPORTS_MAX_TRANSACTIONS_COUNT, 1000);

        $this->addDbSetting(self::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_NEW_SALE_STATUS, 'A,P,D');
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_OPTION_STATUS, 'A,P,D');
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::NOTIFICATION_NEW_USER_SETTING_NAME, Gpf::NO, true);
        $this->addDbSetting(self::MERCHANT_NOTIFICATION_EMAIL, '', true);

        $this->addDbSetting(self::NOTIFICATION_ON_JOIN_TO_CAMPAIGN, Gpf::NO, true);
        $this->addDbSetting(self::NOTIFICATION_ON_COMMISSION_APPROVED, Gpf::NO, true);
        $this->addDbSetting(self::AFF_NOTIFICATION_ON_CHANGE_STATUS_FOR_CAMPAIGN, Gpf::NO);
        $this->addDbSetting(self::AFF_NOTIFICATION_CAMPAIGN_INVITATION, Gpf::YES);
        
        $this->addDbSetting(self::AFF_SEND_EMAILS_PER_MINUTE_SETTING_NAME, 30);
        
        $this->addDbSetting(self::RECAPTCHA_ENABLED, Gpf::NO);
        $this->addDbSetting(self::RECAPTCHA_THEME, 'white');
        $this->addDbSetting(self::RECAPTCHA_PRIVATE_KEY, '');
        $this->addDbSetting(self::RECAPTCHA_PUBLIC_KEY, '');
      
        $this->addDbSetting(self::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::TRACK_BY_IP_SETTING_NAME, Gpf::YES);
        $this->addDbSetting(self::IP_VALIDITY_SETTING_NAME, 2);
        $this->addDbSetting(self::IP_VALIDITY_FORMAT_SETTING_NAME, 'D');
        $this->addDbSetting(self::DEFAULT_AFFILIATE_SETTING_NAME, '');
        $this->addDbSetting(self::FORCE_CHOOSING_PRODUCTID_SETTING_NAME, Gpf::NO);

        $this->addDbSetting(self::PAYOUTS_PAYOUT_OPTIONS_SETTING_NAME, self::DEFAULT_PAYOUT_OPTIONS);
        $this->addDbSetting(self::PAYOUTS_MINIMUM_PAYOUT_SETTING_NAME, self::DEFAULT_MINIMUM_PAYOUT);

        $this->addDbSetting(self::MOD_REWRITE_PREFIX_SETTING_NAME, self::DEFAULT_PREFIX);
        $this->addDbSetting(self::MOD_REWRITE_SEPARATOR_SETTING_NAME, self::DEFAULT_SEPARATOR);
        $this->addDbSetting(self::MOD_REWRITE_SUFIX_SETTING_NAME, self::DEFAULT_SUFFIX);

        $this->addDbSetting(self::REPEATING_CLICKS_ACTION_SETTING_NAME, self::DEFAULT_REPEATING_CLICKS_ACTION);
        $this->addDbSetting(self::REPEATING_CLICKS_SECONDS_SETTING_NAME, self::DEFAULT_REPEATING_CLICKS_SECONDS);
        $this->addDbSetting(self::REPEATING_BANNER_CLICKS, Gpf::NO);
        $this->addDbSetting(self::REPEATING_CLICKS_SETTING_NAME, self::DEFAULT_REPEATING_CLICKS);
        $this->addDbSetting(self::REPEATING_SIGNUPS_ACTION_SETTING_NAME, self::DEFAULT_REPEATING_SIGNUPS_ACTION);
        $this->addDbSetting(self::REPEATING_SIGNUPS_SECONDS_SETTING_NAME, self::DEFAULT_REPEATING_SIGNUPS_SECONDS);
        $this->addDbSetting(self::REPEATING_SIGNUPS_SETTING_NAME, self::DEFAULT_REPEATING_SIGNUPS);
        $this->addDbSetting(self::DUPLICATE_ORDERS_IP_MESSAGE_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_IP_MESSAGE);
        $this->addDbSetting(self::DUPLICATE_ORDER_ID_HOURS_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_ID_HOURS);
        $this->addDbSetting(self::DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_IP_ACTION);
        $this->addDbSetting(self::DUPLICATE_ORDERS_ID_MESSAGE_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_ID_MESSAGE);
        $this->addDbSetting(self::DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_IP_SECONDS);
        $this->addDbSetting(self::DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_ID_ACTION);
        $this->addDbSetting(self::DUPLICATE_ORDERS_ID_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_ID);
        $this->addDbSetting(self::APPLY_TO_EMPTY_ID_SETTING_NAME, self::DEFAULT_APPLY_TO_EMPTY_ORDERS_ID);
        $this->addDbSetting(self::DUPLICATE_ORDERS_IP_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_IP);
        $this->addDbSetting(self::DUPLICATE_ORDERS_IP_SAMECAMPAIGN_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_IP_SAMECAMPAIGN);
        $this->addDbSetting(self::DUPLICATE_ORDERS_IP_SAMEORDERID_SETTING_NAME, self::DEFAULT_DUPLICATE_ORDERS_IP_SAMEORDERID);

        $this->addDbSetting(self::SETTING_LINKING_METHOD, 'P');
        $this->addDbSetting(self::AUTO_DELETE_RAWCLICKS, '0');
        $this->addDbSetting(self::AUTO_DELETE_EXPIRED_VISITORS, Gpf::NO);
        $this->addDbSetting(self::ALLOW_COMPUTE_NEGATIVE_COMMISSION, Gpf::NO);

        $this->addDbSetting(self::SUPPORT_VAT_SETTING_NAME, Gpf::NO);
        $this->addDbSetting(self::VAT_PERCENTAGE_SETTING_NAME, '0');
        $this->addDbSetting(self::VAT_COMPUTATION_SETTING_NAME, 'D');
        $this->addDbSetting(self::PAYOUT_INVOICE_WITH_VAT_SETTING_NAME);

        $this->addDbSetting(self::SIGNUP_BONUS, 0);

        $this->addDbSetting(self::MATRIX_WIDTH, self::MATRIX_WIDTH_DEFAULT_VALUE);
        $this->addDbSetting(self::MATRIX_HEIGHT, self::MATRIX_HEIGHT_DEFAULT_VALUE);
        $this->addDbSetting(self::FULL_FORCED_MATRIX, Gpf::NO);
        $this->addDbSetting(self::MATRIX_SPILLOVER, 'S');
        $this->addDbSetting(self::MATRIX_AFFILIATE, '');
        $this->addDbSetting(self::DEFAULT_MERCHANT_ID, self::DEFAULT_MERCHANT_ID_VALUE);
        $this->addDbSetting(self::MATRIX_EXPAND_WIDTH, self::MATRIX_EXPAND_WIDTH_DEFAULT_VALUE);
        $this->addDbSetting(self::MATRIX_EXPAND_HEIGHT, self::MATRIX_EXPAND_HEIGHT_DEFAULT_VALUE);
        $this->addDbSetting(self::MATRIX_FILL_BONUS, self::MATRIX_FILL_BONUS_DEFAULT_VALUE);  
        $this->addDbSetting(self::MATRIX_OTHER_FILL_BONUS, self::MATRIX_OTHER_FILL_BONUS_DEFAULT_VALUE);         

        $this->addDbSetting(self::NOT_SET_PARENT_AFFILIATE, Gpf::NO);

        $this->addDbSetting(self::BRANDING_KNOWLEDGEBASE_LINK, Pap_Branding::DEFAULT_BRANDING_KNOWLEDGEBASE_LINK);
        $this->addDbSetting(self::BRANDING_POST_AFFILIATE_PRO_HELP_LINK, Pap_Branding::DEFAULT_BRANDING_POST_AFFILIATE_PRO_HELP_LINK);
        $this->addDbSetting(self::BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK, Pap_Branding::DEFAULT_BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK);
        $this->addDbSetting(self::BRANDING_QUALITYUNIT_CHANGELOG_LINK, Pap_Branding::DEFAULT_BRANDING_QUALITYUNIT_CHANGELOG_LINK);
        $this->addDbSetting(self::BRANDING_QUALITYUNIT_PAP, Pap_Branding::DEFAULT_BRANDING_QUALITYUNIT_PAP);
        $this->addDbSetting(self::BRANDING_TEXT_POST_AFFILIATE_PRO, Pap_Branding::DEFAULT_BRANDING_TEXT_POST_AFFILIATE_PRO);
        $this->addDbSetting(self::BRANDING_TUTORIAL_VIDEOS_BASE_LINK, Pap_Branding::DEFAULT_BRANDING_TUTORIAL_VIDEOS_BASE_LINK);
        $this->addDbSetting(self::BRANDING_TUTORIAL_VIDEOS_ENABLED, Gpf::YES);

        $this->addDbSetting(self::GEOIP_CLICKS, Gpf::NO);
        $this->addDbSetting(self::GEOIP_SALES, Gpf::NO);
        $this->addDbSetting(self::GEOIP_AFFILIATES, Gpf::NO);
        $this->addDbSetting(self::GEOIP_CLICKS_BLACKLIST, '');
        $this->addDbSetting(self::GEOIP_SALES_BLACKLIST, '');
        $this->addDbSetting(self::GEOIP_AFFILIATES_BLACKLIST, '');
        $this->addDbSetting(self::GEOIP_CLICKS_BLACKLIST_ACTION, 'D');
        $this->addDbSetting(self::GEOIP_SALES_BLACKLIST_ACTION, 'D');
        $this->addDbSetting(self::GEOIP_AFFILIATES_BLACKLIST_ACTION, 'D');

        $this->addDbSetting(self::BANNEDIPS_CLICKS, Gpf::NO);
        $this->addDbSetting(self::BANNEDIPS_SALES, Gpf::NO);
        $this->addDbSetting(self::BANNEDIPS_SIGNUPS, Gpf::NO);
        $this->addDbSetting(self::BANNEDIPS_CLICKS_ACTION, 'D');
        $this->addDbSetting(self::BANNEDIPS_SALES_ACTION, 'D');
        $this->addDbSetting(self::BANNEDIPS_SIGNUPS_ACTION, 'D');
        $this->addDbSetting(self::BANNEDIPS_LIST_CLICKS, '');
        $this->addDbSetting(self::BANNEDIPS_LIST_SALES, '');
        $this->addDbSetting(self::BANNEDIPS_LIST_SIGNUPS, '');
        $this->addDbSetting(self::BANNEDIPS_SALES_MESSAGE, '');
        $this->addDbSetting(self::LAST_BILLING_DATE, '');

        parent::defineDbSettings();
    }

    private function getDefaultCookieDomainValidity() {
        $host = @$_SERVER['HTTP_HOST'];
        if($host == '' || $host == 'localhost') {
            return '';
        }

        $requiredParts = 3;
        if(strpos($host, '.co.') != false) {
            $requiredParts = 4;
        }

        $pos = strpos($host, 'www.');
        if( $pos !== false && $pos === 0) {
            $host = substr($host, 3);
        }

        $parts = count(explode('.', $host));

        if($parts < $requiredParts) {
            $host = '.'.$host;
        }

        return $host;
    }

    protected function lazyInitDefaultValue($name) {
        
        switch($name) {
            case self::PROGRAM_LOGO:
                $this->addDefaultValue(self::PROGRAM_LOGO, Gpf_Paths::getInstance()->getImageUrl('logo_pap.gif', 'signup'));
                break;
            default:
                try {
                    $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getResourcePath($name .
                    '.' . Gpf_Settings_Define::LARGE_TEXT_SETTING_TEMPLATE_FILE_EXTENSION, Gpf_Settings_Define::LARGE_TEXT_SETTINGS_DIR, 'merchants'));
                } catch (Gpf_ResourceNotFoundException $e) {
                    return;
                }
                $this->addDefaultValue($name, $file->getContents());
        }
    }
}
?>
