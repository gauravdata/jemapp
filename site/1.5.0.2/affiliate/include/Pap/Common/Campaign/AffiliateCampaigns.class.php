<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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
class Pap_Common_Campaign_AffiliateCampaigns extends Gpf_Object {

	/**
	 * @var Gpf_Data_RecordSet
	 */
	private $campaigns;
	/**
	 * @var Gpf_Data_RecordSet
	 */
	protected $affiliateCampaigns;
	private $campaignIDColumnName;
	private $affiliateID;

	/**
	 * $campaigns recordset must contains columns (campaignid, rstatus, rtype). Campaign ID column can be defined in $campaignIDColumnName.
	 * 
	 * @param Gpf_Data_RecordSet $campaigns
	 * @param $affiliateID
	 * @param $campaignIDColumnName	 
	 */
	public function __construct(Gpf_Data_RecordSet $campaigns, $affiliateID, $campaignIDColumnName = 'campaignid') {		
		$this->campaigns = $campaigns;
		$this->campaignIDColumnName = $campaignIDColumnName;
		$this->affiliateID = $affiliateID;
		$this->initAffiliateCampaigns();
	}

	/**
	 * @return Gpf_Data_RecordSet
	 */
	public function getAffiliateCampaigns() {
		$cTable = Pap_Db_Table_Commissions::getInstance();
		$rsCommissions = $cTable->getAllCommissionsInCampaign('', '');

		foreach ($this->campaigns as $campaign) {
			$status = $campaign->get(Pap_Db_Table_Campaigns::STATUS);
			if(!in_array($status, Pap_Affiliates_Promo_Campaigns::getCampaignAllowedStatuses())) {
				continue;
			}

			if ($cTable->findCampaignInCommExistsRecords($campaign->get($this->campaignIDColumnName), $rsCommissions)) {
				if ($this->isAffiliateInCampaign($campaign)) {
					$this->affiliateCampaigns->addRecord($campaign);
				}
			}
		}

		return $this->affiliateCampaigns;
	}

	protected function initAffiliateCampaigns() {
		$this->affiliateCampaigns = new Gpf_Data_RecordSet();
		$this->affiliateCampaigns->setHeader($this->campaigns->getHeader()->toArray());
	}
	
	protected function insertAffiliateCampaign(Gpf_Data_Record $campaign) {
		$this->affiliateCampaigns->addRecord($campaign);
	}
	
	/**
	 * @param Gpf_Data_Record $campaign
	 * @return boolean
	 */
	private function isAffiliateInCampaign(Gpf_Data_Record $campaign) {
		try {
			Pap_Db_Table_UserInCommissionGroup::getStatus($campaign->get($this->campaignIDColumnName), $this->affiliateID);
		} catch (Gpf_DbEngine_NoRowException $e) {
			if ($campaign->get(Pap_Db_Table_Campaigns::TYPE) == Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION) {
				return false;
			}
		}
		return true;
	}
}
