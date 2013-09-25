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

class Pap_Features_PayoutFieldsEncryption_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'PayoutFieldsEncryption';
        $this->name = $this->_('Payout fields encryption');
        $this->description = $this->_('This features encrypts all payout field values that are stored to database. You have to configure password and initialization vector after feature activation. %s<br/>', '<a href="'.Gpf_Application::getKnowledgeHelpUrl('features-and-functions/payout-fields-encryption.html').'" target="_blank">'.$this->_('More help in our Knowledge Base').'</a>');
        $this->version = '1.0.0';
        $this->configurationClassName = 'Pap_Features_PayoutFieldsEncryption_Config';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addRequirement('PapCore', '4.1.12.0');
        
        $this->addImplementation('Core.defineSettings', 'Pap_Features_PayoutFieldsEncryption_Main', 'initSettings');
        
        $this->addImplementation('PostAffiliate.PayoutOption.getValue', 'Pap_Features_PayoutFieldsEncryption_Main', 'decodeValue');
        $this->addImplementation('PostAffiliate.PayoutOption.setValue', 'Pap_Features_PayoutFieldsEncryption_Main', 'encodeValue');
    }
        
    public function onDeactivate() {
        Pap_Features_PayoutFieldsEncryption_Main::getHandlerInstance()->recodeAllValues('', '');
        $this->clearKeys();
    }
    
    private function clearKeys() {
        Gpf_Settings::set(Pap_Features_PayoutFieldsEncryption_Config::ENCRYPT_KEY, '');
        Gpf_Settings::set(Pap_Features_PayoutFieldsEncryption_Config::ENCRYPT_IV, '');
    }
}
?>
