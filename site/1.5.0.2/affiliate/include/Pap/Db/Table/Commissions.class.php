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
class Pap_Db_Table_Commissions extends Gpf_DbEngine_Table {
	const ID = 'commissionid';
	const TIER = 'tier';
	const SUBTYPE = 'subtype';
	const TYPE = 'commissiontype';
	const RTYPE = 'rtype';
	const VALUE = 'commissionvalue';
	const TYPE_ID = 'commtypeid';
	const GROUP_ID = 'commissiongroupid';

	const SUBTYPE_NORMAL = 'N';
	const SUBTYPE_RECURRING = 'R';

	const FIRST_TIER = '1';
	private static $instance;

	/**
	 * @return Pap_Db_Table_Commissions
	 */
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	protected function initName() {
		$this->setName('pap_commissions');
	}

	public static function getName() {
		return self::getInstance()->name();
	}

	protected function initColumns() {
		$this->createPrimaryColumn(self::ID, 'char', 8, true);
		$this->createColumn(self::TIER, 'int', 0);
		$this->createColumn(self::SUBTYPE, 'char', 1);
		$this->createColumn(self::TYPE, 'char', 1);
		$this->createColumn(self::VALUE, 'float', 0);
		$this->createColumn(self::TYPE_ID, 'char', 8);
		$this->createColumn(self::GROUP_ID, 'char', 8);
	}

