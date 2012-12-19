<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Juraj Simon, Maros Galik
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
class Pap_Tracking_Common_RecognizeAccountId extends Gpf_Object {

    public function recognize(Pap_Contexts_Tracking $context) {
        $context->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID,
        Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_DEFAULT);

        $this->recognizeAccountId($context);
    }

    private function recognizeAccountId(Pap_Contexts_Tracking $context) {
        if (!$this->accountValid($context->getVisit()->getAccountId())) {
            $context->debug('Account from visit with accountId='.$context->getVisit()->getAccountId() .
                            ' is not valid! For now, set default account: '.$context->getAccountId());
            return;
        }
        $context->debug('Set AccountId: '.$context->getVisit()->getAccountId());
        $context->setAccountId($context->getVisit()->getAccountId(),
        Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_FROM_FORCED_PARAMETER);
    }

    protected function accountValid($accountId) {
        try {
            $account = new Pap_Account();
            $account->setId($accountId);
            $account->load();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}

?>
