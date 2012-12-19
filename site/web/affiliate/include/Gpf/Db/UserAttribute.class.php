<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UserAttribute.class.php 20743 2008-09-08 15:06:38Z aharsani $
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
class Gpf_Db_UserAttribute extends Gpf_DbEngine_Row {

    function init() {
        $this->setTable(Gpf_Db_Table_UserAttributes::getInstance());
        parent::init();
    }

    public function setName($name) {
        $this->set(Gpf_Db_Table_UserAttributes::NAME, $name);
    }

    public function setAccountUserId($accountUserId) {
        $this->set(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, $accountUserId);
    }

    public function getValue() {
        return $this->get(Gpf_Db_Table_UserAttributes::VALUE);
    }

    public function setValue($value) {
        $this->set(Gpf_Db_Table_UserAttributes::VALUE, $value);
    }

    public function getName() {
        return $this->get(Gpf_Db_Table_UserAttributes::NAME);
    }

    public function getAccountUserId() {
        return $this->get(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID);
    }

    public function getSetting($name, $accounUsertId = null) {
        if ($accounUsertId == null) {
            $accounUsertId = Gpf_Session::getAuthUser()->getAccountUserId();
        }
        $this->setName($name);
        $this->setAccountUserId($accounUsertId);
        $this->loadFromData(array(Gpf_Db_Table_UserAttributes::NAME, Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID));
        return $this->getValue();
    }

    public function save() {
        try {
            $attribute = new Gpf_Db_UserAttribute();
            $attribute->getSetting($this->getName(), $this->getAccountUserId());
            $this->setPrimaryKeyValue($attribute->getPrimaryKeyValue());
            $this->update();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->insert();
        }
    }

    /**
     * returns recordset of given attribute values for given array of users
     *
     * @param unknown_type $settingsNames
     * @param unknown_type $accountUserIds
     */
    public static function getSettingsForGroupOfUsers($settingsNames, $accountUserIds) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, "accountuserid");
        $select->select->add(Gpf_Db_Table_UserAttributes::NAME, "name");
        $select->select->add(Gpf_Db_Table_UserAttributes::VALUE, "value");

        $select->from->add(Gpf_Db_Table_UserAttributes::getName());

        $select->where->add(Gpf_Db_Table_UserAttributes::NAME, "IN", $settingsNames);
        $select->where->add(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, "IN", $accountUserIds);

        $recordSet = $select->getAllRows();

        $results = array();
        foreach($recordSet as $record) {
            $results[$record->get('accountuserid')][$record->get('name')] = $record->get('value');
        }

        return $results;
    }

    public static function saveAttribute($name, $value, $accountUserId = null) {
        $attribute = new Gpf_Db_UserAttribute();
        $attribute->setName($name);
        $attribute->setValue($value);
        if ($accountUserId == null) {
            $attribute->setAccountUserId(Gpf_Session::getInstance()->getAuthUser()->getAccountUserId());
        } else {
            $attribute->setAccountUserId($accountUserId);
        }
        return $attribute->save();
    }
}

?>
