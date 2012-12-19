<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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
class CyberSource_Config extends Gpf_Plugins_Config {
    const CUSTOM_FIELD_NUMBER = 'CybersourceCustomFieldNumber';
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom field number"), self::CUSTOM_FIELD_NUMBER, $this->_("Custom field number sets number of the custom field which will be used for storing tracking data. "));        
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $customFieldNumber = (integer)$form->getFieldValue(self::CUSTOM_FIELD_NUMBER);
        if ($customFieldNumber < 1 || $customFieldNumber > 4) {
            $form->setFieldError(self::CUSTOM_FIELD_NUMBER, $this->_('Custom field number must be from range 1-4.'));
            return $form;
        }
        Gpf_Settings::set(self::CUSTOM_FIELD_NUMBER, $customFieldNumber);
        $form->setInfoMessage($this->_('CyberSource plugin configuration saved'));
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
        $form->addField(self::CUSTOM_FIELD_NUMBER, Gpf_Settings::get(self::CUSTOM_FIELD_NUMBER));
        return $form;
    }
}

?>
