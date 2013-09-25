<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: AuthUser.class.php 18081 2008-05-16 12:17:32Z mfric $
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
class Pap_Application extends Gpf_Application {
    const ROLETYPE_MERCHANT = 'M';
    const ROLETYPE_AFFILIATE = 'A';

    const DEFAULT_ROLE_MERCHANT = 'pap_merc';
    const DEFAULT_ROLE_AFFILIATE = 'pap_aff';

    public function getAuthClass() {
        return 'Pap_AuthUser';
    }

    public function getDefaultLanguage() {
        return Pap_Branding::DEFAULT_LANGUAGE_CODE;
    }


    public function initDatabase() {
        $role = new Gpf_Db_Role();
        $role->setId(self::DEFAULT_ROLE_MERCHANT);
        $role->setName('Merchant');
        $role->setRoleType(self::ROLETYPE_MERCHANT);
        $role->insert();

        $role = new Gpf_Db_Role();
        $role->setId(self::DEFAULT_ROLE_AFFILIATE);
        $role->setName('Affiliate');
        $role->setRoleType(self::ROLETYPE_AFFILIATE);
        $role->insert();
    }

    public function registerRolePrivileges() {
        $this->addRolePrivileges(self::DEFAULT_ROLE_MERCHANT, 'Pap_Privileges_Merchant');
        $this->addRolePrivileges(self::DEFAULT_ROLE_AFFILIATE, 'Pap_Privileges_Affiliate');
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Application.registerRolePrivileges', $this);
    }

    public function createSettings($onlyFile = false) {
        return new Pap_Settings($onlyFile);
    }

	protected function addSmartyPluginsDir() {
		parent::addSmartyPluginsDir();
    	Gpf_Paths::getInstance()->addSmartyPluginPath(Gpf_Paths::getInstance()->getTopPath() . 'include/Pap/SmartyPlugins');
    }

    public function getVersion() {
        return '4.5.74.7';
    }

    public function getHelpUrl() {
    	if ($this->isInstalled()) {
    		return Gpf_Settings::get(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK);
    	}
        return parent::getHelpUrl();
    }

    protected function computeLatestInstalledApplicationVersion() {
        return Gpf_Db_Table_Versions::getInstance()->getLatestVersion(array($this->getCode(), 'paplite'));
    }

    public function getCode() {
        return 'pap';
    }

    public function getName() {
        return Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO);
    }

    /**
     * @return Pap_Account
     */
    public function createAccount() {
        return new Pap_Account();
    }

    public function getApiFileName() {
    	return 'PapApi.class.php';
    }

    /**
     * @return Gpf_Plugins_Definition
     */
    public function getApplicationPluginsDefinition() {
        $plugins = parent::getApplicationPluginsDefinition();
        $plugins[] = new Pap_Definition();
        return $plugins;
    }

    public function getFeaturePathsDefinition() {
        return array_merge(parent::getFeaturePathsDefinition(),
                           array(Gpf_Paths::getInstance()->getTopPath().'include/Pap/Features/'));
    }

    protected function initLogger() {
    	try {
        	Gpf_Log::addLogger(Gpf_Log_LoggerDatabase::TYPE, Pap_Logger::getLogLevel());
        } catch (Gpf_Exception $e) {
        }
    }
}
?>
