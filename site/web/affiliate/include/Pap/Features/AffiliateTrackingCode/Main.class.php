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
class Pap_Features_AffiliateTrackingCode_Main extends Gpf_Plugins_Handler {
    private static $instance = false;
    /**
     * @return Pap_Features_AffiliateTrackingCode_Main
     */
    private function __construct() {
    }

    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Features_AffiliateTrackingCode_Main();
        }
        return self::$instance;
    }

    public function addToMerchantMenu(Pap_Merchants_Menu $menu) {
        $menu->getItem('Affiliates-Overview')->addItem('Affiliate-Tracking-Codes', $this->_('Affiliate Tracking Code'));
    }

    public function initAffiliateCampaignsGridViewColumns(Pap_Merchants_Campaign_CampaignsGrid $grid) {
        $grid->addViewColumn(Gpf_View_GridService::ACTIONS, $this->_('Actions'));
    }

    public function initAffiliateCampaignsGridDefaultView(Pap_Merchants_Campaign_CampaignsGrid $grid) {
        $grid->addDefaultViewColumn(Gpf_View_GridService::ACTIONS);
    }

    public function initDefaultAffiliatePrivileges(Pap_Privileges_Affiliate $affiliatePrivileges) {
        $affiliatePrivileges->addPrivilege(Pap_Privileges::AFFILIATE_TRACKING_CODE, Pap_Privileges::P_READ_OWN);
        $affiliatePrivileges->addPrivilege(Pap_Privileges::AFFILIATE_TRACKING_CODE, Pap_Privileges::P_WRITE_OWN);
    }

    public function displayAffiliateTrackingCode(Pap_Contexts_Action $context) {
         
         
        if ($context->getDoCommissionsSave() == false) {
            $context->debug('AffiliateTrackingCode: commissions were not saved. stopping');
            return;
        }
        $commissionType = $context->getCommissionTypeObject();
        $affiliate = $context->getUserObject();
        if ($commissionType == null || $affiliate == null) {
            $context->debug('AffiliateTrackingCode: no affiliate or commission type. stopping');
            return;
        }
        if ($context->getTransactionObject()->getTransactionId() == '') {
            $context->debug('AffiliateTrackingCode: no transaction saved for affiliate: ' . $affiliate->getId() . '. stopping');
            return;
        }
        try {
            $affiliateTrackingCode = $this->loadAffiliateTrackingCode($commissionType, $affiliate);
        } catch (Gpf_Exception $e) {
            $context->debug('AffiliateTrackingCode: no approved code for this affiliate');
            return $context;
        }

        $affiliateTrackingCode->setCode($this->replaceTransactionConstants($affiliateTrackingCode->getCode(), $context->getTransactionObject()));

        $this->printAffiliateTrackingCode($affiliateTrackingCode);
    }

    private function replaceTransactionConstants($text,Pap_Common_Transaction $transaction) {
        $transactionFields = Pap_Common_TransactionFields::getInstance();
        $transactionFields->setTransaction($transaction);

        $text = $transactionFields->replaceTransactionConstantsInText($text);
        $text = $transactionFields->removeTransactionCommentsInText($text);

        return $text;
    }

    private function printAffiliateTrackingCode(Pap_Db_AffiliateTrackingCode $affiliateTrackingCode) {
        if ($affiliateTrackingCode->getType() === Pap_Db_AffiliateTrackingCode::TYPE_HTML) {
            echo "function setTrackingCode() {\n"
            . "document.getElementById('_affiliatetrackingcode').contentWindow.document.body.innerHTML = '"
            . $affiliateTrackingCode->getCode()
            . "';\n"
            . "}\n"
            . "\n"
            . "var iframe = document.createElement('iframe');\n"
            . "iframe.width = '1';\n"
            . "iframe.height = '1';\n"
            . "iframe.setAttribute('frameborder', '0');\n"
            . "iframe.id = '_affiliatetrackingcode';\n"
            . "\n"
            . "scriptElement = document.getElementById('pap_x2s6df8d');\n"
            . "scriptElement.parentNode.insertBefore(iframe, scriptElement.nextSibling);\n"
            . "setTimeout(setTrackingCode, 1000);";
            return;
        }
        echo $affiliateTrackingCode->getCode();
    }

    /**
     * @return Pap_Db_AffiliateTrackingCode
     */
    private function loadAffiliateTrackingCode(Pap_Db_CommissionType $commissionType, Pap_Common_User $affiliate) {
        $code = new Pap_Db_AffiliateTrackingCode();
        $code->setAffiliateId($affiliate->getId());
        $code->setCommissionTypeId($commissionType->getId());
        $code->setStatus(Pap_Common_Constants::STATUS_APPROVED);
        $code->loadFromData();
        return $code;
    }
}
?>
