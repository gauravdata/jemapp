<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class GetResponseSignup_Config extends Gpf_Plugins_Config {
    const GETRESPONSE_API_KEY = 'getResponseApiKey';
    const GETRESPONSE_CAMPAIGN_NAME = 'getResponseCampaign';
    const CUSTOM_DATA_FIELDS = 'getResponseCustomDataFields';
    const API_URL = 'http://api2.getresponse.com';
    
    protected function initFields() {
        $this->addTextBox($this->_("GetResponse API key"), self::GETRESPONSE_API_KEY, $this->_("Api key can be found after login to GetResponse in menu Account -> Edit settings in section Nr. 6 of this form"));
        $this->addTextBox($this->_("GetResponse campaign name"), self::GETRESPONSE_CAMPAIGN_NAME, $this->_("Campaign name defined in GetResponse account"));
        $this->addTextBox($this->_("Custom data fields"), self::CUSTOM_DATA_FIELDS, $this->_("Comma separated data1 - data25 fields which you want to fill in new getResponse contact."));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $apiKey = $form->getFieldValue(self::GETRESPONSE_API_KEY);
        $campaignName = $form->getFieldValue(self::GETRESPONSE_CAMPAIGN_NAME);
        Gpf_Settings::set(self::GETRESPONSE_API_KEY, $apiKey);
        Gpf_Settings::set(self::GETRESPONSE_CAMPAIGN_NAME, $campaignName);
        Gpf_Settings::set(self::CUSTOM_DATA_FIELDS, $form->getFieldValue(self::CUSTOM_DATA_FIELDS));
        $form->setInfoMessage($this->_('GetResponseSignup plugin configuration saved'));
        return $form;
    }

    /**
     * @anonym
     * @service custom_separator read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(self::GETRESPONSE_API_KEY, Gpf_Settings::get(self::GETRESPONSE_API_KEY));
        $form->addField(self::GETRESPONSE_CAMPAIGN_NAME, Gpf_Settings::get(self::GETRESPONSE_CAMPAIGN_NAME));
        $form->addField(self::CUSTOM_DATA_FIELDS, Gpf_Settings::get(self::CUSTOM_DATA_FIELDS));
        return $form;
    }
}

?>
