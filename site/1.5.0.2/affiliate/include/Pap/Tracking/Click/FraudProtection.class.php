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
class Pap_Tracking_Click_FraudProtection extends Gpf_Object {

    const ACTION_DECLINE = 'D';
    const ACTION_DONTSAVE = 'DS';

    /**
     * checks for click fraud rules...
     *
     * @param Pap_Contexts_Click $context
     */
    public function check(Pap_Contexts_Click $context) {
        $context->debug('FraudProtection started');

        $this->checkBannedIP($context);
        $this->checkMultipleClicksFromSameIP($context);

        Gpf_Plugins_Engine::extensionPoint('FraudProtection.Click.check', $context);

        $context->debug('FraudProtection ended');
    }


    /**
     * checks for banned IP
     *
     * @param Pap_Contexts_Click $context
     * @return string
     */
    private function checkBannedIP(Pap_Contexts_Click $context) {
        if(Gpf_Settings::get(Pap_Settings::BANNEDIPS_CLICKS) != Gpf::YES) {
            $context->debug('Check for banned IP address is not turned on');
            return true;
        }

        $context->debug('Checking banned IP started');


        $bannedIPAddresses = Gpf_Net_Ip::getBannedIPAddresses(Pap_Settings::BANNEDIPS_LIST_CLICKS);

        if($bannedIPAddresses === false) {
            $context->debug('List of banned IP addresses is invalid or empty, stop checking');
            return true;
        }

        $checkAction = Gpf_Settings::get(Pap_Settings::BANNEDIPS_CLICKS_ACTION);
        if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
            $context->debug("Action after check is not correct: '$checkAction'");
            return true;
        }


        $ip = $context->getVisit()->getIp();

        if(Gpf_Net_Ip::ipMatchRange($ip, $bannedIPAddresses)) {
            if($checkAction == self::ACTION_DONTSAVE) {
                $context->debug("    STOPPING (setting setDoTrackerSave(false), IP: $ip is banned");
                $context->setDoTrackerSave(false);
                $context->debug('      Checking banned IP endeded');
                return false;

            } else {
                $context->debug("  DECLINING, IP: $ip is banned");

                $this->declineClick($context);

                $context->debug('      Checking banned IP endeded');
                return true;
            }
        } else {
            $context->debug("    IP: $ip is not banned");
        }

        $context->debug('      Checking banned IP endeded');
        return true;
    }


    /**
     * checks for duplicate records from same IP
     *
     * @param Pap_Contexts_Click $context
     * @return string
     */
    private function checkMultipleClicksFromSameIP(Pap_Contexts_Click $context) {
        if(Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_SETTING_NAME) != Gpf::YES) {
            $context->debug('    Check for duplicate clicks with the same IP is not turned on');
            return true;
        }

        $context->debug('    Checking duplicate clicks from the same IP started');

        $checkPeriod = Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME);
        $checkAction = Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_ACTION_SETTING_NAME);
        if($checkPeriod == '' || $checkPeriod == '0' || !is_numeric($checkPeriod)) {
            $context->debug("Checking period is not correct: '$checkPeriod'");
            return true;
        }
        if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
            $context->debug("Action after check is not correct: '$checkAction'");
            return true;
        }

        $ip = $context->getVisit()->getIp();
        $clickObject = new Pap_Db_RawClick();

        //only clicks on same banner will be fraudulent
        $bannerId = false;
        if (Gpf_Settings::get(Pap_Settings::REPEATING_BANNER_CLICKS) == Gpf::YES) {
            $bannerId = $context->getBannerId();
            if (!strlen($bannerId)) {
                $bannerId = false;
            }
        }

        $recordsCount = $clickObject->getNumberOfClicksFromSameIP($ip, $checkPeriod, $bannerId, $context->getVisitDateTime());
        if($recordsCount > 0) {
            if($checkAction == self::ACTION_DONTSAVE) {
                $context->debug("    STOPPING (setting setDoTrackerSave(false), found another clicks from the same IP: $ip within $checkPeriod seconds");
                $context->setDoTrackerSave(false);
                $context->debug('      Checking duplicate clicks from the same IP endeded');
                return false;

            } else {
                $context->debug("  DECLINING, found another clicks from the same IP: $ip within $checkPeriod seconds");

                $this->declineClick($context);

                $context->debug('      Checking duplicate clicks from the same IP endeded');
                return true;
            }
        } else {
            $context->debug("    No duplicate clicks from the same IP: $ip found");
        }

        $context->debug('      Checking duplicate clicks from the same IP endeded');
        return true;
    }

    /**
     * Sets status of transaction to declined and sets it's message
     *
     * @param Pap_Plugins_Tracking_Action_Context $context
     * @param string $checkMessage
     */
    private function declineClick(Pap_Contexts_Click $context) {
        $context->setClickStatus(Pap_Db_ClickImpression::STATUS_DECLINED);
    }
}

?>
