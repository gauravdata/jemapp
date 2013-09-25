<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
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
 * @package GwtPhpFramework
 */
class Pap_Common_Campaign_CommTypeListBox extends Gpf_Object implements Gpf_Rpc_TableData {

	/**
	 * @service campaign read
	 * @return Gpf_Data_RecordSet
	 */
	public function getRow(Gpf_Rpc_Params $params) {
		if (!Pap_Db_Table_CommissionTypes::isSpecialType($params->get(self::SEARCH))) {
			return $this->getRowFromDB($params->get(self::SEARCH), $params->get('campaignid'));
		}
		return $this->getRowFromSpecialTypes($params->get(self::SEARCH));
	}

	/**
	 * @service campaign read
	 * @return Gpf_Data_Table
	 */
	public function getRows(Gpf_Rpc_Params $params) {		
		$select = $this->createCommissionTypeSelect($params->get('campaignid'));
		$commTypes = $select->getAllRows();
		$this->addSpecialTypes($commTypes);
		$this->translateCommTypes($commTypes);				
		return $this->createResponse($params, $commTypes);
	}
	
	/**
	 * @param $params
	 * @return Gpf_Data_Table
	 */
	protected function createResponse(Gpf_Rpc_Params $params, Gpf_Data_RecordSet $commTypes) {
		$data = new Gpf_Data_Table($params);
		$data->fill($commTypes);
		return $data;
	}

	protected function addSpecialTypes(Gpf_Data_RecordSet $commTypes) {
		foreach (Pap_Db_Table_CommissionTypes::getSpecialTypesArray() as $specialType) {
			$commTypes->add($this->createArray($specialType));
		}
	}

	/**
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	protected function createCommissionTypeSelect($campaignId) {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		foreach ($this->createHeaderArray() as $column) {
			$selectBuilder->select->add($column);	
		}		
		$selectBuilder->from->add(Pap_Db_Table_CommissionTypes::getName());
		if ($campaignId !== '') {
			$selectBuilder->where->add(Pap_Db_Table_CommissionTypes::CAMPAIGNID, '=', $campaignId);
		}
		return $selectBuilder;
	}

	/**
	 * @param $commTypeId
	 * @param $campaignId
	 * @return Gpf_Data_RecordSet
	 */
	protected function getRowFromDB($commTypeId, $campaignId) {
		$select = $this->createCommissionTypeSelect($campaignId);
		$select->where->add(Pap_Db_Table_CommissionTypes::ID, '=', $commTypeId);
		$select->limit->set(0, 1);
		$commType = $select->getAllRows();
		$this->translateCommTypes($commType);
		return $commType;
	}

	/**
	 * @param $params
	 * @return Gpf_Data_IndexedRecordSet
	 */
	protected function getRowFromSpecialTypes($commTypeValue) {
		$commTypes = new Gpf_Data_IndexedRecordSet(Pap_Db_Table_CommissionTypes::ID);
		$commTypes->setHeader($this->createHeaderArray());
		$this->addSpecialTypes($commTypes);
		$this->translateCommTypes($commTypes);
		$commType = $commTypes->toShalowRecordSet();
		try {
			$commType->addRecord($commTypes->getRecord($commTypeValue));
		} catch (Gpf_Data_RecordSetNoRowException $e) {
		}
		return $commType;
	}

	protected function translateCommTypes(Gpf_Data_RecordSet $commTypes) {
		foreach ($commTypes as $commType) {
			if ($commType->get(Pap_Db_Table_CommissionTypes::TYPE) === Pap_Common_Constants::TYPE_ACTION) {
				continue;
			}
			$commType->set('name', $this->_localize(Pap_Common_Constants::getTypeAsText($commType->get(Pap_Db_Table_CommissionTypes::TYPE))));
		}
	}

	/**
	 * @return array
	 */
	private function createHeaderArray() {
		return array(Pap_Db_Table_CommissionTypes::ID,
		Pap_Db_Table_CommissionTypes::NAME,
		Pap_Db_Table_CommissionTypes::TYPE);
	}

	/**
	 * @param $type
	 * @return array
	 */
	private function createArray($type) {
		return array($type, null, $type);
	}
}
?>
