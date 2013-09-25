<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Juraj Simon
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
class Bluepay_Config extends Gpf_Plugins_Config {
    const CUSTOM_SEPARATOR = 'BluepayCustomSeparator';
    const HTML_COOKIE_VARIABLE = 'BluepayHtmlCookieVariable';
    
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom value separator"), self::CUSTOM_SEPARATOR, $this->_("Custom value separator must be set."));        
        $this->addListBox($this->_('Variable'), self::HTML_COOKIE_VARIABLE, array('ORDER_ID'=>'ORDER_ID','INVOICE_ID'=>'INVOICE_ID'), $this->_('Enter the variable used for storing PAP VisitorID.'));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::CUSTOM_SEPARATOR, $form->getFieldValue(self::CUSTOM_SEPARATOR));
        Gpf_Settings::set(self::HTML_COOKIE_VARIABLE, $form->getFieldValue(self::HTML_COOKIE_VARIABLE));
        $form->setInfoMessage($this->_('Configuration saved'));
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
        $form->addField(self::CUSTOM_SEPARATOR, Gpf_Settings::get(self::CUSTOM_SEPARATOR));
        $form->addField(self::HTML_COOKIE_VARIABLE, Gpf_Settings::get(self::HTML_COOKIE_VARIABLE));
        return $form;
    }
}

?>
