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
class Pap_Tracking_Click_SaveVisitorAffiliate extends Gpf_Object implements Pap_Tracking_Common_Saver {

    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    protected $visitorAffiliateCache;

    private $overwriteSettings = array();

    /**
     * Pap_Tracking_Cookie
     */
    protected $cookieObject;

    public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
        $this->cookieObject = new Pap_Tracking_Cookie();
        $this->visitorAffiliateCache = $visitorAffiliateCache;
    }

    public function saveChanges() {
    }

    public function process(Pap_Contexts_Tracking $context) {
        $context->debug('Preparing for save visitor affiliate');

        $cacheCompoundContex = new Pap_Common_VisitorAffiliateCacheCompoundContext($this->visitorAffiliateCache, $context);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.click.beforeSaveVisitorAffiliate', $cacheCompoundContex);

        if ($cacheCompoundContex->getVisitorAffiliateAlreadySaved()) {
            $context->debug('VisitorAffiliate already set by plugins, not saving');
            return;
        }
        
        Pap_Tracking_Common_VisitorAffiliateCheckCompatibility::getHandlerInstance()->checkCompatibility($context->getVisitorId(), $this->visitorAffiliateCache);

        $rows = $this->visitorAffiliateCache->getVisitorAffiliateAllRows($context->getVisitorId());
        
        $context->debug('Found ' . $rows->getSize() . ' records in visitorAffiliates');
        switch ($rows->getSize()) {
            case 0:
                $visitorAffiliate = $this->createAndPrepareVisitorAffiliate($context);
                $visitorAffiliate->setActual(true);
                $rows->add($visitorAffiliate);
                $context->debug('Saving first visitorAffiliate '.$visitorAffiliate->toString());
                break;
            case 1:
                $lastVisit = $this->createAndPrepareVisitorAffiliate($context);
                if ($this->isOverWriteEnabled($context) || !$rows->get(0)->isValid()) {
                    $rows->get(0)->setActual(false);
                    $lastVisit->setActual(true);
                }
                $context->debug('Adding second visitorAffiliate '.$lastVisit->toString());
                $rows->add($lastVisit);
                break;
            case 2:
                if ($this->isOverWriteEnabled($context) || ($rows->get(0)->isActual() && !$rows->get(0)->isValid())) {
                    $rows->get(0)->setActual(false);
                    $this->prepareVisitorAffiliate($rows->get(1), $context);
                    $rows->get(1)->setActual(true);
                    $context->debug('Overwrting second visitor affilite '.$rows->get(1)->toString());
                } else {
                    if ($rows->get(1)->isActual() && $rows->get(1)->isValid()) {
                        $rows->add($this->createAndPrepareVisitorAffiliate($context));
                        $context->debug('Adding third (last) visitor affiliate '.$rows->get(1)->toString());
                    } else {
                        $this->prepareVisitorAffiliate($rows->get(1), $context);
                        $context->debug('Overwriting second visitor affiliate '.$rows->get(1)->toString());
                    }
                }
                break;
            case 3:
                if ($this->isOverWriteEnabled($context) || ($rows->get(1)->isActual() && !$rows->get(1)->isValid())) {
                    for ($i = 1; $i <=2; $i++) {
                        if ($rows->get($i)->isPersistent()) {
                            $rows->get($i)->delete();
                            $context->debug('Deleting '.$i.' visitoraffiliate ' . $rows->get($i)->toString());
                        }
                        $rows->remove($i);
                    }
                    $rows->correctIndexes();
                    $lastVisit = $this->createAndPrepareVisitorAffiliate($context);
                    $lastVisit->setActual(true);
                    $rows->add($lastVisit);
                    $context->debug('Adding third (last) visitor affiliate '.$lastVisit->toString());
                } else {
                    $this->prepareVisitorAffiliate($rows->get(2), $context);
                    $context->debug('Overwriting third (last) visitor affiliate '.$rows->get(2)->toString());
                }
                break;
            default:
                $context->error('Too many rows per visitor in visitor affiliates table');
                break;
        }

        $this->checkActualSelected($rows);

        $context->debug('Finished saving visitor affiliate');
        $context->debug('');
    }

    private function checkActualSelected(Pap_Tracking_Common_VisitorAffiliateCollection $rows) {
        $actual = false;
        foreach ($rows as $row) {
            $actual = $actual || $row->isActual();
        }
        if (!$actual) {
            $rows->get($rows->getSize()-1)->setActual();
        }
    }

    protected function createAndPrepareVisitorAffiliate(Pap_Contexts_Tracking $context) {
        $visitorAffiliate = $this->visitorAffiliateCache->createVisitorAffiliate($context->getVisitorId());
        $this->prepareVisitorAffiliate($visitorAffiliate, $context);
        return $visitorAffiliate;
    }

    public static function prepareVisitorAffiliate(Pap_Db_VisitorAffiliate $visitorAffiliate, Pap_Contexts_Tracking $context) {
        $visitorAffiliate->setUserId($context->getUserObject()->getId());

        if ($context->getBannerObject() != null) {
            $visitorAffiliate->setBannerId($context->getBannerObject()->getId());
        } else {
            $visitorAffiliate->setBannerId(null);
        }
        
        if ($context->getChannelObject() != null) {
            $visitorAffiliate->setChannelId($context->getChannelObject()->getId());
        }

        $visitorAffiliate->setCampaignId($context->getCampaignObject()->getId());
        $visitorAffiliate->setIp($context->getIp());
        $visitorAffiliate->setDateVisit($context->getDateCreated());
        $visitorAffiliate->setReferrerUrl($context->getReferrerUrl());
        $visitorAffiliate->setData1($context->getExtraDataFromRequest(1));
        $visitorAffiliate->setData2($context->getExtraDataFromRequest(2));
        $visitorAffiliate->setValidTo(self::getVisitorAffiliateValidity($context, $visitorAffiliate));
    }
    
    public static function getVisitorAffiliateValidity(Pap_Contexts_Tracking $context, Pap_Db_VisitorAffiliate $visitorAffiliate) {
        return Gpf_Common_DateUtils::addDateUnit($visitorAffiliate->getDateVisit(),
            Pap_Tracking_Cookie::getCookieLifeTimeInDays($context),
            Gpf_Common_DateUtils::DAY);
    }

    private function isOverWriteEnabled(Pap_Contexts_Click $context) {
        $key = '';
        if ($context->getCampaignObject() != null) {
            $key .= $context->getCampaignObject()->getId();
        }
        $key .= '_';
        if ($context->getUserObject() != null) {
            $key .= $context->getUserObject()->getId();
        }
         
        if (!isset($this->overwriteSettings[$key])) {
            $this->overwriteSettings[$key] =
            $this->cookieObject->isOverwriteEnabled($context->getCampaignObject(), $context->getUserObject());
        }
        return $this->overwriteSettings[$key];
    }
}

?>
