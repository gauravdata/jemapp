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
class Pap_Signup_SendNotificationEmails extends Gpf_Object {

    public function process(Pap_Contexts_Signup $context) {
        $context->debug('    Sending notification emails started');

        $this->sendNotificationToMerchant($context);        

        $context->debug('Sending notification emails ended');
        $context->debug('');
        return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    private function notificationOnNewUser() {
        return Gpf_Settings::get(Pap_Settings::NOTIFICATION_NEW_USER_SETTING_NAME)==Gpf::YES;
    }

    public function sendMerchantNewUserSignupMail(Pap_Common_User $user, $recipient, $context = null) {
        if (!$this->notificationOnNewUser()) {
            if ($context != null) {
                $context->debug('Notification on new affiliate signup is not set up');
            }
            return;
        }
        $signupMail = new Pap_Mail_MerchantNewUserSignup();
        $signupMail->setUser($user);
        $signupMail->setReplyTo($user->getFirstName()." ".$user->getLastName()." <".$user->getEmail().">");
        $signupMail->addRecipient($recipient);
        $signupMail->sendNow();
    }

    private function sendNotificationToMerchant(Pap_Contexts_Signup $context) {
        $context->debug('Sending notification email to merchant started');
        $context->debug('Notification on new affiliate signup email is enabled, sending to address: '.Pap_Common_User::getMerchantEmail());

        $this->sendMerchantNewUserSignupMail($context->getUserObject(), Pap_Common_User::getMerchantEmail());

        $context->debug('Sending notification email to merchant ended');
    }
}

?>
