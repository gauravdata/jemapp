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
class Pap_Tracking_Common_RecognizeCommGroup extends Gpf_Object implements Pap_Tracking_Common_Recognizer  {

	private $commissionsGroup = array();

	private $userCommissionGroup = array();
	
	public function recognize(Pap_Contexts_Tracking $context) {
		return $this->getCommissionGroup($context);
	}

	/**
	 * returns commission group for user (if not set already)
	 * Commission group can be set previously in the checkCampaignType() function
	 *
	 */
	protected function getCommissionGroup(Pap_Contexts_Tracking $context) {
		$context->debug('Recognizing commission group started');

		if (($user = $context->getUserObject()) == null) {
		    $context->debug('STOPPING, user is not set - cannot find commission group');
		    return;
		}
		
		$commGroupId = $this->getUserCommissionGroupFromCache($context->getCampaignObject(), $user->getId());
		if($commGroupId == false) {
			$context->debug("STOPPING, Cannot find commission group for this affiliate and campaign! ".$context->getCampaignObject()->getId().' - '.$user->getId());
			$context->setDoCommissionsSave(false);
			$context->setDoTrackerSave(false);
			return;
		}
			
		Gpf_Plugins_Engine::extensionPoint('PostAffiliate.RecognizeCommGroup.getCommissionGroup', $context);

        $commissionGroup = $this->getCommissionGroupFromCache($commGroupId);
        if ($commissionGroup == null) {
        	$context->debug('    Commission group with ID '.$commGroupId . ' does not exist');
        	return;
        }

		$context->setCommissionGroup($commissionGroup);
	    $context->debug('Received commission group ID = '.$commGroupId);
	}
	
	private function getUserCommissionGroupFromCache(Pap_Common_Campaign $campaign, $userId) {
		if (isset($this->userCommissionGroup[$campaign->getId()][$userId])) {
			return $this->userCommissionGroup[$campaign->getId()][$userId];
		}
		$userCommissionGroup = $campaign->getCommissionGroupForUser($userId);
		$this->userCommissionGroup[$campaign->getId()][$userId] = $userCommissionGroup;
		return $userCommissionGroup;
	}

	protected function getCommissionGroupFromCache($commGroupId) {
		if (isset($this->commissionsGroup[$commGroupId])) {
			return $this->commissionsGroup[$commGroupId];
		}

		$commissionGroup = new Pap_Db_CommissionGroup();
		$commissionGroup->setPrimaryKeyValue($commGroupId);
		try {
			$commissionGroup->load();
			$this->commissionsGroup[$commGroupId] = $commissionGroup;
			return $commissionGroup;
		} catch (Gpf_DbEngine_NoRowException $e) {
		}
		return null;
	}
}

?>
