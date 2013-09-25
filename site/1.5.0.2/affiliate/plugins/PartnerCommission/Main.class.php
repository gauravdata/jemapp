<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
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

class PartnerCommission_Main extends Gpf_Plugins_Handler {

    /**
     * @return PartnerCommission_Main
     */
    public static function getHandlerInstance() {
        return new PartnerCommission_Main();
    }

    private function sumOfCommissions(Pap_Contexts_Tracking $context, $tier) {
        $sumCommissions = 0;
        for ($count=$tier - 1; $count>=1; $count--) {
            $commission = $context->getTransaction($count);
            if ($commission != null) {
                $sumCommissions += $commission->getCommission();
            }
        }
        return $sumCommissions;
    }

    public function modifyCommission(Pap_Common_SaveCommissionCompoundContext $compoundContext) {
        $context = $compoundContext->getContext();
        if (!($context instanceof Pap_Contexts_Action)) {
            $context->debug('PartnerCommission plugin not applied as Action/Sale is not saved');
            return;
        }
        $context->debug('PartnerCommission plugin begin');
        $user = $compoundContext->getUser();
        $tier = $compoundContext->getTier();
        $saveAllCommissions = $compoundContext->getSaveObject();
        if ($user->getParentUserId() == '') {
            $firstTransaction = $context->getTransactionObject(1);
            $transaction = $context->getTransactionObject($tier);
            if ($transaction != null) {
                $transaction->setCommission($transaction->getTotalCost() - $transaction->getFixedCost() - $this->sumOfCommissions($context, $tier));
                $context->debug('  PartnerCommission changed commission to: ' . $transaction->getCommission());
                $transaction->save();
            } else {
                $context->debug('  Commission does not exist.');
                $commission = new Pap_Tracking_Common_Commission($tier, Pap_Db_CommissionType::COMMISSION_FIXED, $firstTransaction->getTotalCost() - $firstTransaction->getFixedCost() - $this->sumOfCommissions($context, $tier));
                $commission->setStatusFromType($context->getCommissionTypeObject());
                $saveAllCommissions->saveCommission($context, $user, $commission);
            }
        }
        $context->debug('PartnerCommission plugin end');
    }
}

?>
