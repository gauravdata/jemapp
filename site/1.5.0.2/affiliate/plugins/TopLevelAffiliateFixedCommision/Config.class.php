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
class TopLevelAffiliateFixedCommision_Config extends Gpf_Object {

    const COMMISSION = 'TopAffiliateFixedCommission';
    const UNDEFINED_PERCENTAGE = -1;

    /**
     * @return TopLevelAffiliateFixedCommision_Config
     */
    public static function getHandlerInstance() {
        return new TopLevelAffiliateFixedCommision_Config();
    }

    public function initFieldsInForm(Pap_Merchants_Campaign_CommissionTypeEditAdditionalForm $additionalDetails) {
        $additionalDetails->addTextBoxWithDefault($this->_("Commission Percentage for Top Affiliate "), 
            self::COMMISSION, self::UNDEFINED_PERCENTAGE, $this->_("undefined"),
        $this->_('Top affiliate commission fixed percentage. Undefined: ').self::UNDEFINED_PERCENTAGE);
    }

    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Form $form
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Form $form) {
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::COMMISSION);
        $form->addValidator(new Gpf_Rpc_Form_Validator_NumberRangeValidator(self::UNDEFINED_PERCENTAGE, 100), self::COMMISSION);
        $form->validate();
        if($form->isSuccessful()){
            $commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
            $commTypeAttr->setCommissionTypeAttributeValue($form->getFieldValue('Id'), self::COMMISSION,
            $form->getFieldValue(self::COMMISSION));
            $form->setInfoMessage($this->_('Plugin configuration saved'));
        } else {
            $form->setErrorMessage($this->_('Commission Percentage must be from interval 0-100. Or '.self::UNDEFINED_PERCENTAGE.' as Undefined.'));
        }
        return $form;
    }

    /**
     * @anonym
     * @service custom_separator read
     * @param Gpf_Rpc_Form $form
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Form $form) {
        $commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
        try {
            $value = $commTypeAttr->getCommissionTypeAttribute($form->getFieldValue('Id'),
            self::COMMISSION)->getValue();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $value = self::UNDEFINED_PERCENTAGE;
        }

        $form->setField(self::COMMISSION, $value);
        return $form;
    }
}

?>
