<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author M
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
class TrialPay_Config extends Gpf_Plugins_Config {
    const SEPARATOR = 'TrialPaySeparator';
    
    protected function initFields() {
        $this->addTextBox($this->_("Separator:"), self::SEPARATOR, $this->_("Separator that can separate your value from %s cookie value in SubId field. Use this only in case you use SubId for your application", Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_PAP)));        
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::SEPARATOR, $form->getFieldValue(self::SEPARATOR));
        $form->setInfoMessage($this->_('TrialPay plugin configuration saved'));
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
        $form->addField(self::SEPARATOR, Gpf_Settings::get(self::SEPARATOR));
        return $form;
    }
}

?>
