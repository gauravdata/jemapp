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
class Pap_Features_SplitCommissions_VisitorAffiliateCheckCompatibility extends Gpf_Object {

    private static $instance = false;

    /**
     * @return Pap_Features_SplitCommissions_VisitorAffiliateCheckCompatibility
     */
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Features_SplitCommissions_VisitorAffiliateCheckCompatibility();
        }
        return self::$instance;
    }

    /**
     * Check if visitor affiliates are in SplitCommissions format - if not convert
     */
    public function checkCompatibility($visitorId, Pap_Tracking_Visit_VisitorAffiliateCache $cache) {
        $rows = $cache->getVisitorAffiliateAllRows($visitorId);

        switch ($rows->getValidSize()) {
            case 0:
                break;
            case 1:
                $this->cloneVisitorAffiliate($rows->getValid(0), $rows, true);
                break;
            case 2:
                if ($rows->getValid(0)->getUserId() != $rows->getValid(1)->getUserId()) {
                    if ($rows->getValid(0)->getDateVisit() < $rows->getValid(1)->getDateVisit()) {
                        $this->cloneVisitorAffiliate($rows->getValid(0), $rows, false);
                    } else {
                        $this->cloneVisitorAffiliate($rows->getValid(1), $rows, false);
                    }
                }
                break;
            case 3:

                $firstClickVisitorAffiliate = $this->getFirstClickVisitorAffiliate($rows);

                if ($rows->getValid(0)->getUserId() != $rows->getValid(1)->getUserId() &&
                $rows->getValid(1)->getUserId() != $rows->getValid(2)->getUserId() &&
                $rows->getValid(0)->getUserId() != $rows->getValid(2)->getUserId() ) {
                    $this->cloneVisitorAffiliate($firstClickVisitorAffiliate, $rows);
                    return;
                }

                if ($this->checkTwoVisitorAffiliateAreSameAndRemove(0, 1, $rows, $firstClickVisitorAffiliate, $cache)) {
                    return;
                }

                if ($this->checkTwoVisitorAffiliateAreSameAndRemove(1, 2, $rows, $firstClickVisitorAffiliate, $cache)) {
                    return;
                }

                if ($this->checkTwoVisitorAffiliateAreSameAndRemove(0, 2, $rows, $firstClickVisitorAffiliate, $cache)) {
                    return;
                }
                break;
            default:
                $this->checkIfFirstVisitorAffiliateClickIsThereTwoTimes($rows);
        }

        foreach ($rows as $row) {
            $row->setActual(false);
        }
    }

    private function checkIfFirstVisitorAffiliateClickIsThereTwoTimes(Pap_Tracking_Common_VisitorAffiliateCollection $rows) {
		Pap_Tracking_Visit_VisitorAffiliateCache::sortVisitorAffiliatesByDateVisit($rows);
				
		$usersIndexes= array();
		$hasFirstTwoTimes = false;
		$firstClickVisitorAffiliate = $rows->getValid(0);		
		
        for ($i = 1; $i < $rows->getValidSize(); $i++) {		
            if ($rows->getValid($i)->getUserId() === $firstClickVisitorAffiliate->getUserId() && !$hasFirstTwoTimes) {
                $hasFirstTwoTimes = true;                
                continue;
            }     
            if (isset($usersIndexes[$rows->getValid($i)->getUserId()])) {
            	$usersIndexes[$rows->getValid($i)->getUserId()][] = $i;
            	continue;
            }
            $usersIndexes[$rows->getValid($i)->getUserId()] = array($i); 
        }
        
        $this->removeUserDuplicates($rows, $usersIndexes);
        
        if (!$hasFirstTwoTimes) {        	
        	$this->cloneVisitorAffiliate($firstClickVisitorAffiliate, $rows);
        }
    }

    /**
     * @param $rows
     * @param $usersIndexes array('userid' => array('index')); 
     */
    private function removeUserDuplicates(Pap_Tracking_Common_VisitorAffiliateCollection $rows, array $usersIndexes) {
		foreach ($usersIndexes as $user) {
        	for ($i = 1; $i < count($user); $i++) {
        		$rows->remove($user[$i]);
        	}
        }
        $rows->correctIndexes();
    }
    
    private function checkTwoVisitorAffiliateAreSameAndRemove($index1, $index2, Pap_Tracking_Common_VisitorAffiliateCollection $rows,
    Pap_Db_VisitorAffiliate $firstClickVisitorAffiliate, Pap_Tracking_Visit_VisitorAffiliateCache $cache) {
        if ($rows->getValid($index1)->getUserId() != $rows->getValid($index2)->getUserId()) {
            return false;
        }

        if ($rows->getValid($index1)->getUserId() == $firstClickVisitorAffiliate->getUserId()) {
            return false;
        }

        $cache->removeVisitorAffiliate($rows->getValid($index2)->getId());
        $rows->remove($index2);
        $rows->correctIndexes();
        
        $this->cloneVisitorAffiliate($firstClickVisitorAffiliate, $rows, false);
        return true;
    }

    private function getFirstClickVisitorAffiliate(Pap_Tracking_Common_VisitorAffiliateCollection $rows) {
        $firstClick = null;
        foreach ($rows as $row) {
            if (!$row->isValid()) {
                continue;
            }
            if ($firstClick == null) {
                $firstClick = $row;
                continue;
            }
            if ($firstClick->getDateVisit() > $row->getDateVisit()) {
                $firstClick = $row;
            }
        }
        return $firstClick;
    }

    private function cloneVisitorAffiliate(Pap_Db_VisitorAffiliate $visitorAffiliate, Pap_Tracking_Common_VisitorAffiliateCollection $rows) {
        $newVisitorAffiliate = clone $visitorAffiliate;
        $newVisitorAffiliate->setId('');
        $newVisitorAffiliate->setPersistent(false);

        $rows->add($newVisitorAffiliate);
    }
}
?>
