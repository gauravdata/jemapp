<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
abstract class Pap_Common_Overview_InfoData extends Pap_Common_Overview_OverviewBase {

	protected static $instance;
	/**
	 * @var array
	 */
	protected $fieldValues;	

	public function getDetails(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);
		foreach ($this->fieldValues as $code => $value) {
			$data->setValue($code, $value);
		}
		return $data;
	}

	public function getFields(Gpf_Rpc_Params $params) {
		$result = new Gpf_Data_RecordSet();
		$result->setHeader(array('id', 'code', 'name', 'type', 'countrycodes', 'status', 'availablevalues', 'help'));
		$this->buildData($result, $params);
		return $result;
	}
	
	protected function init() {
		$this->fieldValues = array();
	}
	
	protected abstract function buildData(Gpf_Data_RecordSet $fields, Gpf_Rpc_Params $params);		
}

?>
