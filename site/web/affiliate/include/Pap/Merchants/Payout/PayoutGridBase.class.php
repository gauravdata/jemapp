<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
abstract class Pap_Merchants_Payout_PayoutGridBase extends Pap_Common_StatsGrid {

	/**
	 * @var Gpf_Data_RecordSet
	 */
	private static $userFields;

    public function __construct() {
        parent::__construct(Pap_Stats_Table::USERID, 'pu');
    }

	protected function addUserAdditionalViewColumns() {
		foreach (self::getUserFields() as $field) {
			$this->addViewColumn($field->get('code'), Gpf_Lang::_localizeRuntime($field->get('name')), true);
		}
	}

	protected function addUserAditionalDataColumns() {
		foreach (self::getUserFields() as $field) {
			if ($field->get('code') === Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL) {
				$this->addDataColumn($field->get('code'),   'au.' . $field->get('code'));
				continue;
			}
			$this->addDataColumn($field->get('code'),   'pu.' . $field->get('code'));
		}
	}

    protected function initRequiredColumns() {
        parent::initRequiredColumns();
        $this->addRequiredColumn('userid');
    }


	private static function getUserFields() {
		if (is_null(self::$userFields)) {
			self::$userFields = Gpf_Db_Table_FormFields::getInstance()->getFieldsNoRpc('affiliateForm', array('M', 'O'));
		}
		return self::$userFields;
	}

}
?>
