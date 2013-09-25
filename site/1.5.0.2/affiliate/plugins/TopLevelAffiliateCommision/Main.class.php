<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Rene
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

class TopLevelAffiliateCommision_Main extends Gpf_Plugins_Handler {
    
    const PLUGIN_NAME = 'TopLevelAffiliateCommision';
    
    /**
     * @return TopLevelAffiliateCommision_Main
     */
    public static function getHandlerInstance() {
        return new TopLevelAffiliateCommision_Main();
    }

    public function beforeSave(Pap_Common_TransactionCompoundContext $transactionCompoundContext){
        $user =  Pap_Affiliates_User::loadFromId($transactionCompoundContext->getTransaction()->getUserId());
        if ($user->getParentUserId() != '') {
            $transactionCompoundContext->setSaveTransaction(false);
        }
    }

    public function modifyCommission(Pap_Common_SaveCommissionCompoundContext $compoundContext) {
        $context = $compoundContext->getContext();
        if (!($context instanceof Pap_Contexts_Action)) {
            $context->debug($this->getPluginName().' plugin not applied as Action/Sale is not saved');
            return;
        }
        $context->debug($this->getPluginName().' plugin begin');
        $user = $compoundContext->getUser();
        $tier = $compoundContext->getTier();
        if ($user->getParentUserId() == '') {
            if (($transaction = $context->getTransaction($tier)) != null) {
                $newCommission = $this->computeCommission($context, $transaction);
                if ($newCommission === null) {
                    $context->debug('Top Level Commission is not applicated');
                    return;
                }
                $transaction->setCommission($newCommission);
                $transaction->save();
            } else {
                $context->debug('Commission does not exist.');
                $commission = $this->getCommission($tier, $context);
                if ($commission === null) {
                    $context->debug('Top Level Commission is not applicated');
                    return;
                }
                $commission->setStatusFromType($context->getCommissionTypeObject());
                $compoundContext->getSaveObject()->saveCommission($context, $user, $commission);
            }
            $context->debug($this->getPluginName().' commision saved to'. $user->getId());
        }
        $context->debug($this->getPluginName().' plugin end');
    }

    protected function getCommission($tier, Pap_Contexts_Action $context) {
        return new Pap_Tracking_Common_Commission($tier, Pap_Db_CommissionType::COMMISSION_FIXED,
                $this->computeCommission($context, $context->getTransaction(1)));
    }
    
    protected function computeCommission(Pap_Contexts_Action $context, Pap_Common_Transaction $transaction){
        $percent = Gpf_Settings::get(TopLevelAffiliateCommision_Config::COMMISSION_KEY);
        return $transaction->getCommission() * $percent / 100;
    }
    
    public function initSettings(Gpf_Settings_Gpf $context) {
        $context->addFileSetting(TopLevelAffiliateCommision_Config::COMMISSION_KEY, 100);
    }
    
    protected function getPluginName() {
        return self::PLUGIN_NAME;
    }
}

?>
