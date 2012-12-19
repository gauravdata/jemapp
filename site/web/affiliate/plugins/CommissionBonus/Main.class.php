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

class CommissionBonus_Main extends Gpf_Plugins_Handler {

    const MIN_SALE_VALUE_FOR_BONUS = 'CommissionBonusMinSaleValueForBonus';
    const MIN_SALE_VALUE_FOR_BONUS_UNDEFINED = -1;
    const BONUS_VALUE = 'CommissionBonusBonusValue';

    private static $instance = false;
     
    /**
     * @return CommissionBonus_Main
     */
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new CommissionBonus_Main();
        }
        return self::$instance;
    }

    public function initFields(Pap_Merchants_Campaign_CommissionTypeEditAdditionalForm $additionalDetails) {
        $additionalDetails->addTextBoxWithDefault($this->_('Bonus for sales greater than'),
        self::MIN_SALE_VALUE_FOR_BONUS,
            '-1', $this->_("undefined"),
        $this->_('Minimum sale value for which bonus commission will be added. Undefined: -1. For all sales: 0.'),
        true);
        $additionalDetails->addTextBox($this->_('Bonus commission value'),
        self::BONUS_VALUE,
        $this->_('Bonus commission value in $.'));
    }

    public function save(Gpf_Rpc_Form $form) {
        $minSaleValueForBonus = $form->getFieldValue(self::MIN_SALE_VALUE_FOR_BONUS);
        $bonusValue = $form->getFieldValue(self::BONUS_VALUE);
        
        if (is_float($minSaleValueForBonus)) {
            $form->setErrorMessage($this->_('Wrong format used for Bonus for sales'));
            return;
        }

        if ($minSaleValueForBonus < 0 && $minSaleValueForBonus != -1) {
            $form->setErrorMessage($this->_('Bonus for sales value must be greater than 0 or must equals to -1'));
            return;
        }

        if (is_float($bonusValue)) {
            $form->setErrorMessage($this->_('Wrong format used for Bonus commission value'));
            return;
        }

        if ($bonusValue < 0) {
            $form->setErrorMessage($this->_('Bonus commission must be greater or equal to 0'));
            return;
        }

        $commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
        $commTypeAttr->setCommissionTypeAttributeValue($form->getFieldValue('Id'), self::MIN_SALE_VALUE_FOR_BONUS,
        $minSaleValueForBonus);
        $commTypeAttr->setCommissionTypeAttributeValue($form->getFieldValue('Id'), self::BONUS_VALUE,
        $bonusValue);
    }

    public function load(Gpf_Rpc_Form $form) {
        $form->setField(self::MIN_SALE_VALUE_FOR_BONUS, $this->getMinSaleValueForBonus($form->getFieldValue('Id')));
        $form->setField(self::BONUS_VALUE, $this->getBonusValue($form->getFieldValue('Id')));
    }

    private function getMinSaleValueForBonus($commTypeId) {
        $commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
        try {
            return $commTypeAttr->getCommissionTypeAttribute($commTypeId,
            self::MIN_SALE_VALUE_FOR_BONUS)->getValue();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return self::MIN_SALE_VALUE_FOR_BONUS_UNDEFINED;
        }
    }

    private function getBonusValue($commTypeId) {
        $commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
        try {
            return $commTypeAttr->getCommissionTypeAttribute($commTypeId,
            self::BONUS_VALUE)->getValue();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return 0;
        }
    }

    public function saveCommission(Pap_Common_TransactionCompoundContext $transactionCompoundContext) {
        $context = $transactionCompoundContext->getContext();
        $transaction = $transactionCompoundContext->getTransaction();
        
        $context->debug('BonusCommissions started');
        
        if ($transaction->getTier() != 1) {
            $context->debug('BonusCommissions - transaction is not tier 1. STOPPED');
            return;
        }
        
        $minCommissionSaleValue = $this->getMinSaleValueForBonus($transaction->getCommissionTypeId());
        if ($minCommissionSaleValue == -1) {
            $context->debug('BonusCommissions - minimum Commission Value = -1. STOPPED');
            return;
        }
        
        $bonusValue = $this->getBonusValue($transaction->getCommissionTypeId());
        if ($bonusValue == 0) {
            $context->debug('BonusCommissions - bonus value = 0. STOPPED');
            return;
        }
        
        if ($transaction->getTotalCost() >= $minCommissionSaleValue) {
            $transaction->setCommission($transaction->getCommission() + $bonusValue);
        }
        $context->debug('BonusCommissions - commission added. Ended');
    }
}

?>
