<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UserAttributes.class.php 26292 2009-11-26 17:27:07Z mbebjak $
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
class Gpf_Db_Table_UserAttributes extends Gpf_DbEngine_Table {

    const ID = 'attributeid';
    const ACCOUNT_USER_ID = "accountuserid";
    const NAME = "name";
    const VALUE = "value";
    
    /**
     * @var Gpf_Data_IndexedRecordSet
     */
    protected $attributes = null;

    protected static $instance;
        
    /**
     * @return Gpf_Db_Table_UserAttributes
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_userattributes');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::ACCOUNT_USER_ID, 'char', 8);
        $this->createColumn(self::NAME, 'char', 40);
        $this->createColumn(self::VALUE, 'char');
    }

    public function loadAttributes($userId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add(self::NAME);
        $select->select->add(self::VALUE);

        $select->from->add(self::getName(), 'ua');

        $select->where->add(self::ACCOUNT_USER_ID, '=', $userId);

        $this->attributes = $select->getAllRowsIndexedBy('name');
    }

    /**
     * @param string $name
     * @return Gpf_Data_Record
     */
    private function get($name) {
        if ($this->attributes == null) {
            throw new Gpf_Exception("Attributes not loaded");
        }
        return $this->attributes->getRecord($name);
    }
    
    public function getAttributeWithDefaultValue($name, $defaultValue = "") {
        try {
            return $this->get($name)->get(self::VALUE);
        } catch (Gpf_Data_RecordSetNoRowException $e) {
            return $defaultValue;
        }
    }
    
    public function getAttribute($name) {
        return $this->get($name)->get(self::VALUE);
    }
    
    /**
     * @throws Gpf_DbEngine_NoRowException
     */
    public static function getSetting($name, $accounUsertId = null) {
        return self::getInstance()->getInstanceSetting($name, $accounUsertId);
    }
    
    public static function setSetting($name, $value, $accountUserId = null) {
        self::getInstance()->setInstanceSetting($name, $value, $accountUserId);
    }

    protected function setInstanceSetting($name, $value, $accountUserId = null) {
        if ($accountUserId == null) {
            $accountUserId = Gpf_Session::getAuthUser()->getAccountUserId();
        }
        
        $attribute = new Gpf_Db_UserAttribute();
        $attribute->setName($name);
        $attribute->set(self::VALUE, $value);
        $attribute->setAccountUserId($accountUserId);
        $attribute->save();
    }
    
    protected function getInstanceSetting($name, $accounUsertId = null) {
        $attribute = new Gpf_Db_UserAttribute();
        return $attribute->getSetting($name, $accounUsertId);
    }
}

?>
