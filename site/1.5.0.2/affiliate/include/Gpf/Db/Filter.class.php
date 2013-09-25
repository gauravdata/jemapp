<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Filter.class.php 24612 2009-06-11 13:28:02Z aharsani $
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
class Gpf_Db_Filter extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Filters::getInstance());
        parent::init();
    }

    /**
     *
     * @service filter delete
     * @param $filterid
     * @return Gpf_Rpc_Action
     */
    public function deleteFilters(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to delete %s filter(s)'));
        $action->setInfoMessage($this->_('%s filter(s) successfully deleted'));

        foreach ($action->getIds() as $filterid) {
            try {
                $this->delete($filterid);
            } catch (Exception $e) {
                $action->addError();
            }
        }
        return $action;
    }

    public function delete($filterId) {
        $this->set('filterid', $filterId);
        $this->load();
        if($this->getPreset() == Gpf::YES) {
       		throw new Gpf_Exception("You cannot delete preset filter!");
        }
        $conditionsTable = Gpf_Db_Table_FilterConditions::getInstance();
        $conditionsTable->deleteAll($filterId);
        return parent::delete();
    }
    
    public function setFilterId($value) {
        $this->set(Gpf_Db_Table_Filters::FILTER_ID, $value);
    }
    
    public function getId() {
        return $this->get(Gpf_Db_Table_Filters::FILTER_ID);
    }
    
    public function setName($value) {
    	return $this->set(Gpf_Db_Table_Filters::NAME, $value);
    }

    public function setUserId($value) {
    	return $this->set(Gpf_Db_Table_Filters::USER_ID, $value);
    }

    public function setFilterType($value) {
    	return $this->set(Gpf_Db_Table_Filters::FILTER_TYPE, $value);
    }

    public function setPreset($value) {
    	return $this->set(Gpf_Db_Table_Filters::PRESET, $value);
    }
        
    public function getPreset() {
    	return $this->get(Gpf_Db_Table_Filters::PRESET);
    }
}
