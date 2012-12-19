<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik, Michal Bebjak
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
class Pap_Tracking_BackwardCompatibility_BackwardCompatibilityProcessor extends Gpf_Object implements Pap_Tracking_Common_VisitProcessor {
     
    /**
     * @var Pap_Tracking_Cookie_ClickData
     */
    protected $firstClickCookie = null;
    /**
     * @var Pap_Tracking_Cookie_ClickData
     */
    protected $lastClickCookie = null;
    /**
     * @var Pap_Tracking_Cookie_Sale
     */
    protected $saleCookie = null;

    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    private $visitorAffiliateCache;
	
    /**
     * @var Pap_Tracking_BackwardCompatibility_RecognizeAffiliate
     */
    protected $recognizeAffiliate;

    /**
     * @var Pap_Tracking_BackwardCompatibility_RecognizeCampaign
     */
    private $recognizeCampaign;

    /**
     * @var Pap_Tracking_Common_RecognizeCommGroup
     */
    private $recognizeCommGroup;
    
    /**
     * @var Pap_Contexts_BackwardCompatibility
     */
    private $context;

    public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
        $this->visitorAffiliateCache = $visitorAffiliateCache;
        $this->context = new Pap_Contexts_BackwardCompatibility();
        
        $this->recognizeAffiliate = new Pap_Tracking_BackwardCompatibility_RecognizeAffiliate();
        $this->recognizeCampaign = new Pap_Tracking_BackwardCompatibility_RecognizeCampaign();
        $this->recognizeCommGroup = new Pap_Tracking_Common_RecognizeCommGroup();
    }

    public function saveChanges() {
    }

    public function process(Pap_Db_Visit $visit) {
        $visitorId = $visit->getVisitorId();
        $this->logMessage('Backward compatibility processor ('.$visitorId.') - started');

        if (!$visit->isNewVisitor()) {
            $this->logMessage('Not new visitor ('.$visitorId.') - stopped');
            return;
        }

        if ($visit->getCookies() == '') {
            $this->logMessage('Not old cookie ('.$visitorId.') - stopped');
            return;
        }
        
        $this->visitorAffiliateCache->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
        $visitorAffiliates = $this->visitorAffiliateCache->getVisitorAffiliateAllRows($visitorId);

        $this->loadCookies($visit);

        if ($this->firstClickCookie !== null) {
            $this->logMessage('Processing first click cookie');
            $visitorAffiliates->add($this->createVisitorAffiliate($this->firstClickCookie, $visit));
            $visit->setNewVisitor(false);
        }

        if ($this->lastClickCookie !== null) {
            if ($this->firstClickCookie == null ||
            !$this->lastClickCookie->equals($this->firstClickCookie)) {
                $this->logMessage('Processing last click cookie');
                $visitorAffiliates->add($this->createVisitorAffiliate($this->lastClickCookie, $visit));
                $visit->setNewVisitor(false);
            }
        }

        if ($this->saleCookie !== null) {
            $this->logMessage('Processing sale cookie - creating visitor affiliate');
            $saleVisitorAffiliate = $this->createVisitorAffiliateFromSale($this->saleCookie, $visit);
            $this->addSaleToVisitorAffiliates($saleVisitorAffiliate, $visitorAffiliates);
            $visit->setNewVisitor(false);
        }

        $this->logMessage('Backward compatibility - finished');
    }

    private function addSaleToVisitorAffiliates(Pap_Db_VisitorAffiliate $saleVisitorAffiliate, Gpf_DbEngine_Row_Collection $visitorAffiliates) {
        $iterator = $visitorAffiliates->getIterator();
        while ($iterator->valid()) {
            $visitorAffiliate = $iterator->current();
            if ($this->isSameVisitorAffiliates($saleVisitorAffiliate, $visitorAffiliate)) {
                $visitorAffiliate->setActual(Pap_Db_VisitorAffiliate::TYPE_ACTUAL);
                return;
            }
            $iterator->next();
        }

        if ($visitorAffiliates->getSize() == 1 && $this->firstClickCookie != null) {
            $visitorAffiliates->add($saleVisitorAffiliate);
            return;
        }

        $visitorAffiliates->insert($visitorAffiliates->getSize() - 1, $saleVisitorAffiliate);
    }

    private function isSameVisitorAffiliates(Pap_Db_VisitorAffiliate $saleVisitorAffiliate, Pap_Db_VisitorAffiliate $clickVisitorAffilaite) {
        return ($saleVisitorAffiliate->getUserId() == $clickVisitorAffilaite->getUserId() &&
        $saleVisitorAffiliate->getCampaignId() == $clickVisitorAffilaite->getCampaignId() &&
        $saleVisitorAffiliate->getChannelId() == $clickVisitorAffilaite->getChannelId());
    }

    private function createVisitorAffiliateFromSale(Pap_Tracking_Cookie_Sale $saleCookie, Pap_Db_Visit $visit) {
        $visitorAffiliate = $this->visitorAffiliateCache->createVisitorAffiliate($visit->getVisitorId());
        $visitorAffiliate->setUserId($saleCookie->getAffiliateId());
        $visitorAffiliate->setCampaignId($saleCookie->getCampaignId());
        $visitorAffiliate->setChannelId($saleCookie->getChannelId());
        $visitorAffiliate->setActual(Pap_Db_VisitorAffiliate::TYPE_ACTUAL);
        $visitorAffiliate->setDateVisit(Gpf_Common_DateUtils::now());
        $this->setVisitorAffiliateValidity($visitorAffiliate);
        return $visitorAffiliate;
    }

    protected function createVisitorAffiliate(Pap_Tracking_Cookie_ClickData $clickCookie, Pap_Db_Visit $visit) {
        $visitorAffiliate = $this->visitorAffiliateCache->createVisitorAffiliate($visit->getVisitorId());
        $visitorAffiliate->setBannerId($clickCookie->getBannerId());
        try {
            $click = $clickCookie->getClick();
            $visitorAffiliate->setUserId($click->getUserId());
            $visitorAffiliate->setCampaignId($click->getCampaignId());
        } catch (Gpf_Exception $e) {
        }
        $visitorAffiliate->setChannelId($clickCookie->getChannelId());
        $visitorAffiliate->setIp($this->getIp($clickCookie, $visit));
        $visitorAffiliate->setDateVisit($this->getDateVisit($clickCookie));
        $visitorAffiliate->setReferrerUrl($this->getReferrerUrl($clickCookie, $visit));
        $visitorAffiliate->setData1($clickCookie->getData1());
        $visitorAffiliate->setData2($clickCookie->getData2());
        $this->setVisitorAffiliateValidity($visitorAffiliate);
        return $visitorAffiliate;
    }
    
    protected function getIp(Pap_Tracking_Cookie_ClickData $clickCookie, Pap_Db_Visit $visit) {
        if ($clickCookie->getIp() !== null && $clickCookie->getIp() !== '') {
            return $clickCookie->getIp();
        }
        
        return $visit->getIp();
    }
    
    protected function getReferrerUrl(Pap_Tracking_Cookie_ClickData $clickCookie, Pap_Db_Visit $visit) {
        if ($clickCookie->getReferrerUrl() != null) {
            return $clickCookie->getReferrerUrl();
        }
        
        return $visit->getReferrerUrl();
    }
    
    protected function getDateVisit(Pap_Tracking_Cookie_ClickData $clickCookie) {
        if ($clickCookie->getTimestamp() != null) {
            return Gpf_Common_DateUtils::getDateTime($clickCookie->getTimestamp());
        }
        
        if ($this->lastClickCookie->getTimestamp() != null) {
            return Gpf_Common_DateUtils::getDateTime($this->lastClickCookie->getTimestamp());
        }
        
        return Gpf_Common_DateUtils::now();
    }
    
    private function setVisitorAffiliateValidity(Pap_Db_VisitorAffiliate $visitorAffiliate) {
        $this->context->setVisitorAffiliate($visitorAffiliate);
        $this->recognizeAffiliate->recognize($this->context);
        $this->recognizeCampaign->recognize($this->context);
        $this->recognizeCommGroup->recognize($this->context);
        $visitorAffiliate->setValidTo(Pap_Tracking_Click_SaveVisitorAffiliate::getVisitorAffiliateValidity($this->context, $visitorAffiliate));
    }

    protected function loadCookies(Pap_Db_Visit $visit) {
        $cookiesArray = array();
        $args = explode('||', ltrim($visit->getCookies(), '|'));;
        foreach ($args as $arg) {
            $parsedParams = explode('=', $arg);
            if (count($parsedParams)>=2) {
                list($argName, $argValue) = $parsedParams;
                if ($argValue != '') {
                    $cookiesArray[$argName] = urldecode($argValue);
                }
            }
        }

        $cookies = new Pap_Tracking_Cookie($cookiesArray);
        try {
        	$this->firstClickCookie = $this->getClickCookie($cookies->getFirstClickCookie());
        } catch (Pap_Tracking_Exception $e) {
            $this->logMessage($e->getMessage());
        }
        try {
            $this->lastClickCookie = $this->getClickCookie($cookies->getLastClickCookie());
        } catch (Pap_Tracking_Exception $e) {
            $this->logMessage($e->getMessage());
        }
        try {
            $this->saleCookie = $this->getSaleCookie($cookies->getSaleCookie());
        } catch (Pap_Tracking_Exception $e) {
            $this->logMessage($e->getMessage());
        }
    }
        
    /**
     *
     * @param $saleCookie
     * @return Pap_Tracking_Cookie_Sale
     */
    protected function getSaleCookie(Pap_Tracking_Cookie_Sale $saleCookie = null) {
        if ($saleCookie === null) {
            return null;
        }
        $this->logMessage('Sale cookie not null, affiliateid=' . $saleCookie->getAffiliateId() . ', campaignid=' . $saleCookie->getCampaignId());
        if ($this->isClickDataValid($saleCookie->getAffiliateId(), $saleCookie->getCampaignId())) {
            $this->logMessage('Sale cookie valid, user and campaign exists.');
        	return $saleCookie;
        }
        $this->logMessage('Sale cookie not valid, user or campaign probably does not exists.');
        return null;
    }
    
    /**
     *
     * @param $clickData
     * @return Pap_Tracking_Cookie_ClickData
     */
    protected function getClickCookie(Pap_Tracking_Cookie_ClickData $clickData) {
    	try {
        	if ($this->isClickDataValid($clickData->getClick()->getUserId(), $clickData->getClick()->getCampaignId())) {
        		return $clickData;
        	}
    	} catch (Gpf_Exception $e) {
    	}
        return null;
    }

    private function logMessage($msg) {
        $this->context->debug($msg);
    }
    
    /**
     * @param $campaignId
     * @param $clickData
     * @return boolean
     */
    private function isClickDataValid($userId, $campaignId) {
    	return $this->isExistsUser($userId) && $this->isExistsCampaign($campaignId);
    }
    
    protected function isExistsUser($userId) {
        if (is_null($this->recognizeAffiliate->getUserById($this->context, $userId))) {
            $this->logMessage('User ' . $userId . ' not found!');
            return false;
        }
        return true;
    }
    
    protected function isExistsCampaign($campaignId) {
        try {
    		$this->recognizeCampaign->getCampaignById($this->context, $campaignId);
    	} catch (Gpf_Exception $e) {
    	    $this->logMessage('Campaign ' . $campaignId . ' not found! ' . $e->getMessage());
    		return false;
    	}
    	return true;
    }
}
?>
