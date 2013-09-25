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
class TopLevelAffiliateCommision_Config extends Gpf_Plugins_Config {
    const COMMISSION_KEY = 'TopAffiliateCommission';

    protected function initFields() {
        $this->addTextBox($this->_("Commission Percent"), self::COMMISSION_KEY, $this->_('Top affiliate commission percentage'));
    }

    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::COMMISSION_KEY);
        $form->addValidator(new Gpf_Rpc_Form_Validator_NumberRangeValidator(0, 100), self::COMMISSION_KEY);
        $form->validate();
        if($form->isSuccessful()){
            Gpf_Settings::set(self::COMMISSION_KEY, $form->getFieldValue(self::COMMISSION_KEY));
            $form->setInfoMessage($this->_('Plugin configuration saved'));
        }
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
        $form->addField(self::COMMISSION_KEY, Gpf_Settings::get(self::COMMISSION_KEY));
        return $form;
    }
}

?>
