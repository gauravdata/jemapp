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
class Gpf_Role_RolePrivilegesForm extends Gpf_Object {

    /**
     * Load role privileges
     *
     * @service role read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function loadRolePrivileges(Gpf_Rpc_Params $params) {
        $role = new Gpf_Db_Role();
        $role->setId($params->get('roleid'));
        $role->load();

        $defaultPrivileges = Gpf_Application::getInstance()->getDefaultPrivilegesByRoleType(
        $role->getRoleType());
        $result = new Gpf_Data_RecordSet();
        $result->addColumn('object');
        $result->addColumn('objectName');
        $result->addColumn('possiblePrivileges');
        $result->addColumn('activePrivileges');

        $rolePrivileges = Gpf_Privileges::loadPrivileges($role->getId());
        foreach ($defaultPrivileges->getDefaultPrivileges() as $object => $privileges) {
            $record = new Gpf_Data_Record($result->getHeader());

            $record->add('object', $object);
            $record->add('objectName', ucfirst(str_replace('_', ' ', strtolower($object))));
            $allTypes = $defaultPrivileges->getObjectToTypeRelation();
            $record->add('possiblePrivileges', implode(',', $allTypes[$object]));
            if (array_key_exists($object, $rolePrivileges)) {
                $record->add('activePrivileges', implode(',', array_keys($rolePrivileges[$object])));
            } else {
                $record->add('activePrivileges', '');
            }
            $result->addRecord($record);
        }
        $result->sort('objectName');
        return $result;
    }

    /**
     * Save role privileges
     *
     * @service role write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function saveRolePrivileges(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $role = new Gpf_Db_Role();
        $role->setId($form->getFieldValue('roleid'));
        $role->load();

        if (!strlen($role->getAccountId())) {
            $form->setErrorMessage($this->_('It is not possible to changes privileges for default role %s', $role->getName()));
            return $form;
        }

        //delete all privileges for selected role
        Gpf_Db_Table_RolePrivileges::getInstance()->deleteAllPrivileges($role->getId());

        //insert all privileges again to database
        $objects = new Gpf_Data_RecordSet();
        $objects->loadFromArray($form->getFieldValue('objects'));
        foreach ($objects as $record) {

            $rights = explode(',', $record->get('privileges'));
            foreach ($rights as $right) {
                $privilege = new Gpf_Db_RolePrivilege();
                $privilege->setObject($record->get('object'));
                $privilege->setRoleId($role->getId());
                $privilege->setPrivilege($right);
                $privilege->insert();
            }
        }
        return $form;
    }
}

?>
