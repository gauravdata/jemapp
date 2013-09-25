<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: UserAttribute.class.php 20743 2008-09-08 15:06:38Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Db_CampaignAttribute extends Gpf_DbEngine_Row {

	function init() {
		$this->setTable(Pap_Db_Table_CampaignAttributes::getInstance());
		parent::init();
	}

	public function setCampaignId($campaignId) {
		$this->set(Pap_Db_Table_CampaignAttributes::CAMPAIGN_ID, $campaignId);
	}

	public function setName($name) {
		$this->set(Pap_Db_Table_CampaignAttributes::NAME, $name);
	}

	public function setValue($value) {
		$this->set(Pap_Db_Table_CampaignAttributes::VALUE, $value);
	}

	public function getCampaignId() {
		return $this->get(Pap_Db_Table_CampaignAttributes::CAMPAIGN_ID);
	}

	public function getName() {
		return $this->get(Pap_Db_Table_CampaignAttributes::NAME);
	}

	public function getValue() {
		return $this->get(Pap_Db_Table_CampaignAttributes::VALUE);
	}

	/**
	 * @throws Gpf_DbEngine_TooManyRowsException
	 * @throws Gpf_DbEngine_NoRowException
	 *
	 * @param $name
	 * @param $campaignId
	 * @return $value
	 */
	public function getSetting($name, $campaignId) {
		$this->setName($name);
		$this->setCampaignId($campaignId);
		$this->loadFromData(array(Pap_Db_Table_CampaignAttributes::NAME, Pap_Db_Table_CampaignAttributes::CAMPAIGN_ID));
		return $this->getValue();
	}

	/**
	 * @throws Gpf_DbEngine_TooManyRowsException
	 */
	public function save() {
		try {
			$attribute = new Pap_Db_CampaignAttribute();
			$attribute->getSetting($this->getName(), $this->getCampaignId());
			$this->setPrimaryKeyValue($attribute->getPrimaryKeyValue());
			$this->update();
		} catch (Gpf_DbEngine_NoRowException $e) {
			$this->insert();
		}
	}
}

?>
