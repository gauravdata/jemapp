<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: FormFieldGrid.class.php 23398 2009-02-04 14:47:26Z vzeman $
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
class Gpf_View_FormFieldGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn('code', $this->_("Code"), true);
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn('rtype', $this->_("Type"), true);
        $this->addViewColumn('rstatus', $this->_("Status"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_FormFields::ID);
        $this->addDataColumn('formid', Gpf_Db_Table_FormFields::FORMID);
        $this->addDataColumn('code', Gpf_Db_Table_FormFields::CODE);
        $this->addDataColumn('name', Gpf_Db_Table_FormFields::NAME);
        $this->addDataColumn('rtype', Gpf_Db_Table_FormFields::TYPE);
        $this->addDataColumn('rstatus', Gpf_Db_Table_FormFields::STATUS);
        $this->addDataColumn('availablevalues', Gpf_Db_Table_FormFields::AVAILABLEVALUES);
        $this->addDataColumn('sectionid', Gpf_Db_Table_FormFields::SECTION);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('code', '40px', 'N');
        $this->addDefaultViewColumn('name', '40px', 'N');
        $this->addDefaultViewColumn('rtype', '40px', 'N');
        $this->addDefaultViewColumn('rstatus', '40px', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_FormFields::getName());
    }

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createEmptyRow(Gpf_Rpc_Params $params) {
        $row = new Gpf_Db_FormField();
        $row->set(Gpf_Db_Table_Accounts::ID, Gpf_Session::getAuthUser()->getAccountId());
        $row->set(Gpf_Db_Table_FormFields::FORMID, $params->get('formid'));
        $row->set(Gpf_Db_Table_FormFields::CODE, "code");
        $row->set(Gpf_Db_Table_FormFields::NAME, $this->_("New field"));
        $row->set(Gpf_Db_Table_FormFields::TYPE, Gpf_Db_FormField::TYPE_TEXT);
        $row->set(Gpf_Db_Table_FormFields::STATUS, Gpf_Db_FormField::STATUS_OPTIONAL);
        $row->set(Gpf_Db_Table_FormFields::SECTION, Gpf_Db_FormField::DEFAULT_SECTION);

        $i = 1;
        while ($i < 10) {
            try {
                $row->check();
                break;
            } catch (Gpf_DbEngine_Row_CheckException $e) {
                $row->set(Gpf_Db_Table_FormFields::CODE, "code_".$i);
                $i++;
            }
        }

        return $row;
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add(Gpf_Db_Table_FormFields::FORMID, '=', $this->getFormId());
    }

    private function getFormId() {
        if ($this->_params->exists('formid')) {
            return $this->_params->get('formid');
        }
        throw new Gpf_Exception($this->_('Missing formid'));
    }

    protected function getFilterValue(Gpf_Rpc_Params $params, $code) {
        $filters = $params->get('filters');
        if (is_array($filters)) {
            foreach ($filters as $filterArray) {
                $filter = new Gpf_SqlBuilder_Filter($filterArray);
                if ($filter->getCode() == $code) {
                    return $filter->getValue();
                }
            }
        }
        throw new Gpf_Exception($this->_('Can not create field'));
    }

    /**
     * @service form_field read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service form_field add
     * @return Gpf_Rpc_Serializable
     */
    public function getRowsAddNew(Gpf_Rpc_Params $params) {
        return parent::getRowsAddNew($params);
    }

    /**
     * @service form_field export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
