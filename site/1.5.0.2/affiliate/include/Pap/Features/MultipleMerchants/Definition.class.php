<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class Pap_Features_MultipleMerchants_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'MultipleMerchants';
        $this->name = $this->_('Multiple merchants');
        $this->description = $this->_('Multiple merchants feature allows you to have more merchants (administrators) who can access %s. All admins will be able to see same campaigns! In case you are looking for affiliate network functionality, where each merchant can administer own campaigns, please visit our home page and look for Post Affiliate Network section.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO), Gpf_Application::getKnowledgeHelpUrl('features-and-functions/multiple-merchants.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.merchant.menu', 'Pap_Features_MultipleMerchants_Main', 'addToMenu');
    }

    public function onDeactivate() {
        if (Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive('AffiliateNetwork')) {
            throw new Gpf_Exception($this->_('Multiple merchants feature can not be dactivated when Affiliate network is active'));
        }
    }
}

?>
