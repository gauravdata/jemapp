<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Channels.class.php 18660 2008-06-19 15:30:59Z aharsani $
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
abstract class Gpf_Ui_RichListBoxService extends Gpf_Object {
	
	const ID = 'id';
	const VALUE = 'value';
	
	/**
	 * @var Gpf_Rpc_Params
	 */
	protected $params;
	
	private $count;

	/**
     * @param $id, $search, $from, $rowsPerPage
     * @return Gpf_Rpc_Object
     */
    public function load(Gpf_Rpc_Params $params) {
    	
    	// switch is preferred before use three load methods (duplicate permissions)
    	
        $this->init($params);

        switch ($params->get(Gpf_Ui_RichListBox::REQUEST_TYPE)) {
            case Gpf_Ui_RichListBox::REQUEST_CACHED:
                return new Gpf_Ui_RichListBox(Gpf_Ui_RichListBox::REQUEST_CACHED, $this->getChachedRecordSet(), $this->count);
                break;
            case Gpf_Ui_RichListBox::REQUEST_SEARCH:
                return new Gpf_Ui_RichListBox(Gpf_Ui_RichListBox::REQUEST_SEARCH, $this->getSearchRecordSet(), $this->count);
                break;
            case Gpf_Ui_RichListBox::REQUEST_ID:
                return new Gpf_Ui_RichListBox(Gpf_Ui_RichListBox::REQUEST_ID, $this->getIdRecordSet());
        }
        throw new Gpf_Exception($this->_('Request id is missing or unsupported'));
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createSelectBuilder() {
    }
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
    }
    
    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function getSearchRecordSet() {
        $selectBuilder = $this->createSelectBuilder();
        $this->addSearchCondition($selectBuilder, $this->params->get(Gpf_Ui_RichListBox::SEARCH));
        
        $this->count = $this->getCount($selectBuilder);
        
        $selectBuilder->limit->set($this->params->get(Gpf_Ui_RichListBox::FROM), $this->params->get(Gpf_Ui_RichListBox::ROWS_PER_PAGE));
        
        return $selectBuilder->getAllRows();
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function getChachedRecordSet() {
    	$selectBuilder = $this->createSelectBuilder();
        $this->count = $this->getCount($selectBuilder);

        if ($this->count > $this->params->get(Gpf_Ui_RichListBox::MAX_CACHED_COUNT)) {
            $selectBuilder->limit->set($this->params->get(Gpf_Ui_RichListBox::FROM), $this->params->get(Gpf_Ui_RichListBox::ROWS_PER_PAGE));
        }
        return $selectBuilder->getAllRows();
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function getIdRecordSet() {
        $selectBuilder = $this->createSelectBuilder();
        $this->addIdSearchCondition($selectBuilder,  $this->params->get(Gpf_Ui_RichListBox::SEARCH));
        
        return $selectBuilder->getAllRows();
    }
    
    protected function getCount(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
    	$sth = $this->createDatabase()->execute($selectBuilder->toString());
        return $sth->rowCount();
    }
    
    protected function cachedFromRecordSet(Gpf_Data_RecordSet $recordSet) {
    	return $this->getCachedRecordSet($recordSet);
    }
    
    protected function searchFromRecordSet(Gpf_Data_RecordSet $recordSet) {
        $possibleValuesRecordSet = $this->getPossibleValues($recordSet);

        return $this->getCachedRecordSet($possibleValuesRecordSet);
    }
    
    protected function searchIdFromRecordSet(Gpf_Data_RecordSet $recordSet) {
        return $this->getPossibleValues($recordSet, 'id');
    }
    
    private function getPossibleValues(Gpf_Data_RecordSet $recordSet, $column = null) {
        $search = $this->params->get(Gpf_Ui_RichListBox::SEARCH);
        $possibleValuesRecordSet = $recordSet->toShalowRecordSet();

        foreach ($recordSet as $record) {
            if ($column != null) {
                if ($record->get($column) == $search) {
                    $possibleValuesRecordSet->addRecord($record);
                    break;
                }
            } else {
                foreach ($record->toObject() as $value) {
                    if (strstr($value, $search)) {
                        $possibleValuesRecordSet->addRecord($record);
                        break;
                    }
                }
            }
        }

        return $possibleValuesRecordSet;
    }
    
    private function getCachedRecordSet(Gpf_Data_RecordSet $recordSet) {
        $from = $this->params->get(Gpf_Ui_RichListBox::FROM);
        $rowsPerPage = $this->params->get(Gpf_Ui_RichListBox::ROWS_PER_PAGE);
        $this->count = $recordSet->getSize();
            
        if ($this->count > $this->params->get(Gpf_Ui_RichListBox::MAX_CACHED_COUNT)) {
            $cachedRecordSet = $recordSet->toShalowRecordSet();
            
            if ($from + $rowsPerPage > $this->count) {
                $to = $this->count;
            } else {
                $to = $from + $rowsPerPage;
            }

            for ($from; $from < $to; $from++) {
                $cachedRecordSet->add($recordSet->getRecord($from));
            }
            return $cachedRecordSet;
        }
        return $recordSet;
    }
    
    private function init(Gpf_Rpc_Params $params) {
        $this->params = $params;
    }
}
?>
