<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
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
class Pap_Tracking_Visit_VisitorAffiliateCache {
	protected $visitorAffiliateCollections = array();

	private $removeVisitorAffiliateIds = array();

	private $accountId;

	public function __construct() {
		$this->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
	}
//TODO: rewrite this class to all public methods have accountid parameter and rmeove set/get account methods
	public function setAccountId($accountId) {
		$this->accountId = $accountId;
		if (is_null($this->accountId) || $this->accountId === '') {
			$this->accountId = Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
		}
		if (!isset($this->visitorAffiliateCollections[$this->accountId])) {
			$this->visitorAffiliateCollections[$this->accountId] = array();
		}
	}
	
	public function getAccountId() {
		return $this->accountId;
	}

	/**
	 * @return Pap_Tracking_Common_VisitorAffiliateCollection
	 */
	public static function sortVisitorAffiliatesByDateVisit(Pap_Tracking_Common_VisitorAffiliateCollection $visitorAffiliates) {
		$visitorAffiliates->sort(array("Pap_Tracking_Visit_VisitorAffiliateCache", "compareTwoVisitorAffiliateByDateVisit"));
	}

	public static function compareTwoVisitorAffiliateByDateVisit(Pap_Db_VisitorAffiliate $a,
	Pap_Db_VisitorAffiliate $b) {
		if ($a->getDateVisit() == $b->getDateVisit()) {
			return 0;
		}
		return ($a->getDateVisit() > $b->getDateVisit()) ? +1 : -1;
	}

	public function removeVisitorAffiliate($visitorAffiliateId) {
		$this->removeVisitorAffiliateIds[] = $visitorAffiliateId;
	}

	public function getVisitorAffiliateCollections() {
		return $this->visitorAffiliateCollections[$this->accountId];
	}

	/**
     * @return Pap_Tracking_Common_VisitorAffiliateCollection
     */
    public function getVisitorAffiliateAllRows($visitorId) {
        if ($visitorId == '') {
            throw new Gpf_Exception('visitorId can not be empty in Pap_Tracking_Visit_VisitorAffiliateCache::getVisitorAffiliateAllRows()');
        }

        if (!isset($this->visitorAffiliateCollections[$this->accountId][$visitorId])) {
            Gpf_Log::debug('VisitorAffiliate not found in cache, loading from DB');			
			$this->visitorAffiliateCollections[$this->accountId][$visitorId] = $this->loadVisitorAffiliatesFromDb($visitorId);
			Gpf_Log::debug('Saving collection to cache for visitorid=' . $visitorId . ', num rows=' . $this->visitorAffiliateCollections[$this->accountId][$visitorId]->getSize());
		}		
		return $this->visitorAffiliateCollections[$this->accountId][$visitorId];
    }

	/**
	 * @param Pap_Context_Tracking
	 * @return Pap_Db_VisitorAffiliate
	 */
	public function getActualVisitorAffiliate($visitorId) {
		foreach ($this->getVisitorAffiliateAllRows($visitorId) as $visitorAffiliate) {
			if ($visitorAffiliate->isActual() && $visitorAffiliate->isValid()) {
				return $visitorAffiliate;
			}
		}
		return null;
	}

	/**
	 * @param visitorId
	 * @return Pap_Db_VisitorAffiliate
	 */
	public function createVisitorAffiliate($visitorId) {
		$visitorAffiliate = new Pap_Db_VisitorAffiliate();
		$visitorAffiliate->setVisitorId($visitorId);
		$visitorAffiliate->setAccountId($this->accountId);
		return $visitorAffiliate;
	}

