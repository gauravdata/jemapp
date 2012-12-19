<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
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
class LifetimeCommissions_Main extends Gpf_Plugins_Handler {
    
	public static function getHandlerInstance() {
 		return new LifetimeCommissions_Main();
	}

 	/**
 	 * checks if lifetime referral exists and if user (affiliate) is still valid
 	 *
 	 * @param Pap_Contexts_Action $context
 	 */
 	public function checkLifetimeReferral(Pap_Common_VisitorAffiliateCacheCompoundContext $visitorAffiliateCompoundContext) {
 	    $context = $visitorAffiliateCompoundContext->getContext();
    	$context->debug(Gpf_Lang::_runtime('PLUGIN LifetimeCommissions checkLifetimeReferral started'));

    	$identifier = $this->getIdentifier($context);
 		if($identifier == null || $identifier == '') {
 			$context->debug("  Identifier (data1) is empty, stopping");
 		} else {
 			$user = $this->findUserByIdentifier($context, $identifier);

 			if($user != null) {
 				$context->setUserObject($user);
 			}
 		}

    	$context->debug(Gpf_Lang::_runtime('PLUGIN LifetimeCommissions ended'));
    	$context->debug('');
    	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
 	}
 	
    public function checkLifitimeLimit(Pap_Contexts_Action $context) {
        if (Gpf_Settings::get(LifetimeCommissions_Config::LIFETIME_COMMISSION_COOKIE_LIMIT_ACTIVE) == Gpf::NO) {
            return;
        }
        $context->debug('  Checking LifeTime cookie limit started');
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Pap_Db_Table_Transactions::DATE_INSERTED);
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::DATA1, '=', $context->getExtraDataFromRequest(1));
        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Db_Transaction::TYPE_SALE);
        $select->orderBy->add(Pap_Db_Table_Transactions::DATE_INSERTED);
        $select->limit->set(0, 1);
        try {
            $firstSale = $select->getOneRow()->get(Pap_Db_Table_Transactions::DATE_INSERTED);
            $cookieLifeTime = Pap_Tracking_Cookie::getCookieLifeTimeInDays($context);
            if (Gpf_Common_DateUtils::getDifference($firstSale, Gpf_Common_DateUtils::getDateTime(time()), 'day') > $cookieLifeTime) {
                $context->setDoCommissionsSave(false);
                $context->debug('    Transaction not saved. Date of first transaction: ' . $firstSale . ' is not in cookie limit: ' . $cookieLifeTime . ' days.');
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
        $context->debug('  Checking LifeTime cookie limit ended');
    }

 	private function findUserByIdentifier(Pap_Contexts_Action $context, $identifier) {
 		$lifetimeCommission = new Pap_Db_LifetimeCommission();
 		$lifetimeCommission->setIdentifier($identifier);

 		try {
 			$lifetimeCommission->loadFromData(array(Pap_Db_Table_LifetimeCommissions::IDENTIFIER));
 			$context->debug("  Found user '".$lifetimeCommission->getUserId()."' for identifier '$identifier', checking if user is valid");

			return $this->checkUserIsCorrect($context, $lifetimeCommission->getUserId());
 		} catch (Gpf_DbEngine_NoRowException $e) {
 			$context->debug("  New lifetime referral: identifier '$identifier' <-> user '".$lifetimeCommission->getUserId()."' inserted");
 			return null;
 		} catch (Gpf_DbEngine_TooManyRowsException $e) {
 			$context->debug("  Too many rows for ths identifier '$identifier'");
 			return null;
 		}
 	}

    /**
     * checks that user with this ID exists and is correct
     *
     * @param Pap_Contexts_Action $context
     * @param string $userId
     * @param string $trackingMethod
     * @return Pap_Common_User
     */
    private function checkUserIsCorrect(Pap_Contexts_Action $context, $userId) {
    	$userObj = $this->getUserById($context, $userId);
    	if($userObj != null) {
    		$context->setTrackingMethod(Pap_Common_Transaction::TRACKING_METHOD_LIFETIME_REFERRAL);
    	}
    	return $userObj;
    }

	/**
	 * gets user by user id
	 * @param $userId
	 * @return Pap_Common_User
	 */
	protected function getUserById($context, $id) {
		if($id == '') {
			return null;
		}

		// try to get user by userid
		$user = new Pap_Common_User();
		$user->setPrimaryKeyValue($id);
		try {
			$user->load();
			if($user->getStatus() == Pap_Common_Constants::STATUS_DECLINED) {
				$context->debug("    User with UserId: $id found, but he is declined. We'll not use him.");
				return null;
			}

			$context->debug("    User with UserId: $id found an dvalid (approved or pending)");
			return $user;
		} catch (Gpf_DbEngine_NoRowException $e) {
			$context->debug("    User with UserId: $id doesn't exist");
			return null;
		}
	}

 	/**
 	 * saves lifetime referral.
 	 * It requires the sale parameter data1 to be filler. This is the identifier
 	 * that identifies the customer, for example his email.
 	 *
 	 * @param Pap_Contexts_Action $context
 	 */
 	public function saveLifetimeReferral(Pap_Contexts_Action $context) {
    	$context->debug(Gpf_Lang::_runtime('PLUGIN LifetimeCommissions saveLifetimeReferral started'));

    	if($context->getDoCommissionsSave()) {
    		$this->saveReferral($context);
    	} else {
    		$context->debug('  Commissions are not saved, stopping');
    	}

    	$context->debug(Gpf_Lang::_runtime('PLUGIN LifetimeCommissions ended'));
    	$context->debug('');

    	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
 	}

 	private function saveReferral(Pap_Contexts_Action $context) {
 		$transactionObject = $context->getTransactionObject();
 		if($transactionObject == null) {
 			$context->debug('  Transaction object is null');
 			return;
 		}

 		$identifier = $this->getIdentifier($context);
 		if($identifier == null || $identifier == '') {
 			$context->debug("  Identifier (data1) is empty, stopping");
 			return;
 		}

 		$userId = $transactionObject->get(Pap_Db_Table_Transactions::USER_ID);

        $context->debug("  User is '$userId'");

 		$context->debug("  Identifier (data1) is '$identifier'");

 		$lifetimeCommission = new Pap_Db_LifetimeCommission();
 		$lifetimeCommission->setIdentifier($identifier);
 		$lifetimeCommission->setUserId($userId);

 		try {
 			$lifetimeCommission->loadFromData(array(Pap_Db_Table_LifetimeCommissions::IDENTIFIER, Pap_Db_Table_LifetimeCommissions::USER_ID));
 			$context->debug("  Lifetime referral: identifier '$identifier' <-> user '$userId' already exists");
 		} catch (Gpf_DbEngine_NoRowException $e) {
 			$lifetimeCommission->insert();
 			$context->debug("  New lifetime referral: identifier '$identifier' <-> user '$userId' inserted");
 		}
 	}

 	private function getIdentifier(Pap_Contexts_Tracking $context) {
 		if ($context->getExtraDataFromRequest(1) != '') {
 		    return $context->getExtraDataFromRequest(1);
 		}
 		if (!is_null($context->getTransactionObject())) {
            return $context->getTransactionObject()->getData1();
 		}
 		return '';
 	}

 	/**
 	 * add custom screen to menu
 	 *
 	 * @param Gpf_Menu $context
 	 */
 	public function addToMenu(Gpf_Menu $menu) {
//    	$menu->debug('##PLUGIN LifetimeCommissions addToMenu started##');
// 	    $menu->getItem('Affiliates-Overview')->addItem('lifetimeCommissions', $this->_('Lifetime commissions'));
//    	$menu->debug('##PLUGIN LifetimeCommissions addToMenu ended##');
    	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
 	}
 	
    public function initSettings($context) {
        $context->addDbSetting(LifetimeCommissions_Config::LIFETIME_COMMISSION_COOKIE_LIMIT_ACTIVE, Gpf::NO);
    }
}
?>
