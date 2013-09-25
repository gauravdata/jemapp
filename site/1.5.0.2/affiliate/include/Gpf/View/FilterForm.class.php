<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani 
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FilterForm.class.php 25442 2009-09-22 12:13:20Z mjancovic $
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
class Gpf_View_FilterForm extends Gpf_Object {

    /**
     * @service filter read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $filter = new Gpf_Db_Filter();
        $filter->set('filterid', $form->getFieldValue("Id"));
        //TODO: check if filterid belongs to authUser
        try {
            $filter->load();
            $form->load($filter);
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->_("Filter does not exist"));
        }
        return $form;
    }

    /**
     * @service filter write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $filter = new Gpf_Db_Filter();
        $filter->setPrimaryKeyValue($form->getFieldValue("Id"));
        //TODO: check if filterid belongs to authUser

        try {
            $filter->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            $form->setErrorMessage($this->_("Filter does not exist"));
            return $form;
        }

        $form->fill($filter);

        try {
            $filter->save();
            $this->saveFilterPresets($form, $filter->getPrimaryKeyValue());
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($filter);
        $form->setInfoMessage($this->_("Filter saved"));
        return $form;
    }

    /** 
     * @service filter write
     * @param $fields
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $filter = new Gpf_Db_Filter();
        $form->fill($filter);
        $filter->setUserId(Gpf_Session::getAuthUser()->getUserId());

        try {
            $filter->insert();
            $this->saveFilterPresets($form, $filter->getPrimaryKeyValue());
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($filter);
        $form->setField('Id', $filter->get('filterid'));
        $form->setInfoMessage($this->_("Filter added"));
        return $form;
    }

    private function saveFilterPresets(Gpf_Rpc_Form $form, $filterId) {
        $filterConditionsTable = Gpf_Db_Table_FilterConditions::getInstance();
        $filterConditionsTable->deleteAll($filterId);

        $presets = new Gpf_Data_RecordSet();
        $presets->loadFromArray($form->getFieldValue("presets"));

        foreach ($presets as $preset) {
            $filterCondition = new Gpf_Db_FilterCondition();
            $filterCondition->setFilterId($filterId);
            $filterCondition->fillFromRecord($preset);
            $filterCondition->save();
        }
    }
}

?>
