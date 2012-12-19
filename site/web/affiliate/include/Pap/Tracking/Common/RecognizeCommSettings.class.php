<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
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
class Pap_Tracking_Common_RecognizeCommSettings extends Gpf_Object implements Pap_Tracking_Common_Recognizer {

	protected $commissions = array();

	/*
	 * @var Pap_Common_UserTree
	 */
	protected $userTree;

	public function __construct() {
		$this->userTree = new Pap_Common_UserTree();
	}

	public function recognize(Pap_Contexts_Tracking $context, $customStatus = null) {
		$context->debug('Recognizing commission settings started');

		$campaign = $context->getCampaignObject();
		if($campaign == null) {
			$context->debug('    Error, campaign cannot be null!');
			return;
		}
			
		$commissionType = $context->getCommissionTypeObject();

		if ($commissionType == null) {
			$context->debug('    No commission type found for this action');
			return;
		}

		try {
			$commissionCollection = $this->getCommissionsCollection($context);
			foreach($commissionCollection as $dbCommission) {
				$commission = new Pap_Tracking_Common_Commission();
				$commission->loadFrom($dbCommission);
				$commission->setStatusFromType($commissionType);
				if ($customStatus != null) {
					$commission->setStatus($customStatus);
				}
				$context->addCommission($commission);
			}
			$context->debug('    Commission settings loaded, # of tiers: ' . $commissionCollection->getSize());
		} catch(Exception $e) {
			$context->debug('    EXCEPTION, STOPPING. Exception message: '.$e->getMessage());
			return;
		}
			
		$context->debug('Recognizing commission settings ended');
		$context->debug('');
	}

	/*
     * @return Gpf_DbEngine_Row_Collection
     */
    protected function getCommissionsCollection(Pap_Contexts_Tracking $context) {
        $tier = 1;
        $currentUser = $context->getUserObject();
        $collection = new Gpf_DbEngine_Row_Collection();

        while($currentUser != null && $tier < 100) {
            $tierCommissions = $this->getTierCommissionCollection($context, $currentUser->getId(), $tier);
                foreach ($tierCommissions as $dbCommission) {
                    $context->debug('Adding commission commissiontypeid: '.$dbCommission->get(Pap_Db_Table_Commissions::TYPE_ID).
                                ', commissiongroupid: '.$dbCommission->get(Pap_Db_Table_Commissions::GROUP_ID).
                                ', tier: '.$dbCommission->get(Pap_Db_Table_Commissions::TIER).
                                ', subtype: '.$dbCommission->get(Pap_Db_Table_Commissions::SUBTYPE));
                    $collection->add($dbCommission);
                }

            $tier++;
            $currentUser = $this->userTree->getParent($currentUser);
        }
        return $collection;
    }

    /**
     * @return Gpf_DbEngine_Row_Collection
     */
    private function getTierCommissionCollection(Pap_Contexts_Tracking $context, $userId, $tier) {
    	$context->debug('Loading tier commission collection for userid: ' . $userId . ' and tier: ' . $tier);
        $commissionTypeId = $context->getCommissionTypeObject()->getId();
        $groupId = $this->getCommissionGroupForUser($context->getCampaignObject(), $userId);
        $hash = $commissionTypeId.$groupId.$tier;

        if (isset($this->commissions[$hash])) {
        	$context->debug('Record found in cache.');
            return $this->commissions[$hash];
        }

        $context->debug('Trying to load commission for typeid:' . $commissionTypeId . ', groupId:' . $groupId . ',tier:' . $tier);
        $commission = new Pap_Db_Commission();
        $commission->setCommissionTypeId($commissionTypeId);
        $commission->setGroupId($groupId);
        $commission->setTier($tier);
        try {
            $commissions = $this->loadCommissionCollectionFromData($commission);
        } catch (Gpf_DbEngine_NoRowException $e) {
        	$context->debug('Error loading collection from data. returning empty collection.');
            return new Gpf_DbEngine_Row_Collection();
        }
        $context->debug('Commissions succ. loaded, saving to cache.');
        $this->commissions[$hash] = $commissions;
        return $this->commissions[$hash];
    }

    protected function getCommissionGroupForUser(Pap_Common_Campaign $campaign, $userId) {        
        $groupId = $campaign->getCommissionGroupForUser($userId);        
        return $groupId;
    }
    
    /**
     *
     * @return Gpf_DbEngine_Row_Collection
     */
    protected function loadCommissionCollectionFromData(Pap_Db_Commission $commission) {
        return $commission->loadCollection();
    }
}

?>
