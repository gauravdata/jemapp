<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class SaleFraudProtection_Config extends Gpf_Plugins_Config {
    const SECRET_KEY = 'SaleFraudProtectionSecretKey';
    const PARAM_NAME = 'SaleFraudProtectionParamName';
    
    protected function initFields() {
        $this->addTextBox($this->_('Secret key'), self::SECRET_KEY,
        $this->_("Secret key which will be used for computing checksum."));
        $this->addListBox($this->_('Param name'), self::PARAM_NAME,array(1=>'data1',2=>'data2',3=>'data3',4=>'data4',5=>'data5'),
        $this->_("Param name in which checksum will be posted."));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::SECRET_KEY, $form->getFieldValue(self::SECRET_KEY));
        Gpf_Settings::set(self::PARAM_NAME, $form->getFieldValue(self::PARAM_NAME));
        $form->setInfoMessage($this->_('Sale Tracking Fraud Protection plugin configuration saved'));
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
        $form->addField(self::SECRET_KEY, Gpf_Settings::get(self::SECRET_KEY));
        $form->addField(self::PARAM_NAME, Gpf_Settings::get(self::PARAM_NAME));
        return $form;
    }
}

?>
