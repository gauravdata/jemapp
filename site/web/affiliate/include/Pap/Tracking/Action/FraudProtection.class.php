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
class Pap_Tracking_Action_FraudProtection extends Gpf_Object {

    const ACTION_DECLINE = 'D';
    const ACTION_DONTSAVE = 'DS';

    /**
     * checks for click fraud rules...
     *
     * @param Pap_Contexts_Click $context
     */
    public function check(Pap_Contexts_Action $context) {
        $context->debug('    FraudProtection started');

        $this->checkSalesFromBannedIP($context);
        $this->checkMultipleSalesFromSameIP($context);
        $this->checkMultipleSalesWithSameOrderID($context);

        Gpf_Plugins_Engine::extensionPoint('FraudProtection.Action.check', $context);

        $context->debug('    FraudProtection ended');
        $context->debug("");
    }

    /**
     * checks for duplicate records from same IP
     *
     * @param Pap_Contexts_Action $context
     * @return string
     */
    private function checkMultipleSalesFromSameIP(Pap_Contexts_Action $context) {
        $checkIt = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SETTING_NAME);
        if($checkIt != Gpf::YES) {
            $context->debug('    Check for duplicate sales / leads with the same IP is not turned on');
            return true;
        }

        $context->debug('    Checking duplicate sales / leads from the same IP started');

