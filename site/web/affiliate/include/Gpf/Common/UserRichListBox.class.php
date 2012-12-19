<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
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
 * @package GwtPhpFramework
 */
class Gpf_Common_UserRichListBox extends Gpf_Ui_RichListBoxService {
    
   /**
     * @service user read
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
        $select->select->add("users.".Gpf_Db_Table_Users::ID, self::ID);
        $select->select->add("authUsers.".Gpf_Db_Table_AuthUsers::USERNAME, 'username');
        $select->select->add("authUsers.".Gpf_Db_Table_AuthUsers::FIRSTNAME, 'firstname');
        $select->select->add("authUsers.".Gpf_Db_Table_AuthUsers::LASTNAME, 'lastname');
        $select->from->add(Gpf_Db_Table_Users::getName(), 'users');
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'authUsers',
            "users.".Gpf_Db_Table_Users::AUTHID."=authUsers.".Gpf_Db_Table_AuthUsers::ID);
        Gpf_Plugins_Engine::extensionPoint('Gpf_Common_UserRichListBox.createSelectBuilder', $select->where);
        $select->orderBy->add("authUsers.".Gpf_Db_Table_AuthUsers::USERNAME);
      
        return $select;
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
        $selectBuilder->where->addCondition($condition);
    }
    
    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
        $selectBuilder->where->add('users.'.Gpf_Db_Table_Users::ID, '=', $id);
    }
}
?>
