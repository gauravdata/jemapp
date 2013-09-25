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
class RoboKassa_Config extends Gpf_Plugins_Config {
    const SECURE_PASS2 = 'RoboKassaSecurePass2';
    
    protected function initFields() {
        $this->addTextBox($this->_("secure pass2"), self::SECURE_PASS2, $this->_("Secure Pass 2 from RoboKassa is needed for verification with RoboKassa."));        
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::SECURE_PASS2, $form->getFieldValue(self::SECURE_PASS2));
        $form->setInfoMessage($this->_('RoboKassa plugin configuration saved'));
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
        $form->addField(self::SECURE_PASS2, Gpf_Settings::get(self::SECURE_PASS2));
        return $form;
    }
}

?>
