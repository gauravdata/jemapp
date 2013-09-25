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
class Pap_Tracking_Click_ClickProcessor extends Gpf_Object implements Pap_Tracking_Common_VisitProcessor {
    /*
     * @var Pap_Tracking_Common_RecognizeAccountId
     */
    private $accountRecognizer;

    /*
     * array<Pap_Tracking_Common_Recognizer>
     */
    private $paramRecognizers = array();

    /**
     * @var Pap_Tracking_Click_RecognizeDirectLink
     */
    private $directLinkRecognizer;

    /*
     * array<Pap_Tracking_Common_Saver>
     */
    private $savers = array();

    /*
     * Pap_Tracking_Click_FraudProtection
     */
    private $fraudProtection;

    /**
     * @var Pap_Tracking_Click_RecognizeAffiliate
     */
    private $affiliateRecognizer;

    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    private $visitorAffiliateCache;


    public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache, Pap_Tracking_Click_RecognizeAffiliate $recognizeAffiliate = null) {
        if ($recognizeAffiliate === null) {
            $recognizeAffiliate = new Pap_Tracking_Click_RecognizeAffiliate();
        }

        $this->visitorAffiliateCache = $visitorAffiliateCache;

        $this->accountRecognizer = new Pap_Tracking_Common_RecognizeAccountId();

        $this->paramRecognizers[] = $this->affiliateRecognizer = $recognizeAffiliate;
        $this->paramRecognizers[] = new Pap_Tracking_Click_RecognizeBanner();
        $this->paramRecognizers[] = $recognizeCampaign = new Pap_Tracking_Click_RecognizeCampaign($visitorAffiliateCache);
        $this->paramRecognizers[] = new Pap_Tracking_Click_RecognizeChannel();

        $this->directLinkRecognizer = new Pap_Tracking_Click_RecognizeDirectLink($recognizeCampaign);

        $this->savers[] = new Pap_Tracking_Click_SaveClick();
        $this->savers[] = new Pap_Tracking_Click_SaveClickCommissions();
        $this->savers[] = new Pap_Tracking_Click_SaveVisitorAffiliate($visitorAffiliateCache);

        $this->fraudProtection = new Pap_Tracking_Click_FraudProtection();
    }

    public function process(Pap_Db_Visit $visit) {
        if ($this->processParamsClick($visit)) {
            return;
        }

        $this->processDirectLinkClick($visit);
    }

    public function saveChanges() {
        foreach ($this->savers as $saver) {
            $saver->saveChanges();
        }
    }

    private function processParamsClick(Pap_Db_Visit $visit) {
        $context = $this->getContextFromParams($visit);
        if ($context == null) {
            return false;
        }
        $this->fraudProtection->check($context);
        foreach ($this->paramRecognizers as $recognizer) {
            $recognizer->recognize($context);
        }
        $this->saveClick($context);
        return true;
    }

    private function processDirectLinkClick(Pap_Db_Visit $visit) {
        if (Gpf_Settings::get(Pap_Settings::SUPPORT_DIRECT_LINKING) != Gpf::YES) {
            return;
        }
        $context = $this->createContext($visit);
        $this->fraudProtection->check($context);
        $this->directLinkRecognizer->process($context, $visit->getReferrerUrl());
        $this->saveClick($context);
    }

    private function isClickRequest(Pap_Tracking_Request $request) {
        return ($request->getAffiliateId() != '' || $request-> getForcedAffiliateId() != '');
    }

    /**
     * @param Pap_Db_Visit
     * @return Pap_Contexts_Click
     */
    private function getContextFromParams(Pap_Db_Visit $visit) {
        $context = $this->createContext($visit);

        $getRequest = new Pap_Tracking_Request();
        $getRequest->parseUrl($visit->getGetParams());
        if($getRequest->getAffiliateId() == ''){
            $context->debug('Affiliate Id or Affiliate Id Parameter is missing');
        }
        if ($this->isClickRequest($getRequest)) {
            $context->setRequestObject($getRequest);
            $context->debug('It is click request.');
            return $context;
        }
        $anchorRequest = new Pap_Tracking_Request();
        $anchor = $visit->getAnchor();
        $anchorRequest->parseUrl($anchor);
        if ($this->isClickRequest($anchorRequest)) {
            $context->setRequestObject($anchorRequest);
            $context->debug('It is anchor request, anchor: ' . $anchor);
            return $context;
        }

        if ($anchor != '' && Gpf_Settings::get(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING) == Gpf::YES) {
            $user = $this->affiliateRecognizer->getUserById($context, $anchor);
            if ($user == null) {
                $context->debug('User is null, anchor was:' . $anchor);
                return null;
            }
            $shortAnchorRequest = new Pap_Tracking_Request();
            $shortAnchorRequest->parseUrl('?'.Pap_Tracking_Request::getAffiliateClickParamName().'='.$anchor);
            $context->setRequestObject($shortAnchorRequest);
            $context->debug('Short anchor link');
            return $context;
        }

        $context->debug('Return null - this might be a problem...');
        return null;
    }

    private function createContext(Pap_Db_Visit $visit) {
        $context = new Pap_Contexts_Click();
        $context->setDoTrackerSave(true);
        $context->setVisit($visit);
        $context->setVisitorId($visit->getVisitorId());
        $context->setDateCreated($visit->getDateVisit());
        $this->accountRecognizer->recognize($context);
        $this->visitorAffiliateCache->setAccountId($context->getAccountId());
        return $context;
    }

    protected function saveClick(Pap_Contexts_Click $context) {
        Gpf_Plugins_Engine::extensionPoint('Tracker.click.recognizeParameters', $context);

        if(!$context->getDoTrackerSave()) {
            $context->debug('Click registration stopped by feature or plugin');
            return;
        }

        $context->debug("  Saving click started");

        Gpf_Plugins_Engine::extensionPoint('Tracker.click.beforeSaveClick', $context);

        foreach ($this->savers as $saver) {
            $saver->process($context);
        }

        Gpf_Plugins_Engine::extensionPoint('Tracker.click.afterSaveClick', $context);

        $context->debug("  Saving click ended");
        $context->debug("");
    }
}

?>
