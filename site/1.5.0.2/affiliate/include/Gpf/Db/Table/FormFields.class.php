<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: FormFields.class.php 30380 2010-12-10 14:36:57Z mkendera $
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
class Gpf_Db_Table_FormFields extends Gpf_DbEngine_Table {
    const ID = 'formfieldid';
    const ACCOUNTID = 'accountid';
    const FORMID = 'formid';
    const CODE = 'code';
    const NAME = 'name';
    const TYPE = 'rtype';
    const STATUS = 'rstatus';
    const AVAILABLEVALUES = 'availablevalues';
    const ORDER = 'rorder';
    const SECTION = 'sectionid';

    private static $instance;

    /**
     * @return Gpf_Db_Table_FormFields
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_formfields');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'int', 0, true);
        $this->createColumn(self::ACCOUNTID, 'char', 8);
        $this->createColumn(self::FORMID, 'char', 40);
        $this->createColumn(self::CODE, 'char', 40);
        $this->createColumn(self::NAME, 'char', 100);
        $this->createColumn(self::TYPE, 'char', 1);
        $this->createColumn(self::STATUS, 'char', 1);
        $this->createColumn(self::AVAILABLEVALUES, 'char');
        $this->createColumn(self::ORDER, 'int');
        $this->createColumn(self::SECTION, 'char', 8);
    }

    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::FORMID, self::CODE)));
    }

    /**
     * @param string $formid
     * @param string/array $status
     * @return Gpf_Data_RecordSet
     */
    public function getFieldsNoRpc($formid, $status = null, $mainFields = null) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add("f." . self::ID, "id");
        $select->select->add("f." . self::CODE, "code");
        $select->select->add("f." . self::NAME, "name");
        $select->select->add("f." . self::TYPE, "type");
        $select->select->add("f." . self::STATUS, "status");
        $select->select->add("f." . self::AVAILABLEVALUES, "availablevalues");

        $select->from->add($this->getName(), "f");

        $select->where->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
        $select->where->add(self::FORMID, '=', $formid);

        if ($status != null) {
            if (is_array($status)) {
                $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
                foreach ($status as $statusCode) {
                    $condition->add(self::STATUS, '=', $statusCode, 'OR');
                }
                $select->where->addCondition($condition);
            } else {
                $select->where->add(self::STATUS, '=', $status);
            }
        }

        if ($mainFields != null && $mainFields == Gpf::YES) {
            $condition = new Gpf_SqlBuilder_CompoundWhereCondition();

            $condition->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
            $condition->add(self::FORMID, '=', $formid);

            $conditionInner = new Gpf_SqlBuilder_CompoundWhereCondition();
            $conditionInner->add(self::CODE, '=', 'parentuserid', 'OR');
            $conditionInner->add(self::CODE, '=', 'refid', 'OR');
            $conditionInner->add(self::CODE, '=', 'notificationemail', 'OR');

            $condition->addCondition($conditionInner);

            $select->where->addCondition($condition, 'OR');
        }

        //$select->orderBy->add("section");
        $select->orderBy->add(self::ORDER);

        $result = $select->getAllRows();
        $result->addColumn("help", "");
        return $result;
    }

    /**
     * Loads list of fields for dynamic form panel
     *
     * @anonym
     * @service
     * @param $formId
     * @param $status (comma separated list of statuses)
     */
    public function getFields(Gpf_Rpc_Params $params) {
        $formId = $params->get('formId');
        $status = $params->get('status');
        $mainFields = $params->get('mainFields');
        if ($status == '') {
            $status = null;
        } else {
            $status = explode(",", $status);
        }
        return $this->getFieldsNoRpc($formId, $status, $mainFields);
    }
}
?>
