<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric, Andrej Harsani, Miso Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Settings.class.php 18002 2008-05-13 18:31:39Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * class that implements the loading and saving of settings from/to file or from DB
 * Instance of this class is created in a Gpf_SettingsBase class
 *
 * @package GwtPhpFramework
 */
class Gpf_Settings_Gpf extends Gpf_Settings_Define {
    const PROGRAM_NAME = "programName";
    const DEMO_MODE = "DEMO_MODE";
    const TIMEZONE_NAME = 'TIMEZONE';
    const DEFAULT_TIMEZONE = 'America/Los_Angeles';

    const FTP_HOSTNAME  = 'FTP_HOSTNAME';
    const FTP_DIRECTORY = 'FTP_DIRECTORY';
    const FTP_USERNAME  = 'FTP_USERNAME';
    const FTP_PASSWORD  = 'FTP_PASSWORD';

    const DB_HOSTNAME = 'DB_HOSTNAME';
    const DB_USERNAME = 'DB_USERNAME';
    const DB_PASSWORD = 'DB_PASSWORD';
    const DB_DATABASE = 'DB_DATABASE';

    const BENCHMARK_ACTIVE = 'BENCHMARK_ACTIVE';
    const BENCHMARK_MIN_SQL_TIME = 'BENCHMARK_MIN_SQL_TIME';
    const BENCHMARK_MAX_FILE_SIZE = 'BENCHMARK_MAX_FILE_SIZE';
    const NETWORK_ENABLED = 'NETWORK';    

    const LICENSE = 'LICENSE';
    const VARIATION = 'VARIATION';

    const CRON_RUN_INTERVAL = 'cronRunInterval';
    
    const PROXY_SERVER_SETTING_NAME = 'proxyServer';
    const PROXY_PORT_SETTING_NAME = 'proxyPort';
    const PROXY_USER_SETTING_NAME = 'proxyUser';
    const PROXY_PASSWORD_SETTING_NAME = 'proxyPassword';

    const QUICK_LAUNCH_SETTING_NAME = "quickLaunchSetting";
    const LOG_LEVEL_SETTING_NAME = 'log_level';
    const LAST_RUN_TIME_SETTING = 'cronLastRun';

    const MAX_ALLOWED_SERVER_LOAD = 'maxAllowedServerLoad';
    const SERVER_OVERLOAD_INTERRUPTIONS = 'serverOverloadInterruptions';

    const NOT_FORCE_EMAIL_USERNAMES = "notForceEmailUsernames";
    const AUTO_DELETE_EVENTS = "deleteeventdays";
    const AUTO_DELETE_EVENTS_RECORDS_NUM = "deleteeventrecords";
    const AUTO_DELETE_LOGINSHISTORY = "deleteloginshistorydays";

    const BRANDING_TEXT_BASE_LINK = "base_link";
    const BRANDING_FAVICON = "favicon";
    const BRANDING_QUALITYUNIT_ADDONS_LINK = "qualityunit_addons_link";
    const BRANDING_QUALITYUNIT_COMPANY_LINK = "qualityunit_company_link";
    const BRANDING_QUALITYUNIT_PRIVACY_POLICY_LINK = "qualityunit_privacy_policy_link";
    const BRANDING_QUALITYUNIT_CONTACT_US_LINK = "qualityunit_contact_us_link";
    const BRANDING_QUALITY_UNIT = "quality_unit";
    const BRANDING_QUALITYUNIT_SUPPORT_EMAIL = "qualityunit_support_email";
    const DEFAULT_COUNTRY = "defaultCountry";

    const PASSWORD_MIN_LENGTH = "password_min_len";
    const PASSWORD_MAX_LENGTH = "password_max_len";
    const PASSWORD_LETTERS = "password_letters";
    const PASSWORD_DIGITS = "password_digits";
    const PASSWORD_SPECIAL = "password_special";

    const SERVER_NAME = 'serverName';
    const SERVER_NAME_RESOLVE_FROM = 'serverNameResolveFrom';
    const BASE_SERVER_URL = 'baseServerUrl';

    const REGIONAL_SETTINGS_IS_DEFAULT = 'regional_settings_is_default';
    const REGIONAL_SETTINGS_DATE_FORMAT = 'dateformat';
    const REGIONAL_SETTINGS_TIME_FORMAT = 'timeformat';
    const REGIONAL_SETTINGS_THOUSANDS_SEPARATOR = 'thousandsseparator';
    const REGIONAL_SETTINGS_DECIMAL_SEPARATOR = 'decimalseparator';

    const DEFAULT_THOUANDS_SEPARATOR = ' ';
    const DEFAULT_DECIMAL_SEPARATOR = '.';
    const DEFAULT_DATE_FORMAT = 'MM/d/yyyy';
    const DEFAULT_TIME_FORMAT = 'HH:mm:ss';

