<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 20081 2008-08-22 10:21:35Z vzeman $
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
class Gpf_Role_RoleForm extends Gpf_View_FormService {

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_Db_Role();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Role");
    }

    /**
     * Do nothing - form should be always empty
     * @service role read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        throw new Gpf_Exception("Function not supported");
    }

    /**
     * @service role add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $origRole = new Gpf_Db_Role();
        $origRole->setId($form->getFieldValue('roleid'));
        $origRole->load();

        $newRole = new Gpf_Db_Role();
        $newRole->setName($form->getFieldValue('name'));
        $newRole->setAccountId(Gpf_Session::getInstance()->getAuthUser()->getAccountId());
        $newRole->setRoleType($origRole->getRoleType());
        $newRole->insert();

        if (strlen($origRole->getAccountId())) {
            //it is custom role, copy privileges from db
            $select = new Gpf_SqlBuilder_SelectBuilder();
            $select->select->addConstant($newRole->getId(), 'roleid');
            $select->select->add(Gpf_Db_Table_RolePrivileges::OBJECT, Gpf_Db_Table_RolePrivileges::OBJECT);
            $select->select->add(Gpf_Db_Table_RolePrivileges::PRIVILEGE, Gpf_Db_Table_RolePrivileges::PRIVILEGE);
            $select->from->add(Gpf_Db_Table_RolePrivileges::getName());
            $select->where->add(Gpf_Db_Table_Roles::ID, '=', $origRole->getId());

            $insert = new Gpf_SqlBuilder_InsertBuilder();
            $insert->setTable(Gpf_Db_Table_RolePrivileges::getInstance());
            $insert->fromSelect($select);
            $insert->execute();
        } else {
            //it is default role, copy privileges from php settings
            $privileges = Gpf_Application::getInstance()->getRoleDefaultPrivileges($origRole->getId());
            foreach ($privileges as $objectName => $privilegeList) {
                foreach ($privilegeList as $right) {
                    $privilege = new Gpf_Db_RolePrivilege();
                    $privilege->setRoleId($newRole->getId());
                    $privilege->setObject($objectName);
                    $privilege->setPrivilege($right);
                    $privilege->insert();
                }
            }
        }

        return $form;
    }

    /**
     * @service role write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return self::add($params);
    }

    /**
     * @service role write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @service role delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
}

?>
