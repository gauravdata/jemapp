<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
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
class SubaffiliateFirstSaleBonus_Config extends Gpf_Plugins_Config {
    
    const VALIDITY_DAYS = 'SubaffiliateFirstSaleBonusValidityDays';
    const BONUS_TYPE = 'SubaffiliateFirstSaleBonusBonusType';
    const BONUS_AMOUNT = 'SubaffiliateFirstSaleBonusBonusAmount';
    
    protected function initFields() {
        $this->addTextBox($this->_("Days of validity"), self::VALIDITY_DAYS, $this->_("Number of days that defines validity of this plugin action (in days)"));
        $this->addListBox($this->_('Bonus type'), self::BONUS_TYPE, array('$'=>'$','%'=>'%'));
        $this->addTextBox($this->_("Bonus amount"), self::BONUS_AMOUNT);
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::VALIDITY_DAYS, $form->getFieldValue(self::VALIDITY_DAYS));
        Gpf_Settings::set(self::BONUS_TYPE, $form->getFieldValue(self::BONUS_TYPE));
        Gpf_Settings::set(self::BONUS_AMOUNT, $form->getFieldValue(self::BONUS_AMOUNT));
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
        $form->addField(self::VALIDITY_DAYS, Gpf_Settings::get(self::VALIDITY_DAYS));
        $form->addField(self::BONUS_TYPE, Gpf_Settings::get(self::BONUS_TYPE));
        $form->addField(self::BONUS_AMOUNT, Gpf_Settings::get(self::BONUS_AMOUNT));
        return $form;
    }
}

?>
