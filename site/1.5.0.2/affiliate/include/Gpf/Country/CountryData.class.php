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
class Gpf_Country_CountryData extends Gpf_Object implements Gpf_Rpc_TableData {

	/**
	 * @service
	 * @anonym
	 * @return Gpf_Data_RecordSet
	 */
	public function getRow(Gpf_Rpc_Params $params) {
		$select = $this->createCountriesSelect();
		$select->where->add(Gpf_Db_Table_Countries::COUNTRY_CODE, '=', $params->get(self::SEARCH));
		$select->limit->set(0, 1);
		$recordset = $select->getAllRows();
		foreach ($recordset as $record) {
			$record->set('name', $this->_localize($record->get('name')));
		}
		return $recordset;
	}

	/**
	 * @service
	 * @anonym
	 * @return Gpf_Data_Table
	 */
	public function getRows(Gpf_Rpc_Params $params) {
		$data = new Gpf_Data_Table($params);
		$select = $this->createCountriesSelect();
		$recordset = $select->getAllRows();
		foreach ($recordset as $record) {
			$record->set('name', $this->_localize($record->get('name')));
		}
		$data->fill($recordset);
		return $data;
	}

	/**
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	protected function createCountriesSelect() {
		$select = new Gpf_SqlBuilder_SelectBuilder();
		$select->select->add(Gpf_Db_Table_Countries::COUNTRY_CODE, 'code');
		$select->select->add(Gpf_Db_Table_Countries::COUNTRY, 'name');
		$select->from->add(Gpf_Db_Table_Countries::getName());
		$select->where->add(Gpf_Db_Table_Countries::STATUS, '=', Gpf_Db_Country::STATUS_ENABLED);
		$select->orderBy->add(Gpf_Db_Table_Countries::ORDER);
		$select->orderBy->add(Gpf_Db_Table_Countries::COUNTRY);
		return $select;
	}
		
}
?>
