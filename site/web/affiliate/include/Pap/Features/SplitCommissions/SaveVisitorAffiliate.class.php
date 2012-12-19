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

class Pap_Features_SplitCommissions_SaveVisitorAffiliate extends Gpf_Object  {
    private static $instance = false;

    /**
     * @return Pap_Features_SplitCommissions_SaveVisitorAffiliate
     */
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Features_SplitCommissions_SaveVisitorAffiliate();
        }
        return self::$instance;
    }


    public function saveVisitorAffiliate(Pap_Common_VisitorAffiliateCacheCompoundContext $cacheCompoundContext) {
        $cacheCompoundContext->setVisitorAffiliateAlreadySaved(true);

        $context = $cacheCompoundContext->getContext();
        $cache = $cacheCompoundContext->getVisitorAffilliateCache();
        $context->debug('Saving VisitorAffiliate by SplitCommissions started');

        Pap_Features_SplitCommissions_VisitorAffiliateCheckCompatibility::getHandlerInstance()->checkCompatibility($context->getVisitorId(), $cache);

        $rows = $cache->getVisitorAffiliateAllRows($context->getVisitorId());
        $oldVisitorAffiliate = null;

        $lastClickVisitorAffiliate = null;
        $firstClickVisitorAffiliate = null;

        foreach ($rows as $row) {
            if (!$row->isValid()) {
                continue;
            }
            if ($firstClickVisitorAffiliate == null ||
            $firstClickVisitorAffiliate->getDateVisit() > $row->getDateVisit()) {
                $firstClickVisitorAffiliate = $row;
            }

            if ($lastClickVisitorAffiliate == null ||
            $lastClickVisitorAffiliate->getDateVisit() < $row->getDateVisit()) {
                $lastClickVisitorAffiliate = $row;
            }

            if ($row->isActual()) {
                $row->setActual(false);
            }
        }

        $oldVisitorAffiliate = $this->findAlreadyStoredVisitorAffiliate($rows, $firstClickVisitorAffiliate, $context);

        if ($firstClickVisitorAffiliate == null) {
            $firstVisitorAffiliate = $cache->createVisitorAffiliate($context->getVisitorId());
            Pap_Tracking_Click_SaveVisitorAffiliate::prepareVisitorAffiliate($firstVisitorAffiliate, $context);
            $rows->add($firstVisitorAffiliate);

            $visitorAffiliate = $cache->createVisitorAffiliate($context->getVisitorId());
        } else {
            $visitorAffiliate = $this->getVisitorAffiliate($firstClickVisitorAffiliate,
            $oldVisitorAffiliate, $cache, $context);

            if ($oldVisitorAffiliate == null && 
            $visitorAffiliate === $firstClickVisitorAffiliate) {
                Pap_Tracking_Click_SaveVisitorAffiliate::prepareVisitorAffiliate($visitorAffiliate, $context);
                $visitorAffiliate = $cache->createVisitorAffiliate($context->getVisitorId()); 
            }
        }

        if ($visitorAffiliate == null) {
            $context->debug('SplitCommission VisitorAffiliate no updated - not actual data');
            return;
        }

        Pap_Tracking_Click_SaveVisitorAffiliate::prepareVisitorAffiliate($visitorAffiliate, $context);

        if ($oldVisitorAffiliate == null && $firstClickVisitorAffiliate !== $visitorAffiliate) {
            $rows->add($visitorAffiliate);
        }

        $context->debug('New visitorAffiliate added/saved by SplitCommissions');
    }

    /**
     * @return Pap_Db_VisitorAffiliate
     */
    private function findAlreadyStoredVisitorAffiliate(Pap_Tracking_Common_VisitorAffiliateCollection $rows,
    Pap_Db_VisitorAffiliate $firstClickVisitorAffiliate = null,
    Pap_Contexts_Tracking $context) {
        foreach ($rows as $row) {
            if (!$row->isValid()) {
                continue;
            }
            if ($context->getUserObject()->getId() == $row->getUserId() &&
            $row !== $firstClickVisitorAffiliate) {
                $context->debug('VisitorAffiliate with affiliate already exist');
                return $row;
            }
        }
        return null;
    }


    private function getVisitorAffiliate(Pap_Db_VisitorAffiliate $firstClickVisitorAffiliate,
    Pap_Db_VisitorAffiliate $oldVisitorAffiliate = null, Pap_Tracking_Visit_VisitorAffiliateCache $cache,
    Pap_Contexts_Tracking $context) {

        if ($firstClickVisitorAffiliate->getDateVisit() > $context->getDateCreated()) {
            return $firstClickVisitorAffiliate;
        }

        if ($oldVisitorAffiliate == null) {
            $visitorAffiliate = $cache->createVisitorAffiliate($context->getVisitorId());
            $visitorAffiliate->setDateVisit($context->getDateCreated());
            return $visitorAffiliate;
        }

        if ($oldVisitorAffiliate->getDateVisit() < $context->getDateCreated()) {
            return $oldVisitorAffiliate;
        }

        return null;
    }
}
?>
