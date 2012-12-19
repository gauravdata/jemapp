<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Common_VisitorAffiliateCacheCompoundContext extends Gpf_Object {

    /*
     * @var Pap_Contexts_Tracking
     */
    private $context;

    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    private $visitorAffiliateCache;

    private $isVisitorAffiliateAlreadySaved = false;

    public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache = null, Pap_Contexts_Tracking $context = null){
        $this->context = $context;
        $this->visitorAffiliateCache = $visitorAffiliateCache;
    }

    /**
     * @return Pap_Contexts_Tracking
     */
    public function getContext(){
        return $this->context;
    }

    public function setContext(Pap_Contexts_Tracking $context){
        $this->context = $context;
    }


    /**
     * @return Pap_Tracking_Visit_VisitorAffiliateCache
     */
    public function getVisitorAffilliateCache() {
        return $this->visitorAffiliateCache;
    }

    public function setVisitorAffiliateAlreadySaved($value) {
        $this->isVisitorAffiliateAlreadySaved = $value;
    }

    public function getVisitorAffiliateAlreadySaved() {
        return $this->isVisitorAffiliateAlreadySaved;
    }
}

?>
