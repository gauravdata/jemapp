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
class Pap_Tracking_Common_VisitorAffiliateCheckCompatibility extends Gpf_Object {
     
    private static $instance = false;

    /**
     * @return Pap_Tracking_Common_VisitorAffiliateCheckCompatibility
     */
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Tracking_Common_VisitorAffiliateCheckCompatibility();
        }
        return self::$instance;
    }

    /**
     * Pap_Tracking_Cookie
     */
    protected $cookieObject;

    public function __construct() {
        $this->cookieObject = new Pap_Tracking_Cookie();
    }

    public function checkCompatibility($visitorId, Pap_Tracking_Visit_VisitorAffiliateCache $cache) {
        $rows = $cache->getVisitorAffiliateAllRows($visitorId);

        if ($this->isVisitorAffiliatesCollectionValid($rows)) {
            return;
        }

        if ($rows->getSize() == 1) {
            $rows->get(0)->setActual();
            return;
        }

        Pap_Tracking_Visit_VisitorAffiliateCache::sortVisitorAffiliatesByDateVisit($rows);

        $rows->correctIndexes();

        for ($i=1, $size = $rows->getSize();$i<$size-1;$i++) {
            $cache->removeVisitorAffiliate($rows->get($i)->getId());
            $rows->remove($i);
        }

        $rows->correctIndexes();

        $campaign = $this->getCampaignById($rows->get(1)->getCampaignId());
        $user = $this->getUserById($rows->get(1)->getUserId());

        if ($this->cookieObject->isOverwriteEnabled($campaign, $user)) {
            $rows->get(1)->setActual();
        } else {
            $rows->get(0)->setActual();
        }
    }

    protected function getCampaignById($campaignId) {
        $campaign = new Pap_Common_Campaign();
        $campaign->setId($campaignId);
        $campaign->load();
        return $campaign;
    }

    protected function getUserById($userId) {
        $user = new Pap_Common_User();
        $user->setId($userId);
        $user->load();
        return $user;
    }

    private function isVisitorAffiliatesCollectionValid(Pap_Tracking_Common_VisitorAffiliateCollection $rows) {
        if ($rows->getSize() == 0) {
            return true;
        }

        if ($rows->getSize() > 3) {
            return false;
        }

        foreach ($rows as $row) {
            if ($row->isActual()) {
                return true;
            }
        }

        return false;
    }
}
?>
