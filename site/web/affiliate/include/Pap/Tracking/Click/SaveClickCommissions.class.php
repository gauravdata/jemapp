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
class Pap_Tracking_Click_SaveClickCommissions extends Gpf_Object implements Pap_Tracking_Common_Saver {

    /*
     * array<Pap_Tracking_Common_Recognizer>
     */
    private $paramRecognizers = array();

    /**
     * array<Pap_Tracking_Common_Saver>
     */
    private $commissionSavers = array();

    private $commissions = array();

    public function __construct() {
        $this->paramRecognizers[] = new Pap_Tracking_Common_RecognizeCommGroup();
        $this->paramRecognizers[] = new Pap_Tracking_Common_RecognizeCommSettings();

        $this->commissionSavers[] = new Pap_Tracking_Common_UpdateAllCommissions();
    }

    public function saveChanges() {
        foreach ($this->commissionSavers as $commissionSaver) {
            $commissionSaver->saveChanges();
        }
    }

    public function process(Pap_Contexts_Tracking $context) {
        $context->debug('  Preparing commissions for the click started');

        $context->setDoCommissionsSave($this->isValidCommission($context));
        if (!$context->getDoCommissionsSave()) {
            return;
        }

        $this->recognizeCommissions($context);

        if($context->getDoCommissionsSave() && $context->getDoTrackerSave()
        && $context->getClickStatus() != Pap_Db_ClickImpression::STATUS_DECLINED) {
            $this->saveCommissions($context);
        }

        $context->debug('  Preparing commissions for the click ended');
        $context->debug('');
    }

    protected function recognizeCommissions(Pap_Contexts_Click $context) {
        foreach ($this->paramRecognizers as $recognizer) {
            $recognizer->recognize($context);
        }
    }

    protected function saveCommissions(Pap_Contexts_Click $context) {
        Gpf_Plugins_Engine::extensionPoint('Tracker.click.beforeSaveCommissions', $context);
        if (!$context->getDoCommissionsSave()) {
            $context->debug('Click commissions save stopped by plugin.');
            return;
        }
        foreach ($this->commissionSavers as $commissionSaver) {
            $commissionSaver->process($context);
        }
        Gpf_Plugins_Engine::extensionPoint('Tracker.click.afterSaveCommissions', $context);
    }

    private function isValidCommission(Pap_Contexts_Click $context) {
        $context->debug('    Checking if we should save commissions for this click');

        if(!$context->getDoTrackerSave()) {
            $context->debug("  Saving cookies in Tracker is disabled (getDoTrackerSave() returned false), so we set also saving comissions (getDoCommissionsSave() to false");
            return false;
        }

        if($context->getCampaignObject() == null) {
            $context->debug('        STOPPING, campaign not recognized');
            return false;
        }

        $clickCommission = $this->getCampaignClickCommissions($context);
        if ($clickCommission == null) {
            $context->debug('        STOPPING, campaign does not have per click commission');
            return false;
        }
        $context->setCommissionTypeObject($clickCommission);

        if (!$this->setCurrency($context)) {
            $context->debug('        STOPPING, no default currency defined');
            return false;
        }

        $this->initTransactionObject($context);

        $context->debug('    Checking ended');
        $context->debug('');

        return true;
    }

    protected function getCampaignClickCommissions(Pap_Contexts_Click $context) {
        try {
            $context->debug('        Checking that click commission is in campaign');

            if($context->getCampaignObject() == null) {
                $context->debug("    STOPPING, no campaign was recognized! ");
                return null;
            }
            
            return $context->getCampaignObject()->getCommissionTypeObject(Pap_Common_Constants::TYPE_CLICK, '', $context->getVisit()->getCountryCode()); 
        } catch (Pap_Tracking_Exception $e) {
            $context->debug("    STOPPING, This commission type is not supported by current campaign or is NOT enabled! ");
            return null;
        }
    }

    protected function setCurrency(Pap_Contexts_Click $context) {
        try {
            $defaultCurrency = Gpf_Db_Currency::getDefaultCurrency();
        } catch(Gpf_Exception $e) {
            $context->debug('        ERROR, Cannot get default curency');
            return false;
        }

        $context->debug("    Currency set to ".$defaultCurrency->getName());
        $context->setDefaultCurrencyObject($defaultCurrency);
        return true;
    }

    private function initTransactionObject(Pap_Contexts_Click $context) {
        $transaction = new Pap_Common_Transaction();

        $transaction->setTotalCost('');

        $transaction->generateNewTransactionId();
        $transaction->setData1($context->getExtraDataFromRequest(1));
        $transaction->setData2($context->getExtraDataFromRequest(2));
        $transaction->set(Pap_Db_Table_Transactions::REFERER_URL, $context->getReferrerUrl());
        $transaction->set(Pap_Db_Table_Transactions::IP, $context->getIp());
        $transaction->set(Pap_Db_Table_Transactions::BROWSER, $context->getUserAgent());
        $transaction->setType(Pap_Common_Constants::TYPE_CLICK);
        $transaction->setDateInserted($context->getVisitDateTime());
        if ($context->getVisit()!= null && $context->getVisit()->getCountryCode() != '') {
            $transaction->setCountryCode($context->getVisit()->getCountryCode());
        }
        $context->setTransactionObject($transaction);
        $context->debug("Transaction object set");
    }
}

?>
