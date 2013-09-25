<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Db_Table_CommissionTypeAttributes extends Gpf_DbEngine_Table {
    const ID = 'attributeid';
    const COMMISSION_TYPE_ID = 'commtypeid';
    const NAME = 'name';
    const VALUE = 'value';
    private static $instance;
     
    /**
     * @return Pap_Db_Table_CommissionTypeAttributes
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_commissiontypeattributes');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::COMMISSION_TYPE_ID, 'char', 8);
        $this->createColumn(self::NAME, 'char', 40);
        $this->createColumn(self::VALUE, 'text', 40);

    }

    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::COMMISSION_TYPE_ID, self::NAME),
        $this->_('Name of commission type attribute has to be unique')));
    }

    /**
     * @param String commissionTypeId
     *
     * @return Gpf_Data_RecordSet
     */
    public function getAllCommissionTypeAttributes($commissionTypeId) {
        $result = new Gpf_Data_RecordSet('id');

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID, 'attributeid');
        $selectBuilder->select->add(self::COMMISSION_TYPE_ID, 'commtypeid');
        $selectBuilder->select->add(self::NAME, 'name');
        $selectBuilder->select->add(self::VALUE, 'value');
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add(self::ID, '=', $commissionTypeId);

        $result->load($selectBuilder);
        return $result;
    }


    /**
     * Load commissionType from $commissionTypeId and name
     *
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     *
     * @return Pap_Db_CommissionTypeAttribute
     */
    public function getCommissionTypeAttribute($commissionTypeId, $name) {
        $commTypeAttr = new Pap_Db_CommissionTypeAttribute();
        $commTypeAttr->setCommissionTypeId($commissionTypeId);
        $commTypeAttr->setName($name);
        $commTypeAttr->loadFromData(array(self::COMMISSION_TYPE_ID, self::NAME));

        return $commTypeAttr;
    }

    /**
     * Set value to attribute. If attribute doesn't exist, create new.
     *
     * @param $commissionTypeId
     * @param $name
     *
     * @return unknown_type
     */
    public function setCommissionTypeAttributeValue($commissionTypeId, $name, $value) {
        try {
            $commTypeAttr = $this->getCommissionTypeAttribute($commissionTypeId, $name);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $commTypeAttr = new Pap_Db_CommissionTypeAttribute();
            $commTypeAttr->setCommissionTypeId($commissionTypeId);
            $commTypeAttr->setName($name);
            $commTypeAttr->insert();
        }
        $commTypeAttr->setValue($value);
        $commTypeAttr->update(array(self::VALUE));
    }
}
?>
