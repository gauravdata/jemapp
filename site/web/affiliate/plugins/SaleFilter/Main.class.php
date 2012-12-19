<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
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
 * @package PostAffiliate
 */
class SaleFilter_Main extends Gpf_Plugins_Handler {

    /**
     * @return SaleFilter_Main
     */
    public static function getHandlerInstance() {
        return new SaleFilter_Main();
    }

    public function initFields(Pap_Merchants_Campaign_CampaignDetailsAdditionalForm $additionalDetails) {
        $additionalDetails->addTextBoxWithDefault($this->_('Minimum total cost'), SaleFilter_Definition::NAME_MINIMUM_TOTALCOST,
        0, $this->_('Undefined'), $this->_("Commission will be zero for sales that don't reach minimum total cost value"));
        $additionalDetails->addTextBoxWithDefault($this->_('Maximum total cost'), SaleFilter_Definition::NAME_MAXIMUM_TOTALCOST,
        -1, $this->_('Undefined'), $this->_("Commission will be zero for sales that exceed maximum total cost value"));
    }

    public function save(Gpf_Rpc_Form $form) {
        $attribute = $this->createCampaignAttribute();
        $attribute->setName(SaleFilter_Definition::NAME_MINIMUM_TOTALCOST);
        $attribute->setCampaignId($form->getFieldValue('Id'));
        $attribute->setValue($form->getFieldValue(SaleFilter_Definition::NAME_MINIMUM_TOTALCOST));
        $attribute->save();
        
        $attribute = $this->createCampaignAttribute();
        $attribute->setName(SaleFilter_Definition::NAME_MAXIMUM_TOTALCOST);
        $attribute->setCampaignId($form->getFieldValue('Id'));
        $attribute->setValue($form->getFieldValue(SaleFilter_Definition::NAME_MAXIMUM_TOTALCOST));
        $attribute->save();
    }

    public function load(Gpf_Rpc_Form $form) {
        try {
            $form->setField(SaleFilter_Definition::NAME_MINIMUM_TOTALCOST,
            $this->createCampaignAttribute()->getSetting(SaleFilter_Definition::NAME_MINIMUM_TOTALCOST, $form->getFieldValue('Id')));
            $form->setField(SaleFilter_Definition::NAME_MAXIMUM_TOTALCOST,
            $this->createCampaignAttribute()->getSetting(SaleFilter_Definition::NAME_MAXIMUM_TOTALCOST, $form->getFieldValue('Id')));
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
    }

    public function updateCommission(Pap_Common_Transaction $transaction) {
    	if ($transaction->getType() != Pap_Db_Transaction::TYPE_SALE) {
    	    return;
    	}
        try {
            $minTotalCost = $this->createCampaignAttribute()->getSetting(SaleFilter_Definition::NAME_MINIMUM_TOTALCOST, $transaction->getCampaignId());
            if ($transaction->getTotalCost() < $minTotalCost) {
                $transaction->setCommission(0);
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
        try {
            $maxTotalCost = $this->createCampaignAttribute()->getSetting(SaleFilter_Definition::NAME_MAXIMUM_TOTALCOST, $transaction->getCampaignId());
            if ($maxTotalCost > 0 && $transaction->getTotalCost() > $maxTotalCost) {
                $transaction->setCommission(0);
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
    }

    /**
     * @return Pap_Db_CampaignAttribute
     */
    private function createCampaignAttribute() {
        return new Pap_Db_CampaignAttribute();
    }
}
?>
