<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
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
class Pap_Tracking_Common_SaveAllCommissions extends Gpf_Object {

    /*
     * @var Pap_Common_UserTree
     */
    protected $userTree;

    public function __construct() {
        $this->userTree = new Pap_Common_UserTree();
    }

    public function save(Pap_Contexts_Tracking $context) {
		$context->debug('Saving commissions started');

		$tier = 1;
		$currentUser = $context->getUserObject();
		$currentCommission = $context->getCommission($tier);

		while($currentUser != null && $tier < 100) {
			if ($currentCommission != null) {
				if ($currentUser->getStatus() != null && $currentUser->getStatus() != 'D') {
					$this->saveCommission($context, $currentUser, $currentCommission);
				} else {
					$context->debug('Commission is not saved, because user is declined');
                }
			} else {
			    $context->debug('Commission is not saved, because it is not defined in campaign or user has no multi tier commission');
			}
		    Gpf_Plugins_Engine::extensionPoint('Tracker.saveAllCommissions', new Pap_Common_SaveCommissionCompoundContext($context, $tier, $currentUser, $this ));

			$tier++;
			$currentUser = $this->userTree->getParent($currentUser);
			$currentCommission = $context->getCommission($tier);
		}

		Gpf_Plugins_Engine::extensionPoint('Tracker.saveCommissions', $context);

		$context->debug('Saving commissions ended');
		$context->debug("");
    }

   public function saveCommission(Pap_Contexts_Tracking $context, Pap_Common_User $user, Pap_Tracking_Common_Commission $commission) {
        $context->debug('Saving '.$context->getActionType().' commission started');

        $transaction = $context->getTransaction($commission->getTier());
        if ($transaction == null) {
            $transaction = clone $context->getTransaction(1);
            $transaction->setPersistent(false);
            $transaction->setId($transaction->generateNewTransactionId());
        }
        if (($parentTransaction = $context->getTransaction($commission->getTier() - 1)) != null) {
            $transaction->setParentTransactionId($parentTransaction->getId());
        }
        if (($channel = $context->getChannelObject()) != null) {
            $transaction->setChannel($channel->getId());
        }

        $transaction->setTier($commission->getTier());
        $transaction->setUserId($user->getId());
        $transaction->setCampaignId($context->getCampaignObject()->getId());
        $transaction->setAccountId($context->getAccountId());

        $banner = $context->getBannerObject();
        if (!is_null($banner)) {
            $transaction->setBannerId($banner->getId());
        }

        if ($user->getStatus() == 'P') {
        	$transaction->setStatus('P');
        	$context->debug('Commission is saved as pending because user state is in pending');
        } else {
            $transaction->setStatus($commission->getStatus());
        }
        $transaction->setPayoutStatus(Pap_Common_Transaction::PAYOUT_UNPAID);
        $transaction->setCommissionTypeId($context->getCommissionTypeObject()->getId());
        $transaction->setCountryCode(($context->getVisit()!=null)?$context->getVisit()->getCountryCode():'');
        $transaction->setType($context->getCommissionTypeObject()->getType());
        $transaction->setCommission($commission->getCommission($context->getRealTotalCost()-$context->getFixedCost()));
        $context->debug('  Computed commission is: '.$transaction->getCommission());
        $transaction->setClickCount(1);
        $transaction->setLogGroupId($context->getLoggerGroupId());

        if ($transaction->getTier() == 1) {
            $transaction->setSaleId($transaction->getId());
        } else {
            $transaction->setSaleId($context->getTransaction(1)->getSaleId());
        }

        //check if we can save zero commission
        if ($transaction->getCommission() == 0 &&
        $context->getCommissionTypeObject()->getSaveZeroCommissions() != Gpf::YES) {
            $context->debug('  Saving of commission transaction was STOPPED. Saving of zero commissions is disabled. Trans id: '.$transaction->getId());
            return Gpf_Plugins_Engine::PROCESS_CONTINUE;
        }


        $transactionCompoundContext = new Pap_Common_TransactionCompoundContext($transaction, $context);
        Gpf_Plugins_Engine::extensionPoint('Tracker.saveCommissions.beforeSaveTransaction', $transactionCompoundContext);
        if (!$transactionCompoundContext->getSaveTransaction()) {
            $context->debug('  Saving of commission transaction was STOPPED by plugin. Trans id: '.$transaction->getId());
            return Gpf_Plugins_Engine::PROCESS_CONTINUE;
        }

        $this->saveTransaction($transaction, $context->getVisitDateTime());
        $context->setTransactionObject($transaction, $commission->getTier());

        $context->debug('    Commission transaction was successfuly saved with ID: '.$transaction->getId());
        $context->debug('Saving '.$context->getActionType().' commission ended');
        $context->debug('');

        return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    protected function saveTransaction(Pap_Db_Transaction $transaction, $dateInserted) {
        $transaction->setDateInserted($dateInserted);
    	$transaction->save();
    }
}



?>
