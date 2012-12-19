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
class Pap_Tracking_Action_ActionProcessor extends Gpf_Object implements Pap_Tracking_Common_VisitProcessor {
    
    /*
     * @var Pap_Tracking_Common_RecognizeAccountId
     */
    private $accountRecognizer;
    
    /*
     * array<Pap_Tracking_Common_Recognizer>
     */
    private $paramRecognizers = array();

    /*
     * array<Pap_Tracking_Common_Recognizer>
     */
    private $settingLoaders = array();

    /*
     * array<Pap_Tracking_Common_Recognizer>
     */
    private $recognizers = array();

    /*
     * @var Pap_Tracking_Common_SaveAllCommissions
     */
    private $saveAllCommissionsSaver;

    /**
     * @var Gpf_Rpc_Json
     */
    private $json;

    /**
     * @var Pap_Tracking_Action_FraudProtection
     */
    private $fraudProtectionObj;

    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    private $visitorAffiliateCache;

    public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
        $this->saveAllCommissionsSaver = new Pap_Tracking_Common_SaveAllCommissions();
        $this->visitorAffiliateCache = $visitorAffiliateCache;
        $this->json = new Gpf_Rpc_Json();

        $this->accountRecognizer = new Pap_Tracking_Common_RecognizeAccountId();

        $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeVisitorAffiliateFromVisitorId($this->visitorAffiliateCache);
        $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeVisitorAffiliateFromIp($this->visitorAffiliateCache);
        $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeAffiliate($this->visitorAffiliateCache);
        $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeCampaign();
        $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeBanner();
        $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeChannel();

        $this->fraudProtectionObj = new Pap_Tracking_Action_FraudProtection();

        $this->recognizers[] = new Pap_Tracking_Action_RecognizeCommType();

