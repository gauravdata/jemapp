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
class RefidLength_Config extends Gpf_Plugins_Config {
    const MIN_LENGTH = 'minLength';
    const MAX_LENGTH = 'maxLength';
    
    protected function initFields() {
        $this->addTextBoxWithDefault($this->_("Minimum Referal ID length"), self::MIN_LENGTH, '0', $this->_('undefined'), $this->_("Minimum length must be between 0 and 20"));
        $this->addTextBoxWithDefault($this->_("Maximum Referal ID length"), self::MAX_LENGTH, '0', $this->_('undefined'), $this->_("Maximum length must be between 0 and 20"));       
    }
    
    /**
     * @anonym
     * @service refidlength write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(RefidLength_Main::SETTING_REFID_MIN_LENGTH, $form->getFieldValue(self::MIN_LENGTH));
        Gpf_Settings::set(RefidLength_Main::SETTING_REFID_MAX_LENGTH, $form->getFieldValue(self::MAX_LENGTH));
        $form->setInfoMessage($this->_('Referal ID length limit saved'));
        return $form;
    }

    /**
     * @anonym
     * @service refidlength read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(self::MIN_LENGTH, Gpf_Settings::get(RefidLength_Main::SETTING_REFID_MIN_LENGTH));
        $form->addField(self::MAX_LENGTH, Gpf_Settings::get(RefidLength_Main::SETTING_REFID_MAX_LENGTH));
        return $form;
    }
}

?>
