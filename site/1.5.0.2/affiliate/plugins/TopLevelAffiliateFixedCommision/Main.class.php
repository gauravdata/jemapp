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

class TopLevelAffiliateFixedCommision_Main extends TopLevelAffiliateCommision_Main {

    const PLUGIN_NAME = 'TopLevelAffiliateFixedCommision';

    /**
     * @return TopLevelAffiliateFixedCommision_Main
     */
    public static function getHandlerInstance() {
        return new TopLevelAffiliateFixedCommision_Main();
    }

    protected function getCommission($tier, Pap_Contexts_Action $context) {
        $commissionPercentage = $this->getFixedCommissionPercentage($context->getTransaction()->getCommissionTypeId());
        if ($commissionPercentage == TopLevelAffiliateFixedCommision_Config::UNDEFINED_PERCENTAGE) {
            $context->debug('TopLevelAffiliateFixedCommision undefined for this commission group.');
            return null;
        }
        
        $context->debug('TopLevelAffiliateFixedCommision defined '.$commissionPercentage.' for this commission group.');
        return new Pap_Tracking_Common_Commission($tier, Pap_Db_CommissionType::COMMISSION_PERCENTAGE,
        $commissionPercentage);
    }

    protected function computeCommission(Pap_Contexts_Action $context, Pap_Common_Transaction $transaction) {
        $commissionPercentage = $this->getFixedCommissionPercentage($transaction->getCommissionTypeId());
        if ($commissionPercentage == TopLevelAffiliateFixedCommision_Config::UNDEFINED_PERCENTAGE) {
            return null; 
        }
        return ($context->getRealTotalCost()-$context->getFixedCost()) / 100 * $commissionPercentage;
    }

    protected function getPluginName() {
        return self::PLUGIN_NAME;
    }
    
    private function getFixedCommissionPercentage($commTypeId) {
        $commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
        try {
            return $commTypeAttr->getCommissionTypeAttribute($commTypeId,
            TopLevelAffiliateFixedCommision_Config::COMMISSION)->getValue();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return TopLevelAffiliateFixedCommision_Config::UNDEFINED_PERCENTAGE;
        }
    }
}

?>