        $this->settingLoaders[] = new Pap_Tracking_Common_RecognizeCommGroup();
        $this->settingLoaders[] = new Pap_Tracking_Common_RecognizeCommSettings();
        $this->settingLoaders[] = new Pap_Tracking_Action_ComputeCommissions();
        $this->settingLoaders[] = new Pap_Tracking_Action_ComputeStatus();
    }

    /**
     * @return Pap_Tracking_Visit_VisitorAffiliateCache
     */
    public function getVisitorAffiliatesCache() {
        return $this->visitorAffiliateCache;
    }

    public function process(Pap_Db_Visit $visit) {
    	Gpf_Log::debug('Action processor processing...');
        $accountContext = $this->processAccount($visit);        
        if (!$accountContext->getDoTrackerSave()) {
        	Gpf_Log::debug('Saving disabled because of account problems.');
            return;
        }
        $this->visitorAffiliateCache->setAccountId($accountContext->getAccountId());

        try {
            $actions = $this->loadActions($visit->getSaleParams());
        } catch (Gpf_Exception $e) {
            Gpf_Log::debug('Action processor: ' . $e->getMessage());
            return;
        }

        foreach ($actions as $action) {
            $context = new Pap_Contexts_Action($action, $visit);
            $context->debug('Saving sale/action for visit: '.$visit->toText());
            $context->setDoCommissionsSave(true);
            $context->setAccountId($accountContext->getAccountId(), $accountContext->getAccountRecognizeMethod());
            
            try {
                $this->processAction($context);
            } catch (Gpf_Exception $e) {
                $context->debug("Saving commission interrupted: ".$e->getMessage());
            }
        }
    }

    public function runSettingLoadersAndSaveCommissions(Pap_Contexts_Action $context) {
        $context->setDoCommissionsSave(true);
        $context->setDoTrackerSave(true);
        $this->runRecognizers($context, $this->settingLoaders,
            'Commission save disabled in load settings.');
        $this->prepareContextForSave($context);

        $this->saveAllCommissionsSaver->save($context);
    }
        
    /**
     *
     * @param Pap_Db_Visit $visit
     * @return Pap_Contexts_Action
     */
    private function processAccount(Pap_Db_Visit $visit) {
        $context = new Pap_Contexts_Action();
        $context->setVisit($visit);
        $context->setDoCommissionsSave(true);
        $context->setDoTrackerSave(true);

        $this->accountRecognizer->recognize($context);
        Gpf_Plugins_Engine::extensionPoint('Pap_Tracking_Action_ActionProcessor.processAccount', $context);
        return $context;
    }

    /**
     * @param Pap_Contexts_Action $context
     * @throws Gpf_Exception
     */
    private function processAction(Pap_Contexts_Action $context) {
        $visitorAffiliateCacheCompoundContext = new Pap_Common_VisitorAffiliateCacheCompoundContext($this->visitorAffiliateCache,
        $context);

        Gpf_Plugins_Engine::extensionPoint('Tracker.action.recognizeParametersStarted', $visitorAffiliateCacheCompoundContext);

        $this->runRecognizers($context, $this->paramRecognizers,
            'Commission save disabled in recognize parameters.');

        $this->fraudProtection($context);

        Gpf_Plugins_Engine::extensionPoint('Tracker.action.recognizeAfterFraudProtection', $visitorAffiliateCacheCompoundContext); 
        
        $this->runRecognizers($context, $this->recognizers,
            'Commission save disabled in recognize parameters - second part.');

        $this->runRecognizers($context, $this->settingLoaders,
            'Commission save disabled in load settings.');
        Gpf_Plugins_Engine::extensionPoint('Tracker.action.recognizeParametersEnded', $visitorAffiliateCacheCompoundContext);

        $this->saveCommissions($context);

        $this->deleteCookies($context);
    }

    private function fraudProtection(Pap_Contexts_Action $context) {
        $this->fraudProtectionObj->check($context);
        if (!$context->getDoCommissionsSave()) {
            throw new Gpf_Exception("Commission save disabled by fraud protection.");
        }
    }

    private function runRecognizers(Pap_Contexts_Action $context, array $recognizers, $stopMessage) {
        foreach ($recognizers as $recognizer) {
            $recognizer->recognize($context);
            if (!$context->getDoCommissionsSave()) {
                throw new Gpf_Exception($stopMessage);
            }
        }
    }

    private function deleteCookies(Pap_Contexts_Action $context) {
        if (Gpf_Settings::get(Pap_Settings::DELETE_COOKIE) != Gpf::YES) {
            return;
        }

        $visitorAffiliates = $this->visitorAffiliateCache->getVisitorAffiliateAllRows($context->getVisitorId());
        foreach ($visitorAffiliates as $visitorAffiliate) {
            $this->visitorAffiliateCache->removeVisitorAffiliate($visitorAffiliate->getId());
        }
    }

    private function loadActions($actionsString) {
        if ($actionsString == '') {
            throw new Gpf_Exception($this->_('no actions in visit'));
        }
        $actionsArray = $this->json->decode($actionsString);
        if (!is_array($actionsArray)) {
            throw new Gpf_Exception($this->_('invalid action format (%s)', $actionsString));
        }
        $actions = array();
        foreach ($actionsArray as $actionObject) {
            $actions[] = new Pap_Tracking_Action_RequestActionObject($actionObject);
        }
        return $actions;
    }

    public function saveChanges() {
    }

    private function saveCommissions(Pap_Contexts_Action $context) {
        $context->debug('Saving commissions started');
        Gpf_Plugins_Engine::extensionPoint('Tracker.action.beforeSaveCommissions', $context);
        if (!$context->getDoCommissionsSave()) {
            $context->debug('Commissions save stopped by plugin.');
            return;
        }
        $this->saveCommission($context);

        Gpf_Plugins_Engine::extensionPoint('Tracker.action.afterSaveCommissions', $context);

        $context->debug("Saving commissions ended");
    }

    protected function prepareContextForSave(Pap_Contexts_Action $context) {
        $transaction = $context->getTransaction();
        $transaction->setOrderId($context->getOrderIdFromRequest());
        $transaction->setProductId($context->getProductIdFromRequest());
        $transaction->setTotalCost($context->getRealTotalCost());
        $transaction->setFixedCost($context->getFixedCost());
        $transaction->setCountryCode($context->getCountryCode());

        if($context->getChannelObject() !== null) {
            $transaction->setChannel($context->getChannelObject()->getId());
        }
        if($context->getBannerObject() !== null) {
            $transaction->setBannerId($context->getBannerObject()->getId());
        }

        $transaction->setData1($context->getExtraDataFromRequest(1));
        $transaction->setData2($context->getExtraDataFromRequest(2));
        $transaction->setData3($context->getExtraDataFromRequest(3));
        $transaction->setData4($context->getExtraDataFromRequest(4));
        $transaction->setData5($context->getExtraDataFromRequest(5));

        $transaction->setDateInserted($context->getVisitDateTime());

        $transaction->setVisitorId($context->getVisitorId());
        $transaction->setTrackMethod($context->getTrackingMethod());
        $transaction->setIp($context->getIp());
        try {
            $transaction->setRefererUrl($context->getVisitorAffiliate()->getReferrerUrl());
        } catch (Gpf_Exception $e) {
            $transaction->setRefererUrl($context->getReferrerUrl());
        }

        try {
            $visitorId = $context->getVisitorAffiliate()->getVisitorId();
        } catch (Exception $e) {
            $visitorId = $this->_('unknown');
        }
        
        try {
            $this->setFirstAndLastClick($transaction, $this->getVisitorAffiliatesCollection($context));
        } catch (Gpf_Exception $e) {
            $context->debug('First and Last click can not be recognized for visitorId: ' . $visitorId . '. ' . $e->getMessage());
        }
    }

    /**
     * @throws Gpf_Exception
     * @return Pap_Tracking_Common_VisitorAffiliateCollection
     */
    protected function getVisitorAffiliatesCollection(Pap_Contexts_Action $context) {
        return $this->visitorAffiliateCache->getVisitorAffiliateAllRows($context->getVisitorAffiliate()->getVisitorId());
    }

    private function saveCommission(Pap_Contexts_Action $context) {
        $this->prepareContextForSave($context);

        $actionProcessorCompoundContext = new Pap_Common_ActionProcessorCompoundContext($context, $this);
        Gpf_Plugins_Engine::extensionPoint('Tracker.action.saveCommissions', $actionProcessorCompoundContext);
        if ($actionProcessorCompoundContext->getCommissionsAlreadySaved()) {
            return;
        }
        $this->saveAllCommissionsSaver->save($context);
    }

    protected function setFirstAndLastClick(Pap_Common_Transaction $transaction, Pap_Tracking_Common_VisitorAffiliateCollection $collection) {
        if ($collection->getSize() == 0) {
            throw new Gpf_Exception('VisitorAffiliates for this visitor are empty');
        }
        
        $firstVisitorAffiliate = $collection->get(0);
        $transaction->setFirstClickTime($firstVisitorAffiliate->getDateVisit());
        $transaction->setFirstClickReferer($firstVisitorAffiliate->getReferrerUrl());
        $transaction->setFirstClickIp($firstVisitorAffiliate->getIp());
        $transaction->setFirstClickData1($firstVisitorAffiliate->getData1());
        $transaction->setFirstClickData2($firstVisitorAffiliate->getData2());

        $lastVisitorAffiliate = $collection->get($collection->getSize()-1);
        $transaction->setLastClickTime($lastVisitorAffiliate->getDateVisit());
        $transaction->setLastClickReferer($lastVisitorAffiliate->getReferrerUrl());
        $transaction->setLastClickIp($lastVisitorAffiliate->getIp());
        $transaction->setLastClickData1($lastVisitorAffiliate->getData1());
        $transaction->setLastClickData2($lastVisitorAffiliate->getData2());
    }
}

?>