	/**
	 * @param $ip
	 * @return Pap_Db_VisitorAffiliate
	 */
	public function getLatestVisitorAffiliateFromIp($ip, $accountId) {
		$cacheVisitorAffiliate = $this->getLatestAffiliateFromCollectionByIp($this->getVisitorAffiliateCollections(), $ip);

		$dbVisitorAffiliate = $this->getLatestVisitorAffiliateFromDbByIp($ip, $accountId);

		if ($cacheVisitorAffiliate == null) {
			return $dbVisitorAffiliate;
		}

		if ($dbVisitorAffiliate == null) {
			return $cacheVisitorAffiliate;
		}

		if ($dbVisitorAffiliate->getDateVisit() <
		$cacheVisitorAffiliate->getDateVisit()) {
			return $cacheVisitorAffiliate;
		}
		return $dbVisitorAffiliate;
	}

	/**
	 * @param $ip
	 * @return Pap_Db_VisitorAffiliate
	 */
	protected function getLatestVisitorAffiliateFromDbByIp($ip, $accountId) {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		$selectBuilder->from->add(Pap_Db_Table_VisitorAffiliates::getName());
		$selectBuilder->select->addAll(Pap_Db_Table_VisitorAffiliates::getInstance());
		$selectBuilder->where->add(Pap_Db_Table_VisitorAffiliates::IP, '=', $ip);
		$selectBuilder->where->add(Pap_Db_Table_VisitorAffiliates::ACCOUNTID, '=', $accountId);
		$selectBuilder->where->add(Pap_Db_Table_VisitorAffiliates::VALIDTO, '>=', Gpf_Common_DateUtils::now());
		$selectBuilder->orderBy->add(Pap_Db_Table_VisitorAffiliates::DATEVISIT, false);
		$selectBuilder->limit->set(0, 1);

		try {
			$visitorAffiliate = new Pap_Db_VisitorAffiliate();
			$visitorAffiliate->fillFromRecord($selectBuilder->getOneRow());
		} catch (Gpf_Exception $e) {
			return null;
		}
		 
		return $visitorAffiliate;
	}


	public function saveChanges() {
        Gpf_Log::debug('Saving visitor affiliate cache.');
		foreach ($this->visitorAffiliateCollections as $accountVisitorAffiliates) {
			foreach ($accountVisitorAffiliates as $visitorAffiliates) {
				foreach ($visitorAffiliates as $visitorAffiliate) {
                    $visitorAffiliate->save();
					Gpf_Log::debug('Saved visitor affiliate, visitorId: '. $visitorAffiliate->getVisitorId().', userId: ' . $visitorAffiliate->getUserId());
				}
			}
		}

		$this->deleteVisitorAffiliatesFromDb();
	}

	private function deleteVisitorAffiliatesFromDb() {
		if (count($this->removeVisitorAffiliateIds) == 0) {
			return;
		}

		$deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
		$deleteBuilder->from->add(Pap_Db_Table_VisitorAffiliates::getName());
		foreach ($this->removeVisitorAffiliateIds as $id) {
			$deleteBuilder->where->add(Pap_Db_Table_VisitorAffiliates::ID, '=', $id, 'OR');
		}
		$deleteBuilder->execute();
	}

	/**
	 * @return Pap_Tracking_Common_VisitorAffiliateCollection
	 */
	protected function loadVisitorAffiliatesFromDb($visitorId) {
		$visitorAffiliates = $this->createVisitorAffiliate($visitorId);
		return $visitorAffiliates->loadCollection();
	}

	protected function getLatestAffiliateFromCollectionByIp($collections, $ip) {
		$latestVisitorAffiliate = null;

		foreach ($collections as $visitorAffiliateCollection) {
			foreach ($visitorAffiliateCollection as $visitorAffiliate) {
				if ($visitorAffiliate->getIp() == $ip) {
					if (($latestVisitorAffiliate == null ||
					$latestVisitorAffiliate->getDateVisit() < $visitorAffiliate->getDateVisit()) && $visitorAffiliate->isValid()) {
						$latestVisitorAffiliate = $visitorAffiliate;
					}
				}
			}
		}

		return $latestVisitorAffiliate;
	}
}

?>
