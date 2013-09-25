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
class Pap_Signup_SendEmailToUser extends Gpf_Object {

    /**
     * @param Pap_Tracking_ActionContext $context
     * @return unknown
     */
    public function process(Pap_Contexts_Signup $context) {
    	$context->debug('    Sending email to affiliate started');

    	$affiliate = $context->getUserObject();
    	if($affiliate == null || !is_object($affiliate)) {
    		$context->debug('        STOPPING, Affiliate object is null');
        	return Gpf_Plugins_Engine::PROCESS_STOP_ALL;

    	}

    	$status = $affiliate->getStatus();
    	if($status == Pap_Common_Constants::STATUS_PENDING) {
    		$this->sendBeforeApprovalEmail($context);
    	}
    	$context->debug('    Sending email to affiliate ended');
    	$context->debug('');
    	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    public function sendNewUserSignupApprovedMail(Pap_Common_User $user, $recipient) {
        if (Gpf_Settings::get(Pap_Settings::AFF_NOTOFICATION_SIGNUP_APPROVED_DECLINED) == Gpf::NO) {
            Gpf_Log::info('Sending approval/declined mails to user was disabled');
        } else {
            $signupMail = $this->createNewUserSignupApprovedMail();
            $signupMail->setUser($user);
            $signupMail->addRecipient($recipient);
            $signupMail->sendNow();
        }
        if (!is_null($parentUser = $user->getParentUser())) {
        	$this->sendNewSubAffSignupMail($parentUser, $user);
        }
    }

    public function sendNewUserSignupDeclinedMail(Pap_Common_User $user, $recipient) {
        if (Gpf_Settings::get(Pap_Settings::AFF_NOTOFICATION_SIGNUP_APPROVED_DECLINED) == Gpf::NO) {
            Gpf_Log::info('Sending approval/declined mails to affiliate was disabled');
            return;
        }
        $signupMail = new Pap_Mail_NewUserSignupDeclined();
        $signupMail->setUser($user);
        $signupMail->addRecipient($recipient);
        $signupMail->sendNow();
    }
    
    /**
     * @return Pap_Mail_NewUserSignupApproved
     */
    protected function createNewUserSignupApprovedMail() {
    	return new Pap_Mail_NewUserSignupApproved();
    }

    private function affiliateNotificationBeforeApproval() {
        return Gpf_Settings::get(Pap_Settings::AFF_NOTOFICATION_BEFORE_APPROVAL) == Gpf::YES;
    }

    public function sendNewUserSignupBeforeApprovalMail(Pap_Common_User $user, $recipient, $context = null) {
        if (!$this->affiliateNotificationBeforeApproval()) {
            if ($context!==null) {
                $context->debug('Before-approval email is not enabled');
            }
            return;
        }
        $signupMail = new Pap_Mail_NewUserSignupBeforeApproval();
        $signupMail->setUser($user);
        $signupMail->addRecipient($recipient);
        $signupMail->sendNow();
    }

    private function sendApprovedEmail(Pap_Contexts_Signup $context) {
    	$context->debug('Sending approval email to affiliate started, email address: '.$context->getUserObject()->getEmail());

    	$this->sendNewUserSignupApprovedMail($context->getUserObject(), $context->getUserObject()->getEmail());

    	$context->debug('Sending approval email to affiliate ended');
    }

    private function sendDeclinedEmail(Pap_Contexts_Signup $context) {
        $context->debug('Sending declined email to affiliate started, email address: '.$context->getUserObject()->getEmail());

        $this->sendNewUserSignupDeclinedMail($context->getUserObject(), $context->getUserObject()->getEmail());

        $context->debug('Sending declined email to affiliate ended');
    }

    private function sendBeforeApprovalEmail(Pap_Contexts_Signup $context) {
    	$context->debug('        Sending before-approval email to affiliate started');
    	$context->debug('Before-approval email is enabled, sending to email address: '.$context->getUserObject()->getEmail());

    	$this->sendNewUserSignupBeforeApprovalMail($context->getUserObject(), $context->getUserObject()->getEmail(), $context);

    	$context->debug('Sending before-approval email to affiliate ended');
    }
    
    protected function sendNewSubAffSignupMail(Pap_Common_User $parentUser, Pap_Common_User $newUser) {
        $attribute = Gpf_Db_Table_UserAttributes::getInstance();
        $attribute->loadAttributes($parentUser->getAccountUserId());    	

		if (Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME) == Gpf::YES) {
            $isNotify = $attribute->getAttributeWithDefaultValue('aff_notification_on_subaff_signup',
			Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME));
		} else {
			$isNotify = Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME);
		}
		    	
		if ($isNotify == Gpf::YES) {
			$signupMail = new Pap_Mail_OnSubAffiliateSignup();
			$signupMail->setUser($newUser);
        	$signupMail->addRecipient($parentUser->getEmail());
			$signupMail->sendNow();
		}
    }
}

?>