	protected function initConstraints() {
		$this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::TIER,
		self::SUBTYPE,
		self::TYPE_ID,
		self::GROUP_ID)));
	}

	/**
	 * returns all commissions for given commission type and group
	 * If $commissionTypeId is empty, it returns all commissions for this campaign and group
	 * @param String $commissionTypeId
	 * @param String $commissionGroupId
	 * @param String $multiTier
	 *
	 * @return Gpf_Data_RecordSet
	 */
	public function getAllCommissions($commissionTypeId, $commissionGroupId, $multiTier = 'N', $rtype = null) {
		$result = new Gpf_Data_RecordSet();

		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		$selectBuilder->select->add(self::ID, self::ID);
		$selectBuilder->select->add(self::TIER, 'tier');
		$selectBuilder->select->add(self::SUBTYPE, 'subtype');
		$selectBuilder->select->add(self::TYPE, 'commissiontype');
		$selectBuilder->select->add(self::VALUE, 'commissionvalue');
		$selectBuilder->select->add(self::TYPE_ID, 'commtypeid');
		$selectBuilder->select->add(self::GROUP_ID, 'commissiongroupid');
		$selectBuilder->from->add(self::getName());

		if ($commissionTypeId != '') {
			$selectBuilder->where->add(self::TYPE_ID, '=', $commissionTypeId);
		}
		if ($commissionGroupId != null) {
		    $selectBuilder->where->add(self::GROUP_ID, '=', $commissionGroupId);
		}
		if ($multiTier == Gpf::YES) {
			$selectBuilder->where->add(self::TIER, '>', '1');
		}
	    if ($rtype != null) {
            $selectBuilder->where->add(self::RTYPE, '=', $rtype);
        }
		$selectBuilder->orderBy->add(Pap_Db_Table_Commissions::TIER);

		$result->load($selectBuilder);

		return $result;
	}

	/**
	 * checks if there are any commission types and commissions defined for this campaign
	 * returns true if yes, of false
	 *
	 * @param unknown_type $campaignId
	 */
	public function checkCommissionsExistInCampaign($campaignId) {
		$result = $this->getAllCommissionsInCampaign($campaignId);
		if($result->getSize() == 0) {
			return false;
		}
		return true;
	}

	/**
	 *
	 * @param $campaignId
	 * @param $tier
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	public function getAllCommissionsInCampaignSelectBuilder($campaignId, $tier) {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID, self::ID);
        $selectBuilder->select->add(self::TYPE, self::TYPE);
        $selectBuilder->select->add(self::VALUE, self::VALUE);
        $selectBuilder->select->add('c.commtypeid', 'commtypeid');
        $selectBuilder->select->add("campaignid", "campaignid");
        $selectBuilder->select->add("ct.countrycodes", "countrycodes");
        $selectBuilder->select->add("commissiongroupid", "commissiongroupid");
        $selectBuilder->select->add("tier", "tier");
        $selectBuilder->select->add('ct.rtype', 'rtype');
        $selectBuilder->select->add('ct.'.Pap_Db_Table_CommissionTypes::NAME, 'commissionTypeName');
        $selectBuilder->from->add(self::getName(), 'c');
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionTypes::getName(), 'ct', 'c.commtypeid = ct.commtypeid');
        if($campaignId != '') {
            $selectBuilder->where->add('campaignid', '=', $campaignId);
        }
        $selectBuilder->where->add('ct.rstatus', '=', 'E');
        if($tier != '') {
            $selectBuilder->where->add('c.tier', '=', $tier);
        }
        $selectBuilder->where->add('c.subtype', '=', self::SUBTYPE_NORMAL);
        return $selectBuilder;
	}

	/**
	 *
	 * @param $campaignId
	 * @param $tier
	 * @return Gpf_Data_RecordSet
	 */
	public function getAllCommissionsInCampaign($campaignId = '', $tier = '1') {
		$result = new Gpf_Data_RecordSet();

		$selectBuilder = $this->getAllCommissionsInCampaignSelectBuilder($campaignId, $tier);
		$selectBuilder->orderBy->add(self::VALUE);

		$result->load($selectBuilder);
		return $result;
	}

	/**
	 * checks if for this campaign there is at least one active commission defined
	 *
	 * @param $campaignId
	 * @param Gpf_Data_RecordSet $rsCommissionsExist
	 * @return boolean
	 */
	public function findCampaignInCommExistsRecords($campaignId, Gpf_Data_RecordSet $rsCommissions) {
		if($rsCommissions->getSize() == 0) {
			return false;
		}

		foreach($rsCommissions as $record) {
			if($campaignId == $record->get("campaignid")) {
				return true;
			}
		}

		return false;
	}

	/**
	 * returns text description about campaign commissions
	 *
	 * @param string $campaignId
	 * @param Gpf_Data_RecordSet $rsCommissions
	 * @return string
	 */
	public function getCommissionsDescription($campaignId, Gpf_Data_RecordSet $rsCommissions, $commissionGroupId = null, $extendedFormatting = false) {
		if ($rsCommissions->getSize() == 0) {
			return $this->_('none active !');
		}

		if ($commissionGroupId == null) {
			try {
				$commissionGroupId = $this->getDefaultCommissionGroup($campaignId);
			} catch (Gpf_Exception $e) {
				return $this->_('none active');
			}
		}

		$commissions = array();
		foreach ($rsCommissions as $record) {
			if ($campaignId != $record->get("campaignid") ||
			($commissionGroupId != '' && $commissionGroupId != $record->get("commissiongroupid"))) {
				continue;
			}

			$rType = $record->get('rtype');
			$commissions[$rType]['commtype'] = $record->get(Pap_Db_Table_Commissions::TYPE);
			$commissions[$rType]['value'] = $record->get(Pap_Db_Table_Commissions::VALUE);
			switch ($rType) {
				case Pap_Common_Constants::TYPE_CPM:
					$commissionTypeName = $this->_('CPM');
					break;
				case Pap_Common_Constants::TYPE_CLICK:
					$commissionTypeName = $this->_('per click');
					break;
				case Pap_Common_Constants::TYPE_SALE:
					$commissionTypeName = $this->_('per sale / lead');
					break;
				default:
					$commissionTypeName = $record->get('commissionTypeName');
					break;
			}
			$commissions[$rType]['name'] = $commissionTypeName;
		}

		$description = '';
		if ($extendedFormatting) {
			foreach ($commissions as $rtype => $commission) {
				$description .= ($description != '' ? '<br>' : '');
				$description .= $commission['name'].': <strong>'.Pap_Common_Utils_CurrencyUtils::toStandardCurrencyFormat($commission['value'], $commission['commtype']).'</strong>';
			}
		} else {
			foreach ($commissions as $rtype => $commission) {
				$description .= ($description != '' ? ', ' : '');
				$description .= $commission['name'].': '.Pap_Common_Utils_CurrencyUtils::toStandardCurrencyFormat($commission['value'], $commission['commtype']);
			}
		}
		if($description == '') {
			$description = $this->_('none active');
		}

		return $description;
	}

	public function getDefaultCommissionGroup($campaignId) {
        return Pap_Db_Table_Campaigns::getInstance()->getDefaultCommissionGroup($campaignId)->getId();
	}

	public function deleteAllSubtypeCommissions($subType) {
		$delete = new Gpf_SqlBuilder_DeleteBuilder();
		$delete->from->add(Pap_Db_Table_Commissions::getName());
		$delete->where->add(Pap_Db_Table_Commissions::SUBTYPE, "=", $subType);
		$delete->execute();
	}

   /**
     * @return Gpf_Data_RecordSet
     */
    public function getReferralCommissions() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('c.'.Pap_Db_Table_Commissions::ID, Pap_Db_Table_Commissions::ID);
        $select->select->add('c.'.Pap_Db_Table_Commissions::TIER, Pap_Db_Table_Commissions::TIER);
        $select->select->add('c.'.Pap_Db_Table_Commissions::SUBTYPE, Pap_Db_Table_Commissions::SUBTYPE);
        $select->select->add('c.'.Pap_Db_Table_Commissions::TYPE, 'commissiontype');
        $select->select->add('c.'.Pap_Db_Table_Commissions::VALUE, 'commissionvalue');
        $select->select->add('c.'.Pap_Db_Table_Commissions::TYPE_ID, 'commtypeid');
        $select->from->add(Pap_Db_Table_Commissions::getName(), 'c');
        $select->from->addInnerJoin(Pap_Db_Table_CommissionTypes::getName(), 'ct',
            'c.'.Pap_Db_Table_Commissions::TYPE_ID.'=ct.'.Pap_Db_Table_CommissionTypes::ID);
        $select->where->add(Pap_Db_Table_CommissionTypes::TYPE, '=', Pap_Db_Transaction::TYPE_REFERRAL);
        $select->orderBy->add(Pap_Db_Table_Commissions::TIER);

        return $select->getAllRows();
    }
}
?>
