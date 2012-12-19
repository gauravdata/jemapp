<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
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
 * @package PostAffiliatePro
 */
class AffiliateCampaigns_Main extends Gpf_Plugins_Handler {

	public static function getHandlerInstance() {
		return new AffiliateCampaigns_Main();
	}

	public function initCampaignsVariable(Gpf_Mail_Template $mailTemplate) {
		$mailTemplate->addVariable('affiliatecampaigns', $this->_('Affiliate campaigns'));
	}

	public function setCampaignsVariable(Pap_Mail_UserMail $mailTemplate) {
	    if ($mailTemplate->getUser() == null) {
	       $mailTemplate->setVariable('affiliatecampaigns', array());
	       return;
	    }
		$affiliateCampaigns = new Pap_Common_Campaign_AffiliateCampaigns($this->getCampaigns(), $mailTemplate->getUser()->getId());
		$mailTemplate->setVariable('affiliatecampaigns', $affiliateCampaigns->getAffiliateCampaigns()->toArray());
	}
	
	/**
	 * @return Gpf_Data_RecordSet
	 */
	private function getCampaigns() {
		$campaigns = new Gpf_SqlBuilder_SelectBuilder();
		$campaigns->select->addAll(Pap_Db_Table_Campaigns::getInstance());
		$campaigns->from->add(Pap_Db_Table_Campaigns::getName());
		return $campaigns->getAllRows();
	}
}
?>
