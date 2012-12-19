<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik, Michal Bebjak
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
 * @package PostAffiliate
 */
class Pap_Tracking_Visit_SingleVisitorProcessor extends Pap_Tracking_Visit_Processor {

    private $visitorId;
    private $accountId;    
    private $visitorsProcessed = false;

    /**
     * @var Gpf_DateTime
     */
    private $toDate;

    public function __construct($visitorId = '', $accountId = '') {
        parent::__construct();
        $this->visitorId = $visitorId;
        $this->setAccountId($accountId);
        $this->toDate = new Gpf_DateTime();
    }

    private function setAccountId($accountId) {
        if ($accountId != '') {
            $this->accountId = $accountId;
        } else {
            $this->accountId = Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
        }
        $this->visitorAffiliateCache->setAccountId($this->accountId);
    }

    /**
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function getAffiliate(Gpf_Rpc_Params $params) {      
        return $this->getResponse($params, new Pap_Affiliates_User(), 'userid', array('userid', 'refid'));
    }
    
	/**
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function getCampaign(Gpf_Rpc_Params $params) {      
        return $this->getResponse($params, new Pap_Common_Campaign(), 'campaignid');
    }
    
    /**
     * @param $params
     * @param $row
     * @param $primaryKeyName
     * @param $baseAttributes
     */
    private function getResponse(Gpf_Rpc_Params $params, Gpf_DbEngine_RowBase $row, $primaryKeyName, array $baseAttributes = null) {
    	$this->visitorId = $params->get('visitorId');

        $response = new Gpf_Rpc_Data();
        if ($this->visitorId == '') {
            return $response;
        }
        $this->setAccountId($params->get('accountId'));
        
        $this->processAllVisitorVisits();
        if (($visitorAffiliate = $this->getCurrentVisitorAffiliate()) == null) {
            return $response;
        }  
              
    	if (is_null($baseAttributes)) {
    		$baseAttributes = array($primaryKeyName);
    	}
    	$row->set($primaryKeyName, $visitorAffiliate->get($primaryKeyName));
        try {
            $row->load();
        } catch (Gpf_Exception $e) {
            return $response;
        }
        if (Gpf_Session::getAuthUser()->hasPrivilege('click', 'write')) {
            foreach ($row->getAttributes() as $name => $value) {
                $response->setValue($name, $value);
            }
        } else {
        	foreach ($baseAttributes as $attribute) {
            	$response->setValue($attribute, $row->get($attribute));
        	}
        }
        return $response;
    }
    
    public function processAllVisitorVisits() {
    	if ($this->visitorsProcessed) {
    		return;
    	}
        $processedTableIndex = (Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT) + 2) % 3;
        for ($i=0; $i<3; $i++) {
            $this->processVisitsFrom($processedTableIndex);
            $processedTableIndex = ($processedTableIndex + 2) % 3;
        }
        $this->saveVisitChanges();
        $this->visitorsProcessed = true;
    }

    private function initVisitsWhere(Gpf_SqlBuilder_WhereClause $where) {
        $where->add(Pap_Db_Table_Visits::VISITORID, '=', $this->visitorId);
        
        $accountCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $accountCondition->add(Pap_Db_Table_Visits::ACCOUNTID, '=', $this->accountId);
        if ($this->accountId == Gpf_Db_Account::DEFAULT_ACCOUNT_ID) { 
            $accountCondition->add(Pap_Db_Table_Visits::ACCOUNTID, '=', '', 'OR');
        }
        $where->addCondition($accountCondition);
        
        $where->add(Pap_Db_Table_Visits::DATEVISIT, '<=', $this->toDate->toDateTime());
    }

    private function updateRowsToBeProcessed($tableIndex) {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->set->add(Pap_Db_Table_Visits::RSTATUS, Pap_Db_Visit::INPROCESSING);
        $update->from->add(Pap_Db_Table_Visits::getName($tableIndex));
        $update->where->add(Pap_Db_Table_Visits::RSTATUS, '=', Pap_Db_Visit::UNPROCESSED);
        $this->initVisitsWhere($update->where);
        $update->execute();
    }

    private function processVisitsFrom($tableIndex) {
        $this->updateRowsToBeProcessed($tableIndex);
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_Visits::getInstance($tableIndex));
        $selectBuilder->from->add(Pap_Db_Table_Visits::getName($tableIndex));
        $selectBuilder->where->add(Pap_Db_Table_Visits::RSTATUS, '=', Pap_Db_Visit::INPROCESSING);
        $this->initVisitsWhere($selectBuilder->where);

        foreach ($selectBuilder->getAllRowsIterator() as $visitRecord) {
            $visit = new Pap_Db_Visit($tableIndex);
            $visit->fillFromRecord($visitRecord);
            $this->processAndUpdateVisit($visit);
        }
    }

    /**
     * @return Pap_Db_VisitorAffiliate
     */
    public function getCurrentVisitorAffiliate() {
        $visitorAffiliate = $this->visitorAffiliateCache->getActualVisitorAffiliate($this->visitorId);
        if ($visitorAffiliate != null) {
            return $visitorAffiliate;
        }
        $allVisitorAffiliates = $this->visitorAffiliateCache->getVisitorAffiliateAllRows($this->visitorId);
        if ($allVisitorAffiliates->getSize() > 0) {
            return $allVisitorAffiliates->get(0);
        }
        return null;
    }
}
?>
