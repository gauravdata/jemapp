<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Features_SplitCommissions_SplitCommissionsForm extends Gpf_View_FormService {
    const FIRST_AFF_BONUS = "firstAffBonus";
    const LAST_AFF_BONUS = "lastAffBonus";
    const MIN_COMMISSION = "minCommission";

    private static $instance = false;

    /**
     * @return Pap_Features_SplitCommissions_SplitCommissionsForm
     */
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Features_SplitCommissions_SplitCommissionsForm();
        }
        return self::$instance;
    }

    protected function createDbRowObject() {
    }

    /**
     * @service split_commissions_form write
     * @param Gpf_Rpc_Form
     */
    public function saveSettings(Gpf_Rpc_Form $form) {
        $firstAffBonus = $this->getCommTypeAttr($form, self::FIRST_AFF_BONUS);
        $lastAffBonus = $this->getCommTypeAttr($form, self::LAST_AFF_BONUS);
        $minCommission = $this->getCommTypeAttr($form, self::MIN_COMMISSION);

        if ($firstAffBonus !== false || $lastAffBonus !== false ) {
            if ($firstAffBonus->getValue() + $lastAffBonus->getValue() < 0 ||
            $firstAffBonus->getValue() + $lastAffBonus->getValue() > 100) {
                $form->setErrorMessage($this->_('Sum of First affiliate bonus and Last affiliate bonus must be in range 0-100'));
                return $form;
            }
            $firstAffBonus->save();
            $lastAffBonus->save();
        }

        if ($minCommission !== false) {
            if ($minCommission->getValue() < 0) {
                $form->setErrorMessage($this->_('Minimum commission must be greater or equals to 0'));
                return $form;
            }
            $minCommission->save();
        }
    }

    /**
     * @service split_commissions_form load
     * @param Gpf_Rpc_Form
     */
    public function loadSettings(Gpf_Rpc_Form $form) {
        $this->loadCommTypeAttr($form, self::FIRST_AFF_BONUS);
        $this->loadCommTypeAttr($form, self::LAST_AFF_BONUS);
        $this->loadCommTypeAttr($form, self::MIN_COMMISSION);
    }

    /**
     * @return Pap_Db_CommissionTypeAttribute
     */
    private function getCommTypeAttr(Gpf_Rpc_Form $form, $attributeName) {
        if (!$form->existsField($attributeName)) {
            return false;
        }
        $commTypeAttr = new Pap_Db_CommissionTypeAttribute();
        $commTypeAttr->setCommissionTypeId($form->getFieldValue("Id"));
        $commTypeAttr->setName($attributeName);

        try {
            $commTypeAttr->loadFromData(array(Pap_Db_Table_CommissionTypeAttributes::COMMISSION_TYPE_ID,
            Pap_Db_Table_CommissionTypeAttributes::NAME));
        } catch (Gpf_Exception $e) {
        }
        $commTypeAttr->setValue($form->getFieldValue($attributeName));
        return $commTypeAttr;
    }

    private function loadCommTypeAttr(Gpf_Rpc_Form $form, $attributeName) {
        $commTypeAttr = new Pap_Db_CommissionTypeAttribute();
        $commTypeAttr->setCommissionTypeId($form->getFieldValue("Id"));
        $commTypeAttr->setName($attributeName);
        try {
            $commTypeAttr->loadFromData(array(Pap_Db_Table_CommissionTypeAttributes::COMMISSION_TYPE_ID,
            Pap_Db_Table_CommissionTypeAttributes::NAME));
            $form->addField($attributeName, $commTypeAttr->getValue());
        } catch (Gpf_Exception $e) {
            $form->addField($attributeName, '0');
        }
    }
}
?>
