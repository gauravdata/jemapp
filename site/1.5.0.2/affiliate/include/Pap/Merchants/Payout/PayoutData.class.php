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
class Pap_Merchants_Payout_PayoutData extends Pap_Common_Overview_InfoData {
	
	private function __construct() {
		$this->init();
	}
	
	/**
	 * @return Pap_Merchants_Payout_PayoutData
	 */
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	 * Load payout data detail for user
	 *
	 * @service payout_option read
	 * @param userid
	 */
	public function payoutDataDetail(Gpf_Rpc_Params $params) {
		return parent::getDetails($params);
	}

	/**
	 * Load list of payout data
	 *
	 * @service payout_option read
	 * @param userid
	 */
	public function getFields(Gpf_Rpc_Params $params) {
		if (!$params->exists('userid')) {
			throw new Gpf_Exception('User id is missing');
		}
		return parent::getFields($params);
	}

	protected function buildData(Gpf_Data_RecordSet $fields, Gpf_Rpc_Params $params) {
		$select = new Gpf_SqlBuilder_SelectBuilder();        
        $select->select->add("name", "payoutFieldName", "ff");
        $select->select->add(Pap_Db_Table_UserPayoutOptions::VALUE, Pap_Db_Table_UserPayoutOptions::VALUE, "upo");
        $select->select->add(Pap_Db_Table_UserPayoutOptions::FORMFIELDID, Pap_Db_Table_UserPayoutOptions::FORMFIELDID, "upo");
        $select->from->add(Pap_Db_Table_Users::getName(), "pu");
        $select->from->addInnerJoin(Gpf_Db_Table_FormFields::getName(), "ff", "(ff.formid = CONCAT('payout_option_', pu.payoutoptionid))");
        $select->from->addInnerJoin(Pap_Db_Table_UserPayoutOptions::getName(), "upo", "(pu.userid = upo.userid AND ff.formfieldid = upo.formfieldid)");
        $select->where->add("pu.userid", "=", $params->get('userid'));       
        
        $i = 1;
		foreach ($select->getAllRowsIterator() as $payoutField) {
			$fields->add(array($payoutField->get(Pap_Db_Table_UserPayoutOptions::FORMFIELDID), 
								'data' . $i, 
								$this->_($payoutField->get('payoutFieldName')), 'T', 'M', null, ''));
			$this->fieldValues['data' . $i++] = $payoutField->get(Pap_Db_Table_UserPayoutOptions::VALUE);			
		}
	}
}

?>
