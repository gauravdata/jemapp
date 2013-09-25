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
class Pap_Tracking_Action_SendTransactionNotificationEmails extends Gpf_Object {

    /**
     * @var Pap_Common_Transaction
     */
    private $transaction;
    /**
     * @var Gpf_Settings_AccountSettings
     */
    protected $accountSettings;

    public function __construct(Pap_Common_Transaction $transaction) {
        $this->transaction = $transaction;
        $this->accountSettings = $this->createAccountSettings();
    }

    public function sendOnNewSaleNotification() {
        $this->sendOnNewSaleNotificationToMerchant();
        $this->sendOnNewSaleNotificationToDirectAffiliate();
    }

    public function sendOnNewSaleNotificationToMerchant() {
        try {
            $user = $this->getUser($this->transaction);
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::debug('Sending notification to merchant ended');
            Gpf_Log::error('User with userid=' . $this->transaction->getUserId() . 'can not be loaded and mail can not be send.');
            return;
        }
        $isNotify = $this->accountSettings->get(Pap_Settings::NOTIFICATION_ON_SALE);
        if($isNotify <> Gpf::YES) {
            Gpf_Log::debug('Merchant does not have email notifications on sale');
            return;
        }

        if (strstr($this->accountSettings->get(Pap_Settings::NOTIFICATION_ON_SALE_STATUS), $this->transaction->getStatus()) === false) {
            Gpf_Log::debug('Merchant does not have notification for transaction with status '.$this->transaction->getStatus());
            return;
        }

        Gpf_Log::debug('Sending normal sale notification');
        $this->sendEmail(new Pap_Mail_MerchantOnSale(), $user,
        $this->transaction, $this->getMerchantEmail());

        Gpf_Log::debug('Sending notification to merchant ended');
    }

    public function sendOnNewSaleNotificationToDirectAffiliate() {
        try {
            $user = $this->getUser($this->transaction);
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::error('User with userid=' . $this->transaction->getUserId() . 'can not be loaded and mail can not be send.');
            Gpf_Log::debug('Sending notification to affiliate ended');
            return;
        }
        $isNotify = $this->isNotify($user,
        Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME,
        Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME,
    	   'aff_notification_on_new_sale',
        $this->transaction->getStatus(),
        Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_STATUS);

        if($isNotify <> Gpf::YES) {
            Gpf_Log::debug('Sending new sale notification to affiliate ended. Affiliate '.$user->getId().': '.$user->getName().' does not have new sale notification after sales turned on.');
            return;
        }

        $this->sendEmail(new Pap_Mail_AffiliateOnNewSale(), $user, $this->transaction, $user->getEmail());
        Gpf_Log::debug('Sending notification to affiliate ended');
    }

    public function sendOnNewSaleNotificationToParentAffiliate() {
        try {
            $user = $this->getUser($this->transaction);
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::error('User with userid=' . $this->transaction->getUserId() . 'can not be loaded and mail can not be send.');
            Gpf_Log::debug('Sending new sale notification to parent affiliate (sub-tier sale) ended.');
            return;
        }
        $isNotify = $this->isNotify($user,
        Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME,
        Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME,
    	   "aff_notification_on_subaff_sale");
         
        if ($isNotify <> Gpf::YES) {
            Gpf_Log::debug('Sending new sale notification to parent affiliate (sub-tier sale) ended. Affiliate '.$user->getId().': '.$user->getName().' does not have notification after sub-affiliate sale turned on');
            return;
        }
         
        $this->sendEmail(new Pap_Mail_OnSubAffiliateSale(), $user, $this->transaction, $user->getEmail());
        Gpf_Log::debug('Sending new sale notification to parent affiliate (sub-tier sale) ended.');
    }

    public function sendOnChangeStatusNotification() {
        try {
            $user = $this->getUser($this->transaction);
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::error('User with userid=' . $this->transaction->getUserId() . 'can not be loaded and mail can not be send.');
            Gpf_Log::debug('Sending notification to affiliate ended');
            return;
        }
        $isNotify = $this->isNotify($user,
        Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME,
        Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME,
            'aff_notification_on_change_comm_status',
        $this->transaction->getStatus(),
        Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_OPTION_STATUS);

        if ($isNotify <> Gpf::YES) {
            Gpf_Log::debug('Sending change status notification to affiliate ended. Affiliate '.$user->getId().': '.$user->getName().' does not have change status notification turned on');
            return;
        }

        $this->sendEmail(new Pap_Mail_AffiliateChangeCommissionStatus(), $user, $this->transaction, $user->getEmail());
        
        if(Gpf_Settings::get(Pap_Settings::NOTIFICATION_ON_COMMISSION_APPROVED) == Gpf::YES && $this->transaction->getStatus() == Pap_Common_Constants::STATUS_APPROVED){
            $this->sendEmail(new Pap_Mail_MerchantOnCommissionApproved(), $user, $this->transaction, $this->getMerchantEmail());
        }
        
        Gpf_Log::debug('Sending notification to affiliate ended');
    }

    private function isNotify(Pap_Common_User $user, $defaultSetting, $enabledSetting, $settingName, $transactionStatus = null, $transactionStatusSettingName = null) {

        $isNotify = $this->accountSettings->get($defaultSetting);
        try {
            if ($this->accountSettings->get($enabledSetting) == Gpf::YES) {
                $isNotify = Gpf_Db_Table_UserAttributes::getSetting($settingName, $user->getAccountUserId());
            }
        } catch(Gpf_Exception $e) {
        }

        if ($transactionStatus == null) {
            return $isNotify;
        }

        if (strstr($this->accountSettings->get($transactionStatusSettingName), $transactionStatus) === false) {
            return Gpf::NO;
        }

        return $isNotify;
    }

    protected function sendEmail(Pap_Mail_SaleMail $mailTemplate, $user, Pap_Common_Transaction $transaction, $recipient) {
        $mailTemplate->setUser($user);
        $mailTemplate->setTransaction($transaction);
        $mailTemplate->addRecipient($recipient);
        $mailTemplate->send();
    }

    protected function getUser(Pap_Common_Transaction $transaction) {
        return Pap_Common_User::getUserById($transaction->getUserId());
    }
    
    /**
     * @return Gpf_Settings_AccountSettings
     */
    protected function createAccountSettings() {
    	$campaign = new Pap_Common_Campaign();
    	$campaign->setId($this->transaction->getCampaignId());
    	try {
    		$campaign->load();
    		return Gpf_Settings::getAccountSettings($campaign->getAccountId());
    	} catch (Gpf_Exception $e) {
    	}
    	return Gpf_Settings::getAccountSettings(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
    }

    protected function getMerchantEmail() {
    	return $this->accountSettings->get(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL);
    }
}

?>
