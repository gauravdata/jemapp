<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
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
 * @package PostAffiliatePro
 */
class Pap_Merchants_Payout_AffiliateWithVatRichListBox extends Pap_Common_AffiliateRichListBox {

	protected function modifySelect(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
		$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
		$condition->add('accountUsers.'.Gpf_Db_Table_Users::ID, '=',
    	   'ua.'.Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, 'AND', false);
		$condition->add('ua.'.Gpf_Db_Table_UserAttributes::NAME, '=',
           'apply_vat_invoicing');
		$selectBuilder->from->addLeftJoin(Gpf_Db_Table_UserAttributes::getName(), 'ua',
		  $condition->toString());
		 
		if ($this->getVat() == Gpf::NO) {
			$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
			$condition->add('ua.'.Gpf_Db_Table_UserAttributes::VALUE, '=', Gpf::NO, 'OR');
			$condition->add('ua.'.Gpf_Db_Table_UserAttributes::VALUE, '=', null, 'OR');
			$selectBuilder->where->addCondition($condition);
			return;
		}
		$selectBuilder->where->add('ua.'.Gpf_Db_Table_UserAttributes::VALUE, '=', Gpf::YES);
	}

	private function getVat() {
		if ($this->params->exists('vat')) {
			return $this->params->get('vat');
		}
		throw new Gpf_Exception($this->_('Missing vat'));
	}
}
?>
