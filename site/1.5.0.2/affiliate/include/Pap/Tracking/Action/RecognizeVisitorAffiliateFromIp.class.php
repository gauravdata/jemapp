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
class Pap_Tracking_Action_RecognizeVisitorAffiliateFromIp extends Gpf_Object {
    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    private $visitorAffiliateCache;

    public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
        $this->visitorAffiliateCache = $visitorAffiliateCache;
    }

    public function recognize(Pap_Contexts_Action $context) {
        if ($context->isVisitorAffiliateRecognized()) {
            return;
        }
        
        if(Gpf_Settings::get(Pap_Settings::TRACK_BY_IP_SETTING_NAME) != Gpf::YES) {
            return;
        }
        
        $ip = $context->getIp();
        $context->debug('Trying to get visitor affiliate from IP address '. $ip);

        $visitorAffiliate = $this->visitorAffiliateCache->getLatestVisitorAffiliateFromIp($ip, $context->getAccountId());
        if ($visitorAffiliate == null) {
            $context->debug("No visitor affiliate from IP '$ip'");
            return;
        }
        
        try {
            $periodInSeconds = $this->getValidityInSeconds();
        } catch (Gpf_Exception $e) {
            $context->debug($e->getMessage());
            return;
        }
        
        
        $dateFrom = new Gpf_DateTime($context->getVisitDateTime());
        $dateFrom->addSecond(-1*$periodInSeconds);
        $dateVisit = new Gpf_DateTime($visitorAffiliate->getDateVisit());

        if ($dateFrom->compare($dateVisit) > 0) {
            $context->debug("    No click from IP '$ip' found within ip validity period");
            return null;
        }

        if (!$context->isTrackingMethodSet()) {
            $context->setTrackingMethod(Pap_Common_Transaction::TRACKING_METHOD_IP_ADDRESS);
        }
        $context->debug('Visitor affiliate recognized from IP, id: '.$visitorAffiliate->getId(). ', accountId: '. $visitorAffiliate->getAccountId());
        $context->setVisitorAffiliate($visitorAffiliate);
    }

    private function getValidityInSeconds() {
        $validity = Gpf_Settings::get(Pap_Settings::IP_VALIDITY_SETTING_NAME);
        if($validity == '' || $validity == '0' || !is_numeric($validity)) {
            throw new Gpf_Exception("    IP address validity period is not correct: '$validity'");
        }
        $validityPeriod = Gpf_Settings::get(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME);
        if(!in_array($validityPeriod, array(Pap_Merchants_Config_TrackingForm::VALIDITY_DAYS,
        Pap_Merchants_Config_TrackingForm::VALIDITY_HOURS,
        Pap_Merchants_Config_TrackingForm::VALIDITY_MINUTES))) {
            throw new Gpf_Exception("    IP address validity period is not correct: '$validityPeriod'");
        }

        switch($validityPeriod) {
            case Pap_Merchants_Config_TrackingForm::VALIDITY_DAYS:
                return $validity * 86400;
                 
            case Pap_Merchants_Config_TrackingForm::VALIDITY_HOURS:
                return $validity * 3600;
                 
            case Pap_Merchants_Config_TrackingForm::VALIDITY_MINUTES:
                return $validity * 60;
                 
            default: return 0;
        }
    }
}

?>
