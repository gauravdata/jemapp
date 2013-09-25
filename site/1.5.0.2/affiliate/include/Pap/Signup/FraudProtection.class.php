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
class Pap_Signup_FraudProtection extends Gpf_Object {

    const ACTION_DECLINE = 'D';
    const ACTION_DONTSAVE = 'DS';

    /**
     * checks for click fraud rules...
     *
     * @param Pap_Contexts_Click $context
     */
    public function check(Pap_Signup_SignupFormContext $context) {
        $this->checkBannedIps($context);
        $this->checkMultipleSignupsFromSameIP($context);
        Gpf_Plugins_Engine::extensionPoint('FraudProtection.Signup.check', $context);
    }


    /**
     * checks banned IP
     *
     * @return boolean
     */
    private function checkBannedIps(Pap_Signup_SignupFormContext $context) {
        $checkIt = Gpf_Settings::get(Pap_Settings::BANNEDIPS_SIGNUPS);
        if($checkIt != Gpf::YES) {
            return true;
        }

        $bannedIPAddresses = Gpf_Net_Ip::getBannedIPAddresses(Pap_Settings::BANNEDIPS_LIST_SIGNUPS);
        $checkAction = Gpf_Settings::get(Pap_Settings::BANNEDIPS_SIGNUPS_ACTION);
        if($bannedIPAddresses === false) {
            return true;
        }
        if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
            return true;
        }

        $userObject = new Pap_Common_User();

        if (Gpf_Net_Ip::ipMatchRange($context->getIp(), $bannedIPAddresses)) {
            if($checkAction == self::ACTION_DONTSAVE) {
                $context->getForm()->setErrorMessage($this->_("Not saved by fraud protection - your IP address is banned"));
                $context->setAllowSave(false);
                return false;
            } else if ($checkAction == self::ACTION_DECLINE) {
                $context->getRow()->setStatus(Gpf_Db_User::DECLINED);
            }
        }
        return true;
    }

    /**
     * checks for duplicate signups from same IP
     *
     * @return boolean
     */
    private function checkMultipleSignupsFromSameIp(Pap_Signup_SignupFormContext $context) {
        $checkIt = Gpf_Settings::get(Pap_Settings::REPEATING_SIGNUPS_SETTING_NAME);
        if($checkIt != Gpf::YES) {
            return true;
        }

        $checkPeriod = Gpf_Settings::get(Pap_Settings::REPEATING_SIGNUPS_SECONDS_SETTING_NAME);
        $checkAction = Gpf_Settings::get(Pap_Settings::REPEATING_SIGNUPS_ACTION_SETTING_NAME);
        if($checkPeriod == '' || $checkPeriod == '0' || !is_numeric($checkPeriod)) {
            return true;
        }
        if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
            return true;
        }

        $userObject = new Pap_Common_User();

        $recordsCount = $userObject->getNumberOfUsersFromSameIP($context->getIp(), $checkPeriod);
        if(($recordsCount > 0) && ($checkAction == self::ACTION_DONTSAVE)) {
            $context->getForm()->setErrorMessage($this->_("Not saved by fraud protection"));
            $context->setAllowSave(false);
            return false;
        } else if (($recordsCount > 0) && ($checkAction == self::ACTION_DECLINE)) {
            $context->getRow()->setStatus(Gpf_Db_User::DECLINED);
        }
        return true;
    }
}

?>
