<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Roles.class.php 27532 2010-03-15 14:19:01Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Db_Table_Roles extends Gpf_DbEngine_Table {
    const ID = 'roleid';
    const NAME = 'name';
    const TYPE = 'roletype';

    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_roles');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::NAME, 'char', 80);
        $this->createColumn(self::TYPE, 'char', 40);
        $this->createColumn(Gpf_Db_Table_Accounts::ID, 'char', 8);
    }

    protected function initConstraints() {
       $this->addRestrictDeleteConstraint(self::ID, self::ID, new Gpf_Db_User());
       $this->addCascadeDeleteConstraint(self::ID, self::ID, new Gpf_Db_RolePrivilege());
    }    
    
    
    /**
     * Get list of roles
     *
     * @service role read
     * @param Gpf_Rpc_Params $parmas
     */
    public function getRolesList(Gpf_Rpc_Params $params) {
        $sql = new Gpf_SqlBuilder_SelectBuilder();
        $sql->from->add(Gpf_Db_Table_Roles::getName());
        $sql->select->addAll(Gpf_Db_Table_Roles::getInstance());
        $accountCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $accountCondition->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Session::getInstance()->getAuthUser()->getAccountId(), 'OR');
        $accountCondition->add(Gpf_Db_Table_Accounts::ID, '=', null, 'OR');
        $sql->where->addCondition($accountCondition);
        if ($params->exists('roleTypes') && $params->get('roleTypes') !== '') {
        	$sql->where->add(Gpf_Db_Table_Roles::TYPE, 'IN', explode(',', $params->get('roleTypes')));
        }
        $sql->orderBy->add(Gpf_Db_Table_Accounts::ID);
        $sql->orderBy->add(Gpf_Db_Table_Roles::NAME);
        return $sql->getAllRows();
    }
}

?>
