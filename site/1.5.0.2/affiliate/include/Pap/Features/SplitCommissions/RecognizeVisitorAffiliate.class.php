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
class Pap_Features_SplitCommissions_RecognizeVisitorAffiliate extends Gpf_Object {

    private static $instance = null;

    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Features_SplitCommissions_RecognizeVisitorAffiliate();
        }
        return self::$instance;
    }

    public function recognize(Pap_Common_VisitorAffiliateCacheCompoundContext $visitorAffiliatesCacheCompoundContext) {
        $cache = $visitorAffiliatesCacheCompoundContext->getVisitorAffilliateCache();
        $context = $visitorAffiliatesCacheCompoundContext->getContext();
        $context->debug('SplitCommissions - Recognize VisitorAffiliate started');

        if ($context->getVisitorId() == '') {
            $context->debug('SplitCommissions - Recognize VisitorAffiliate not visitor id set - STOPPED');
            return;
        }
        $visitorAffiliates = $cache->getVisitorAffiliateAllRows($context->getVisitorId());
        $visitorAffiliate = $this->getLatestVisitorAffiliate($visitorAffiliates);

        if ($visitorAffiliate == null) {
            $context->debug('SplitCommissions - Recognize VisitorAffiliate not visitorAffiliate with visitorid: '.$context->getVisitorId().' found. STOPPED');
            return;
        }

        $context->setVisitorAffiliate($visitorAffiliate);
        $context->debug('SplitCommissions - Recognize VisitorAffiliate visitorAffiliate with visitorid: '.$context->getVisitorId().', id: '.$visitorAffiliate->getId().', account: '.$visitorAffiliate->getAccountId().' found. ENDED');
    }
    
    public function addForcedAffiliateToVisitorAffiliates(Pap_Common_VisitorAffiliateCacheCompoundContext $visitorAffiliatesCacheCompoundContext) {
        $context = $visitorAffiliatesCacheCompoundContext->getContext();
        if ($context->getTrackingMethod() != Pap_Common_Transaction::TRACKING_METHOD_FORCED_PARAMETER && 
            $context->getTrackingMethod() != Pap_Common_Transaction::TRACKING_METHOD_COUPON) {
            $context->debug('SplitCommissions - not forced affiliate');
            return;
        }
        
        $context->debug('SplitCommissions - forced affiliate');
        $context->setDateCreated(Gpf_Common_DateUtils::now());
        $this->splitCommissionsSaveVisitorAffiliate($visitorAffiliatesCacheCompoundContext);
    }
    
    protected function splitCommissionsSaveVisitorAffiliate(Pap_Common_VisitorAffiliateCacheCompoundContext $visitorAffiliatesCacheCompoundContext) {
        Pap_Features_SplitCommissions_SaveVisitorAffiliate::getHandlerInstance()->saveVisitorAffiliate($visitorAffiliatesCacheCompoundContext);
    }

    /**
     * @return Pap_Db_VisitorAffiliate
     */
    private function getLatestVisitorAffiliate(Pap_Tracking_Common_VisitorAffiliateCollection $visitorAffiliates) {
        if ($visitorAffiliates->getSize() == 0) {
            return null;
        }

        $latest = null;
        foreach ($visitorAffiliates as $visitorAffiliate) {
            if (($latest == null || $latest->getDateVisit() < $visitorAffiliate->getDateVisit()) && $visitorAffiliate->isValid()) { 
                $latest = $visitorAffiliate;
            }
        }

        return $latest;
    }
}

?>