        $checkPeriod = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME);
        $checkAction = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME);
        if($checkPeriod == '' || $checkPeriod == '0' || !is_numeric($checkPeriod)) {
            $context->debug("Checking period is not correct: '$checkPeriod'");
            return true;
        }
        if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
            $context->debug("Action after check is not correct: '$checkAction'");
            return true;
        }

        $campaignId = null; 
        if (Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SAMECAMPAIGN_SETTING_NAME) == Gpf::YES) {
            $campaignId = $context->getCampaignObject()->getId();
        }
        $orderId = null; 
        if (Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SAMEORDERID_SETTING_NAME) == Gpf::YES) {
            $orderId = $context->getOrderIdFromRequest();
            if (trim($orderId) == '') {
                $orderId = null;
            }
        }
        $ip = $context->getIp();
        $context->debug("    Looking transactions with IP: $ip" . (!is_null($campaignId) ? ", campaignid: $campaignId" : '') . (!is_null($orderId) ? ", order ID: $orderId" : '') . '.');
        $transactionsObject = $context->getTransactionObject();
        $recordsCount = $transactionsObject->getNumberOfRecordsFromSameIP($ip,  $this->getTransactionType($context), $checkPeriod, $context->getParentTransactionId(), $context->getVisitDateTime(), $campaignId, $orderId);
        if($recordsCount > 0) {
            if($checkAction == self::ACTION_DONTSAVE) {
                $context->debug("    STOPPING (setting setDoCommissionsSave(false), found another sales / leads from the same IP: $ip". (!is_null($campaignId) ? ", campaignid: $campaignId" : '') . (!is_null($orderId) ? ", order ID: $orderId" : '') ."within $checkPeriod seconds");
                $context->setDoCommissionsSave(false);
                $context->debug('      Checking duplicate sales / leads from the same IP endeded');
                return false;

            } else {
                $context->debug("  DECLINING, found another sales / leads from the same IP: $ip". (!is_null($campaignId) ? ", campaignid: $campaignId" : '') . (!is_null($orderId) ? ", order ID: $orderId" : '') ." within $checkPeriod seconds");

                $message = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_MESSAGE_SETTING_NAME);

                $this->declineAction($context, $message);

                $context->debug('      Checking duplicate sales / leads from the same IP endeded');
                return true;
            }
        } else {
            $context->debug("    No duplicate sales / leads from the same IP: $ip".(!is_null($campaignId) ? ", campaignid: $campaignId" : '') . (!is_null($orderId) ? ", order ID: $orderId" : '')." found");
        }

        $context->debug('      Checking duplicate sales / leads from the same IP endeded');
        return true;
    }

    /**
     * checks for duplicate records from same IP
     *
     * @param Pap_Contexts_Action $context
     * @return string
     */
    private function checkSalesFromBannedIP(Pap_Contexts_Action $context) {
        $checkIt = Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES);
        if($checkIt != Gpf::YES) {
            $context->debug('    Check for sales / leads with banned IP is not turned on');
            return true;
        }

        $context->debug('    Checking banned IP address of sales / leads started');


        $bannedIPAddresses = Gpf_Net_Ip::getBannedIPAddresses(Pap_Settings::BANNEDIPS_LIST_SALES);

        if($bannedIPAddresses === false) {
            $context->debug("List of banned IP addresses is invalid or empty, stop checking");
            return true;
        }

        $checkAction = Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES_ACTION);
        if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
            $context->debug("Action after check is not correct: '$checkAction'");
            return true;
        }

        $ip = $context->getIp();
        if(Gpf_Net_Ip::ipMatchRange($ip, $bannedIPAddresses)) {
            if($checkAction == self::ACTION_DONTSAVE) {
                $context->debug("    STOPPING (setting setDoCommissionsSave(false), IP: $ip is banned");
                $context->setDoCommissionsSave(false);
                $context->debug('      Checking banned IP of sales / leads endeded');
                return false;

            } else {
                $context->debug("  DECLINING, IP is banned: $ip");

                $message = Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES_MESSAGE);

                $this->declineAction($context, $message);

                $context->debug('      Checking banned IP of sales / leads endeded');
                return true;
            }
        } else {
            $context->debug("    IP $ip is not banned");
        }

        $context->debug('      Checking banned IP of sales / leads endeded');
        return true;
    }






    /**
     * checks for duplicate records with same OrderID
     *
     * @param Pap_Contexts_Action $context
     * @return string
     */
    private function checkMultipleSalesWithSameOrderID(Pap_Contexts_Action $context) {
        $checkIt = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_SETTING_NAME);
        if($checkIt != Gpf::YES) {
            $context->debug('    Check for duplicate sales / leads with the same OrderID is not turned on');
            return true;
        }

        $context->debug('    Checking duplicate sales / leads with the same OrderID started');

        $checkPeriod = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDER_ID_HOURS_SETTING_NAME);
        $checkAction = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME);
        if($checkPeriod == '' || $checkPeriod == '0' || !is_numeric($checkPeriod)) {
            $context->debug("Checking period is not correct: '$checkPeriod'");
            return true;
        }
        if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
            $context->debug("Action after check is not correct: '$checkAction'");
            return true;
        }

        $orderId = $context->getOrderIdFromRequest();
        $transactionsObject = $context->getTransactionObject();

        if(trim($orderId) == '') {
            $applyToEmptyOrderIDs = Gpf_Settings::get(Pap_Settings::APPLY_TO_EMPTY_ID_SETTING_NAME);
            if($applyToEmptyOrderIDs != Gpf::YES) {
                $context->debug('      Order ID is empty, we do not aply fraud protection to empty order IDs');
                return false;
            }
        }

        $transactionType = $this->getTransactionType($context);
        $parentTransactionId = $context->getParentTransactionId();
        $recordsCount = $transactionsObject->getNumberOfRecordsWithSameOrderId($orderId, $transactionType, $checkPeriod, $parentTransactionId, $context->getVisitDateTime());
        $context->debug("Getting number of transactions orderId=$orderId, type=$transactionType, not older than $checkPeriod hours, and not with parent transaction with id=$parentTransactionId returned $recordsCount");
        if($recordsCount > 0) {
            if($checkAction == self::ACTION_DONTSAVE) {
                $context->debug("    STOPPING (setting setDoCommissionsSave(false), found another sales / leads from the same OrderID '$orderId' within $checkPeriod hours");
                $context->setDoCommissionsSave(false);
                $context->debug('      Checking duplicate sales / leads with the same OrderID endeded');
                return false;

            } else {
                $context->debug("  DECLINING, found another sales / leads with the same OrderID '$orderId' within $checkPeriod hours");

                $message = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_MESSAGE_SETTING_NAME);

                $this->declineAction($context, $message);

                $context->debug('      Checking duplicate sales / leads with the same OrderID endeded');
                return true;
            }
        } else {
            $context->debug("    No duplicate sales / leads with the same OrderID '$orderId' found");
        }

        $context->debug('      Checking duplicate sales / leads with the same OrderID endeded');
        return true;
    }

    /**
     * Sets status of transaction to declined and sets it's message
     *
     * @param Pap_Contexts_Action $context
     * @param string $checkMessage
     */
    private function declineAction(Pap_Contexts_Action $context, $message) {
        $context->setFraudProtectionStatus(Pap_Db_ClickImpression::STATUS_DECLINED);
        $transactionsObject = $context->getTransactionObject();

        if($message != '') {
            $transactionsObject->setSystemNote($message);
        }
    }

    private function getTransactionType(Pap_Contexts_Action $context) {
        $actionCode = $context->getActionCodeFromRequest();
        if ($actionCode == null || $actionCode == '') {
            return Pap_Common_Constants::TYPE_SALE;
        } else {
            return Pap_Common_Constants::TYPE_ACTION;
        }
    }
}

?>
