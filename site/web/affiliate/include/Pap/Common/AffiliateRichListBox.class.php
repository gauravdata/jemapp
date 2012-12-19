<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: User.class.php 23172 2009-01-19 00:38:53Z aharsani $
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
class Pap_Common_AffiliateRichListBox extends Gpf_Common_UserRichListBox {
	
   /**
     * @service affiliate read
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
    	$select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add("users.".Pap_Db_Table_Users::ID, self::ID);
        $select->select->add("users.".Pap_Db_Table_Users::REFID, 'refid');
        $select->select->add("authUsers.".Gpf_Db_Table_AuthUsers::USERNAME, 'username');
        $select->select->add("authUsers.".Gpf_Db_Table_AuthUsers::FIRSTNAME, 'firstname');
        $select->select->add("authUsers.".Gpf_Db_Table_AuthUsers::LASTNAME, 'lastname');
        $select->from->add(Pap_Db_Table_Users::getName(), 'users');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'accountUsers',
            'users.'.Pap_Db_Table_Users::ACCOUNTUSERID."=accountUsers.".Gpf_Db_Table_Users::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'authUsers',
            "accountUsers.".Gpf_Db_Table_Users::AUTHID."=authUsers.".Gpf_Db_Table_AuthUsers::ID);
        $select->where->add('users.'.Pap_Db_Table_Users::TYPE,
            '=', Pap_Application::ROLETYPE_AFFILIATE);
        $select->where->add('users.'.Pap_Db_Table_Users::DELETED,
            '<>', Gpf::YES);
        $select->orderBy->add("authUsers.".Gpf_Db_Table_AuthUsers::USERNAME);
        $this->modifySelect($select);
      
        return $select;
    }
    
    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
    	$selectBuilder->where->add('users.'.Pap_Db_Table_Users::ID, '=', $id);
    }
    
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('authUsers.'.Gpf_Db_Table_AuthUsers::USERNAME,
            'LIKE', '%'.$search.'%', 'OR');
        $condition->add('authUsers.'.Gpf_Db_Table_AuthUsers::FIRSTNAME,
            'LIKE', '%'.$search.'%', 'OR');
        $condition->add('authUsers.'.Gpf_Db_Table_AuthUsers::LASTNAME,
            'LIKE', '%'.$search.'%', 'OR');
        $condition->add('users.'.Pap_Db_Table_Users::REFID,
            'LIKE', '%'.$search.'%', 'OR');
        $condition->add('users.'.Pap_Db_Table_Users::ID,
            'LIKE', '%'.$search.'%', 'OR');
        $selectBuilder->where->addCondition($condition);
    }
    
    protected function modifySelect(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
    }
}
?>
