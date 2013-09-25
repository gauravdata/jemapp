<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Channels.class.php 18660 2008-06-19 15:30:59Z aharsani $
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
class Pap_Features_MultipleMerchants_RolesRichListBox extends Gpf_Ui_RichListBoxService {

	/**
	 * @service merchant read
	 * @param $id, $search, $from, $rowsPerPage
	 * @return Gpf_Rpc_Object
	 */
	public function load(Gpf_Rpc_Params $params) {
		return parent::load($params);
	}

	/**
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	protected function createSelectBuilder() {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		$selectBuilder->select->add(Gpf_Db_Table_Roles::ID, self::ID);
		$selectBuilder->select->add(Gpf_Db_Table_Roles::NAME, self::VALUE);
		$selectBuilder->from->add(Gpf_Db_Table_Roles::getName());

		$accountCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
		$accountCondition->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Session::getInstance()->getAuthUser()->getAccountId(), 'OR');
		$accountCondition->add(Gpf_Db_Table_Accounts::ID, '=', null, 'OR');
		$selectBuilder->where->addCondition($accountCondition);
		$selectBuilder->where->add(Gpf_Db_Table_Roles::TYPE, '=', Pap_Application::ROLETYPE_MERCHANT);
		$selectBuilder->orderBy->add(Gpf_Db_Table_Accounts::ID);
		$selectBuilder->orderBy->add(Gpf_Db_Table_Roles::NAME);

		return $selectBuilder;
	}
	 
	protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
		$selectBuilder->where->add(Gpf_Db_Table_Roles::NAME, 'LIKE', '%'.$search.'%');
	}


	protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
		$selectBuilder->where->add(Gpf_Db_Table_Roles::ID, '=', $id);
	}
}
?>