    protected function defineFileSettings() {
        $this->addFileSetting(self::DB_DATABASE);
        $this->addFileSetting(self::DB_HOSTNAME);
        $this->addFileSetting(self::DB_USERNAME);
        $this->addFileSetting(self::DB_PASSWORD);

        $this->addFileSetting(self::FTP_HOSTNAME,  'localhost');
        $this->addFileSetting(self::FTP_DIRECTORY, '/affiliate');
        $this->addFileSetting(self::FTP_USERNAME,  '');
        $this->addFileSetting(self::FTP_PASSWORD,  '');

        $this->addFileSetting(self::LAST_RUN_TIME_SETTING, '');

        $this->addFileSetting(self::MAX_ALLOWED_SERVER_LOAD, 0);
        $this->addFileSetting(self::SERVER_OVERLOAD_INTERRUPTIONS, 0);

        $this->addFileSetting(self::DEMO_MODE, Gpf::NO);
        $this->addFileSetting(self::TIMEZONE_NAME, self::DEFAULT_TIMEZONE);
        $this->addFileSetting(self::NETWORK_ENABLED, Gpf::YES);

        $this->addFileSetting(self::BENCHMARK_ACTIVE, Gpf::NO);
        $this->addFileSetting(self::BENCHMARK_MIN_SQL_TIME, 0);
        $this->addFileSetting(self::BENCHMARK_MAX_FILE_SIZE, 5);
        $this->addFileSetting(self::LICENSE, '');

        $this->addFileSetting(self::SERVER_NAME);
        $this->addFileSetting(self::SERVER_NAME_RESOLVE_FROM, 'SERVER_NAME');
        $this->addFileSetting(self::BASE_SERVER_URL);
    }

    protected function defineDbSettings() {
        $this->addDbSetting(self::VARIATION, '');

        $this->addDbSetting(self::PROXY_SERVER_SETTING_NAME, '');
        $this->addDbSetting(self::PROXY_PORT_SETTING_NAME, '');
        $this->addDbSetting(self::PROXY_USER_SETTING_NAME, '');
        $this->addDbSetting(self::PROXY_PASSWORD_SETTING_NAME, '');

        $this->addDbSetting(self::QUICK_LAUNCH_SETTING_NAME, "showDesktop");
        $this->addDbSetting(self::LOG_LEVEL_SETTING_NAME, 50); // Gpf_Log::CRITICAL - hardcoded because of optimalization

        $this->addDbSetting(self::NOT_FORCE_EMAIL_USERNAMES, Gpf::NO);
        $this->addDbSetting(self::AUTO_DELETE_EVENTS, 7);
        $this->addDbSetting(self::AUTO_DELETE_EVENTS_RECORDS_NUM, 500000);

        $this->addDbSetting(self::AUTO_DELETE_LOGINSHISTORY, 30);

        $this->addDbSetting(self::DEFAULT_COUNTRY, 'US');
        
        $this->addDbSetting(self::CRON_RUN_INTERVAL, '5');

        $this->addDbSetting(self::BRANDING_TEXT_BASE_LINK, 'http://www.qualityunit.com');
        $this->addDbSetting(self::BRANDING_FAVICON, '');
        $this->addDbSetting(self::BRANDING_QUALITYUNIT_ADDONS_LINK, 'http://addons.qualityunit.com');
        $this->addDbSetting(self::BRANDING_QUALITYUNIT_COMPANY_LINK,'http://www.qualityunit.com/company/');
        $this->addDbSetting(self::BRANDING_QUALITYUNIT_PRIVACY_POLICY_LINK,'http://www.qualityunit.com/company/privacy-policy-quality-unit');
        $this->addDbSetting(self::BRANDING_QUALITYUNIT_CONTACT_US_LINK,'http://www.qualityunit.com/company/contact-us');
        $this->addDbSetting(self::BRANDING_QUALITY_UNIT,'Quality Unit');
        $this->addDbSetting(self::BRANDING_QUALITYUNIT_SUPPORT_EMAIL, 'support@qualityunit.com');


        $this->addDbSetting(self::PASSWORD_MIN_LENGTH, 1);
        $this->addDbSetting(self::PASSWORD_MAX_LENGTH, 60);
        $this->addDbSetting(self::PASSWORD_LETTERS, 'N');
        $this->addDbSetting(self::PASSWORD_DIGITS, 'N');
        $this->addDbSetting(self::PASSWORD_SPECIAL, 'N');

        $this->addDbSetting(self::REGIONAL_SETTINGS_IS_DEFAULT, Gpf::YES);
        $this->addDbSetting(self::REGIONAL_SETTINGS_DATE_FORMAT, self::DEFAULT_DATE_FORMAT);
        $this->addDbSetting(self::REGIONAL_SETTINGS_TIME_FORMAT, self::DEFAULT_TIME_FORMAT);
        $this->addDbSetting(self::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR, self::DEFAULT_THOUANDS_SEPARATOR);
        $this->addDbSetting(self::REGIONAL_SETTINGS_DECIMAL_SEPARATOR, self::DEFAULT_DECIMAL_SEPARATOR);

        Gpf_Plugins_Engine::extensionPoint('Core.defineSettings', $this);
    }
}
?>
