<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
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

class Pap3Compatibility_Migration_TaskCampaigns extends Gpf_Object {

	protected $pap3CampaignSettings = array();
	protected $pap3CommissionTypes = array();

	private $countGroups = 0;
	private $countCommTypes;
	private $countCommissions = 0;

    public function run() {
    	Pap3Compatibility_Migration_OutputWriter::logOnce("Migrating campaigns<br/>");
    	$time1 = microtime();

    	try {
    		$this->loadCampaignSettings();
    		$this->migrateCampaigns();
    		$this->insertReferralCommissionType();
    		$this->migrateGroupsTypesCommissions();
    		$this->createDefaultCommissionTypes();
    		$this->migrateUsersInCampaigns();
    	} catch(Exception $e) {
    		Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Errror: ".$e->getMessage()."<br/>");
    	}

    	$time2 = microtime();
		Pap3Compatibility_Migration_OutputWriter::logDone($time1, $time2);
    }
    
    protected function createDefaultCommissionTypes() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_Campaigns::getInstance());
        $selectBuilder->from->add(Pap_Db_Table_Campaigns::getName());
        $result = $selectBuilder->getAllRows();
        
        foreach($result as $record) {
            $campaign = new Pap_Common_Campaign();
            $campaign->setId($record->get(Pap_Db_Table_Campaigns::ID));
            $campaign->load();
            try {
                $campaign->getCommissionTypeObject(Pap_Common_Constants::TYPE_CLICK);
            } catch (Pap_Tracking_Exception $e) {
                $campaign->insertCommissionType(Pap_Common_Constants::TYPE_CLICK);
            }
            try {
                $campaign->getCommissionTypeObject(Pap_Common_Constants::TYPE_SALE);
            } catch (Pap_Tracking_Exception $e) {
                $campaign->insertCommissionType(Pap_Common_Constants::TYPE_SALE);
            }
        }
    }

    private function insertReferralCommissionType() {
        $commissionType = new Pap_Db_CommissionType();
        $commissionType->setType(Pap_Common_Constants::TYPE_REFERRAL);
        $commissionType->setStatus(Pap_Db_CommissionType::STATUS_ENABLED);
        $commissionType->setApproval(Pap_Db_CommissionType::APPROVAL_AUTOMATIC);
        $commissionType->setZeroOrdersCommission(Gpf::NO);
        $commissionType->setSaveZeroCommission(Gpf::NO);
        try {
            $commissionType->insert();
        } catch (Gpf_DbEngine_Row_ConstraintException $e) {
        }
    }

    protected function loadCampaignSettings() {
    	Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Loading PAP3 campaign settings.....");

    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('code', 'code');
        $selectBuilder->select->add('value', 'value');
        $selectBuilder->select->add('id1', 'campaignid');

        $selectBuilder->from->add('wd_g_settings');

        $selectBuilder->where->add('code', 'LIKE', 'Aff_camp_%');
        $selectBuilder->where->add('id1', '!=', 'null');

        $result = new Gpf_Data_RecordSet();
        $result->load($selectBuilder);

        $count = 0;
        foreach($result as $record) {
			$this->pap3CampaignSettings[$record->get('campaignid')][$record->get('code')] = $record->get('value');
        	$count++;
        }

    	Pap3Compatibility_Migration_OutputWriter::log(" ($count) ..... DONE<br/>");
    }

    private function getPap3Setting($campaignId, $code) {
    	if(!isset($this->pap3CampaignSettings[$campaignId])) {
    		return '';
    	}
    	if(!isset($this->pap3CampaignSettings[$campaignId][$code])) {
    		return '';
    	}
    	return $this->pap3CampaignSettings[$campaignId][$code];
    }

    protected function insertCampaignObject($id, $accountId, $name, $description, $dateIns, $status, $productid, $campaignId) {
        $obj = new Pap_Common_Campaign();
        $obj->setId($id);
        $obj->setAccountId($accountId);
        $obj->setName($name);
        $obj->set('description', $description);
        $obj->set('dateinserted', $dateIns);
        $obj->setCampaignStatus($status);

        $publicStatus = $this->getPap3Setting($campaignId, 'Aff_camp_status');

        $obj->setCampaignType(($publicStatus == 1 ? Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC : Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION));
        $obj->set('productid', $productid);
        $obj->resetOverwriteCookieToDefault();
        $obj->save();

        $commGrp = new Pap_Db_CommissionGroup();
        $commGrp->setId($obj->getDefaultCommissionGroup());
        $commGrp->load();
        $commGrp->delete();
    }

    protected function migrateCampaigns() {
    	Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Migrating campaigns.....");

    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('*');

        $selectBuilder->from->add('wd_pa_campaigns', 'c');

        $selectBuilder->where->add('c.deleted', '=', '0');

        $result = $selectBuilder->getAllRows();

        $count = 0;
        foreach($result as $record) {
        	$count++;

        	$this->insertCampaignObject($record->get('campaignid'),
        	   $record->get('accountid'),
        	   $record->get('name'),
        	   ($record->get('description') != '' ? $record->get('description') : $record->get('shortdescription')),
        	   $record->get('dateinserted'),
        	   ($record->get('disabled') == 0 ? Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE : Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED_INVISIBLE),
        	   $record->get('products'),
        	   $record->get('campaignid'));
        }

    	Pap3Compatibility_Migration_OutputWriter::log(" ($count) ..... DONE<br/>");
    }

    protected function migrateGroupsTypesCommissions() {
    	Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Migrating groups, types, commissions.....");

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('cc.*');
        $selectBuilder->select->add('c.commtype', 'commtype');
        $selectBuilder->select->add('c.campaignid', 'campaignid');

        $selectBuilder->from->add('wd_pa_campaigncategories', 'cc');
        $selectBuilder->from->addInnerJoin('wd_pa_campaigns', 'c', 'c.campaignid=cc.campaignid');

        $selectBuilder->where->add('c.deleted', '=', '0');
        $selectBuilder->where->add('cc.deleted', '=', '0');

        $result = $selectBuilder->getAllRows();

        // save normal commissions
       	foreach($result as $record) {
			$groupId = $this->insertGroup($record);

			for($tier = 1; $tier<=10; $tier++) {
    			$this->insertCommission( Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_CPM,
    									Pap_Common_Constants::TYPE_CPM,
    									$record, $groupId, $tier);

    			$this->insertCommission( Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_CLICK,
    									Pap_Common_Constants::TYPE_CLICK,
    									$record, $groupId, $tier);

    			$this->insertCommission( Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_SALE,
    									Pap_Common_Constants::TYPE_SALE,
    									$record, $groupId, $tier);

    			$this->insertCommission( Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_LEAD,
    									Pap_Common_Constants::TYPE_ACTION,
    									$record, $groupId, $tier);
			}
       	}

       	Pap3Compatibility_Migration_OutputWriter::log(" (Groups:".$this->countGroups.", Types:".$this->countCommTypes.", Commissions:".$this->countCommissions.") ..... DONE<br/>");
    }

    protected function insertGroupObject($id, $campaign, $default, $name) {
        $obj = new Pap_Db_CommissionGroup();
        $obj->setId($id);
        $obj->setCampaignId($campaign);
        $obj->setDefault($default);
        $obj->setName($name);
        $obj->save();
        return $obj->getId();
    }

    private function insertGroup($record) {

        $id = $this->insertGroupObject($record->get('campcategoryid'), $record->get('campaignid'),
            ($record->get('name') == 'L_G_UNASSIGNED_USERS' ? Gpf::YES : Gpf::NO),
            ($record->get('name') == 'L_G_UNASSIGNED_USERS' ? "Default commission group" : $record->get('name')));

    	$this->countGroups++;
    	return $id;
    }

    private function insertCommission($pap3CommType, $pap4CommType, $record, $groupId, $tier) {
    	if($record->get('commtype') & $pap3CommType) {
    		$commissionTypeObj = $this->getCommissionType( $record->get('campaignid'),
    													$record->get('recurringcommission'),
    													$record->get('recurringdatetype'),
    													$pap3CommType,
    													$pap4CommType);

			$this->insertCommissionRecord(Pap_Db_Table_Commissions::SUBTYPE_NORMAL, $record, $groupId, $tier, $commissionTypeObj->getId(), $pap4CommType);

			if($commissionTypeObj->getRecurrencePresetId() != Pap_Db_CommissionType::RECURRENCE_NONE) {
				$this->insertCommissionRecord(Pap_Db_Table_Commissions::SUBTYPE_RECURRING, $record, $groupId, $tier, $commissionTypeObj->getId(), $pap4CommType);
			}
    	}
    }

    protected function insertCommissionObject($tier, $subType, $commissionValueType, $commissionValue, $groupId, $commissionTypeId) {
        $obj = new Pap_Db_Commission();
        $obj->setTier($tier);
        $obj->setSubtype($subType);
        $obj->setCommType($commissionValueType);
        $obj->setCommission($commissionValue);
        $obj->setGroupId($groupId);
        $obj->setCommissionTypeId($commissionTypeId);
        $obj->save();
    }

	protected function insertCommissionRecord($subType, $record, $groupId, $tier, $commissionTypeId, $pap4CommType) {
		$commissionValue = $this->getCommissionValueFromRecord($subType, $record, $tier, $pap4CommType);
		if($commissionValue === false) {
			return; // no commission
		}

		$commissionValueType = $this->getCommissionValueTypeFromRecord($subType, $record, $tier, $pap4CommType);

		$this->insertCommissionObject($tier,$subType,$commissionValueType,$commissionValue,$groupId,$commissionTypeId);

		$this->countCommissions++;
	}

    private function getCommissionValueFromRecord($recurringType, $record, $tier, $pap4CommType) {
		// build source field name
		$fieldName = '';
		if($tier > 1) {
			$fieldName = 'st'.$tier;
		}

		if($recurringType == Pap_Db_Table_Commissions::SUBTYPE_NORMAL) {

			if($pap4CommType == Pap_Common_Constants::TYPE_CPM) {
				$fieldName .= 'cpm';
				if($tier > 1) {
					return false; // there are no tiers for CPM commissions
				}
			} else if($pap4CommType == Pap_Common_Constants::TYPE_CLICK) {
				$fieldName .= 'click';
			} else {
				$fieldName .= 'sale';
			}

		} else {
			$fieldName .= 'recurring';
		}
		$fieldName .= 'commission';

		if($record->get($fieldName) == 0) {
			return false; // it means commission is not set
		}

		return $record->get($fieldName);
	}

	private function getCommissionValueTypeFromRecord($recurringType, $record, $tier, $pap4CommType) {
		if($pap4CommType != Pap_Common_Constants::TYPE_SALE && $pap4CommType != Pap_Common_Constants::TYPE_ACTION) {
			return '$';
		}

		if($recurringType == Pap_Db_Table_Commissions::SUBTYPE_NORMAL) {
			$fieldName = ($tier > 1 ? 'st' : '').'salecommtype';
		} else {
			$fieldName = ($tier > 1 ? 'st' : '').'recurringcommtype';
		}
		return $record->get($fieldName);
	}

    private function getCommissionType($campaignId, $recurringCommission, $recurringDateType, $pap3CommType, $pap4CommType) {
    	if(isset($this->pap3CommissionTypes[$campaignId]) && isset($this->pap3CommissionTypes[$campaignId][$pap4CommType])) {
    		return $this->pap3CommissionTypes[$campaignId][$pap4CommType];
    	}

    	// it doesn't exist, insert new commission type
    	$obj = $this->insertCommissionType($campaignId, $recurringCommission, $recurringDateType, $pap4CommType);
    	$this->pap3CommissionTypes[$campaignId][$pap4CommType] = $obj;
    	return $obj;
    }

	protected function insertCommissionType($campaignId, $recurringCommission, $recurringDateType, $pap4CommType) {
		$obj = new Pap_Db_CommissionType();
		$obj->setType($pap4CommType);
		$obj->setStatus(Pap_Common_Constants::ESTATUS_ENABLED);

		// set commission type approval
		$pap3ApprovalStatus = '';
		if($pap4CommType == Pap_Common_Constants::TYPE_CLICK) {
			$pap3ApprovalStatus = $this->getPap3Setting($campaignId, 'Aff_camp_clickapproval');
		} else {
			$pap3ApprovalStatus = $this->getPap3Setting($campaignId, 'Aff_camp_saleapproval');
		}
		if($pap3ApprovalStatus == 2) {
			$obj->setApproval(Pap_Db_CommissionType::APPROVAL_AUTOMATIC);
		} else {
			$obj->setApproval(Pap_Db_CommissionType::APPROVAL_MANUAL);
		}

		// set recurring type
		if($recurringCommission == 0 || $recurringDateType == 0) {
			$obj->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_NONE);
		} else {
			switch($recurringDateType) {
				case Pap3Compatibility_Migration_Pap3Constants::RECURRINGTYPE_WEEKLY:
					$obj->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_WEEKLY); break;

				case Pap3Compatibility_Migration_Pap3Constants::RECURRINGTYPE_MONTHLY:
					$obj->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_MONTHLY); break;

				case Pap3Compatibility_Migration_Pap3Constants::RECURRINGTYPE_QUARTERLY:
					$obj->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_QUARTERLY); break;

				case Pap3Compatibility_Migration_Pap3Constants::RECURRINGTYPE_BIANNUALLY:
					$obj->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_SEMIANNUALLY); break;

				case Pap3Compatibility_Migration_Pap3Constants::RECURRINGTYPE_YEARLY:
					$obj->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_YEARLY); break;
				default:
					$obj->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_NONE);
			}
		}

		$obj->setZeroOrdersCommission(Gpf::YES);
        $obj->setSaveZeroCommission(Gpf::YES);
		$obj->setCampaignId($campaignId);
		$obj->save();

		$this->countCommTypes++;
		return $obj;
	}

    protected function migrateUsersInCampaigns() {
    	Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Migrating users in campaigns.....");

    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('*');
        $selectBuilder->from->add('wd_pa_affiliatescampaigns');

        $count = 0;
        foreach($selectBuilder->getAllRowsIterator() as $record) {
        	if($record->get('affiliateid') == '' || $record->get('campcategoryid') == '') {
        		continue;
        	}
        	$count++;
        	try {
        		$obj = new Pap_Db_UserInCommissionGroup();
        		$obj->setUserId($record->get('affiliateid'));
        		$obj->setCommissionGroupId($record->get('campcategoryid'));
        		$obj->setStatus(Pap3Compatibility_Migration_Pap3Constants::translateStatus($record->get('rstatus')));
        		$obj->setDateAdded(Gpf_Common_DateUtils::now());
        		$obj->save();
        	} catch(Gpf_Exception $e) {
        		// non important error, don't display it
        	}
        }

    	Pap3Compatibility_Migration_OutputWriter::log(" ($count) ..... DONE<br/>");
    }


}
?>
