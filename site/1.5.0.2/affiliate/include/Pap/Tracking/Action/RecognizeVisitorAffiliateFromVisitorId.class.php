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
class Pap_Tracking_Action_RecognizeVisitorAffiliateFromVisitorId extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    private $visitorAffiliateCache;

    public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
        $this->visitorAffiliateCache = $visitorAffiliateCache;
    }

    public function recognize(Pap_Contexts_Tracking $context) {
        if ($context->isVisitorAffiliateRecognized()) {
            return;
        }

        Pap_Tracking_Common_VisitorAffiliateCheckCompatibility::getHandlerInstance()->checkCompatibility($context->getVisitorId(), $this->visitorAffiliateCache);
        
        $context->debug('Getting VisitorAffiliate for visitorId = ' . $context->getVisitorId());
        if (($visitorAffiliate = $this->visitorAffiliateCache->getActualVisitorAffiliate($context->getVisitorId())) == null) {
            $context->debug('Recognize VisitorAffiliate not recognized from actual');
            return;
        }
        
        $context->debug('Recognize VisitorAffiliate recognized from actual, id: '.$visitorAffiliate->getId(). ', accountId: '. $visitorAffiliate->getAccountId());
        $context->setVisitorAffiliate($visitorAffiliate);
    }
}

?>
