<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GridService.class.php 33033 2011-06-03 13:23:56Z mkendera $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *   @package GwtPhpFramework
 */
abstract class Gpf_View_GridService extends Gpf_Object {
    protected $_db;

    //TODO: remove starting underscre
    /**
     * @var Gpf_SqlBuilder_SelectBuilder
     */
    protected $_selectBuilder;

    //TODO: remove starting underscre
    /**
     * @var Gpf_View_Columns
     */
    protected $_columns;

    //TODO: remove starting underscre
    /**
     * @var Gpf_Data_RecordSet
     */
    protected $_requiredColumns;
    protected $_sortColumn;
    protected $_sortAsc;
    /**
     * @var Gpf_Rpc_Params
     */
    protected $_params;
    protected $_fileName;
    protected $_count;

    protected $viewColumns = array();
    protected $dataColumns = array();
    protected $defaultViewColumns = array();
    protected $offset;
    protected $limit = null;

    /**
     * @var Gpf_Rpc_FilterCollection
     */
    protected $filters;


    const KEY_COLUMN_ID = 'id';
    const ACTIONS = 'actions';
    const SEARCH_FILTER = 'search';

    public function __construct() {
        $this->_db = $this->createDatabase();
        $this->_selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $this->filters = new Gpf_Rpc_FilterCollection();
        $this->_columns = new Gpf_View_Columns();
        $this->_requiredColumns = new Gpf_Data_IndexedRecordSet('id');
        $this->_requiredColumns->setHeader(array('id'));
        $this->initColumns();
        $this->_fileName = null;
        if (!array_key_exists(self::KEY_COLUMN_ID, $this->dataColumns)) {
            throw new Gpf_Exception("Key column not defined in " . get_class($this) . ". Use setKeyDataColumn() function to set key data column");
        }
    }

    /**
     * Add view column
     *
     * @param $id
     * @param $name
     * @param $sortable
     * @param $type Default type is Gpf_View_ViewColumn::TYPE_STRING
     */
    public function addViewColumn($id, $name, $sortable = false, $type = Gpf_View_ViewColumn::TYPE_STRING) {
        $this->viewColumns[$id] = new Gpf_View_ViewColumn($id, $name, $sortable, "", "N", false, $type);
    }

    protected function setKeyDataColumn($sqlName) {
        $this->dataColumns[self::KEY_COLUMN_ID] = new Gpf_View_Column(self::KEY_COLUMN_ID, $sqlName);
        $this->addRequiredColumn(self::KEY_COLUMN_ID);
    }

    public function addDataColumn($id, $sqlName = null) {
        if($sqlName === null) {
            $sqlName = $id;
        }
        $this->dataColumns[$id] = new Gpf_View_Column($id, $sqlName);
    }

    /**
     * Add default view column
     *
     * @param string $id
     * @param string $width
     * @param string $sorted Possible values: "N" - no sorting
     */
    public function addDefaultViewColumn($id, $width = '1', $sorted = 'N') {
        if (!array_key_exists($id, $this->viewColumns)) {
            return;
        }
        $this->defaultViewColumns[$id] = array ('width' => $width, 'sorted' => $sorted);
    }

    protected function addRequiredColumn($columnName) {
       try {
            $this->_requiredColumns->getRecord($columnName);            
        } catch (Gpf_Data_RecordSetNoRowException $e) {
            $this->_requiredColumns->add(array($columnName, Gpf::YES));    
        }        
    }

    public function clearDefaultViewColumns() {
        $this->defaultViewColumns = array();
    }

    /**
     * @return Gpf_Data_IndexedRecordSet
     */
    public function getAllViewColumns() {
        $columns = new Gpf_Data_IndexedRecordSet('id');
        $columns->setHeader(Gpf_View_ViewColumn::getMetaResultArray());
        foreach ($this->viewColumns as $viewColumn) {
            $columns->add($viewColumn->getResultArray());
        }
        return $columns;
    }

    /**
     * @return Gpf_Data_IndexedRecordSet
     */
    public function getDefaultViewColumns() {
        $columns = $this->getAllViewColumns();
        foreach ($this->defaultViewColumns as $defaultColumnId => $defaultColumnPreset) {
            $column = $columns->getRecord($defaultColumnId);
            $column->set("visible", Gpf::YES);
            $column->set("width", $defaultColumnPreset['width']);
            $column->set("sorted", $defaultColumnPreset['sorted']);
        }
        return $columns;
    }

    private function init(Gpf_Rpc_Params $params) {
        $this->_params = $params;
        if (!$params->exists('columns')) {
            $viewService = new Gpf_View_ViewService();
            $viewService->fillActiveViewData($params, $this);
        }

        $this->limit = null;

        $this->_sortColumn = $params->get('sort_col');
        if($params->exists('sort_asc')) {
            $this->_sortAsc = ($params->get('sort_asc') == 'true' ? true : false);
        }

        $this->loadRequiredColumnsFromArray($params->get('columns'));

        $this->filters = new Gpf_Rpc_FilterCollection($this->_params);
    }
    
    private function loadRequiredColumnsFromArray($columnsArray) {
        $indexedRecordset = new Gpf_Data_IndexedRecordSet('id');
        $indexedRecordset->loadFromArray($columnsArray);
        foreach ($indexedRecordset as $record) {
            try {
                $visible = $record->get('visible');
            } catch (Gpf_Exception $e) {
                $visible = Gpf::YES;
            }
            if ($visible == Gpf::YES) {                
                $this->addRequiredColumn($record->get('id'));
            }
        }
    }

    protected function initLimit() {
        $this->offset = $this->getParam('offset');
        $this->limit = $this->getParam('limit');

        if (!is_numeric($this->offset) || $this->offset < 0) {
            $this->offset = 0;
        }

        if (!is_numeric($this->limit) || $this->limit <= 0) {
            $this->limit = 30;
        }
    }

    /**
     * Returns row data for grid
     *
     * @service
     *
     * @param $filters
     * @param $limit
     * @param $offset
     * @param $sort_col
     * @param $sort_asc
     * @param Gpf_Data_RecordSet $columns
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $this->init($params);
        return $this->createGridResponse($this->getResult());
    }

    /**
     * @param $params
     * @return Gpf_View_GridService_IdsIterator
     */
    public function getIdsIterator(Gpf_Rpc_Params $params) {
        $this->init($params);
        $this->buildSelect();
        $this->buildFrom();
        $this->buildWhere();
        $this->buildGroupBy();
        return new Gpf_View_GridService_IdsIterator($this->createRowsIterator());
    }

    /**
     * @param $result
     * @return Gpf_Rpc_Object
     */
    protected function createGridResponse(Gpf_Data_RecordSet $result) {
        //TODO: use Gpf_Data_Grid
        $response = new Gpf_Rpc_Object();
        $response->rows = $result->toObject();
        $response->count = $this->getCount();
        return $response;
    }

    /**
     * Adds new row to table and returns row data
     *
     * @service
     *
     * @param $filters
     * @param $limit
     * @param $offset
     * @param $sort_col
     * @param $sort_asc
     * @param Gpf_Data_RecordSet $columns
     * @return Gpf_Rpc_Serializable
     */
    public function getRowsAddNew(Gpf_Rpc_Params $params) {
        $row = $this->createEmptyRow($params);

        if ($params->get('limit') != null) {
            $params->set('limit', $params->get('limit') - 1);
        }
        $this->init($params);

        $row->save();

        return $this->createGridResponse($this->getResult());
    }

    //TODO: to be REMOVED,
    /**
     * Returns row data count
     *
     * @service
     *
     * @param $filters
     * @param Gpf_Data_RecordSet $columns
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        $this->init($params);

        $this->createResultSelect();

        $this->computeCount();

        return $this->createGridResponse(new Gpf_Data_RecordSet());
    }

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createEmptyRow(Gpf_Rpc_Params $params = null) {
        throw new Gpf_Exception("Add row is not supported on server");
    }

    protected function getParam($name) {
        if (isset($this->_params)) {
            return $this->_params->get($name);
        }
        return false;
    }

    private function initColumns() {
        $this->initViewColumns();
        $this->initDataColumns();
        $this->initDefaultView();
        $this->initRequiredColumns();
    }

    abstract protected function initViewColumns();

    abstract protected function initDataColumns();

    abstract protected function initDefaultView();

    protected function initRequiredColumns() {
    }

    protected function createResultSelect() {
        $this->buildSelect();
        $this->buildFrom();
        $this->buildWhere();
        $this->buildOrder();
        $this->buildGroupBy();
    }

    /**
     *
     * @return Gpf_Data_RecordSet
     */
    public final function getResult() {
        $this->createResultSelect();
        $this->computeCount();
        $this->buildLimit();
        return $this->loadResultData();
    }

    /**
     * @deprecated
     * @param Gpf_Data_RecordSet $inputResult
     * @return Gpf_Data_RecordSet
     */
    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
        return $inputResult;
    }

    protected function isColumnRequired($columnName) {
        if ($this->_requiredColumns == null) {
            return false;
        }
        try {
            $column = $this->_requiredColumns->getRecord($columnName);            
            return true;
        } catch (Gpf_Data_RecordSetNoRowException $e) {
            return false;
        }
    }

    protected function buildSelect() {
        foreach ($this->dataColumns as $column) {
            $this->addSelect($column->getName(), $column->getId());
        }
    }

    protected function addSelect($sql, $alias) {
        $this->_selectBuilder->select->add($sql, $alias);
    }

    /**
     * @param $select
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $innerSelectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $innerSelectBuilder->select = $select->select;
        $innerSelectBuilder->from = $select->from;
        $innerSelectBuilder->where = $select->where;
        $innerSelectBuilder->groupBy = $select->groupBy;
        $innerSelectBuilder->having = $select->having;

        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from->addSubselect($innerSelectBuilder, 'count');

        return $count;
    }

    protected function getCount() {
        return (int) $this->_count;
    }

    protected function computeCount() {
        $this->_count = $this->createCountSelect($this->_selectBuilder)->getOneRow()->get('count');
    }

    protected function buildOrder() {
        if($this->_sortColumn) {
            if (array_key_exists($this->_sortColumn, $this->dataColumns)) {
                $this->_selectBuilder->orderBy->add($this->_sortColumn, $this->_sortAsc);
            }
        }
    }

    protected function buildLimit() {
        $this->initLimit();
        while($this->offset > $this->_count) {
            $this->offset = $this->offset - $this->limit;
        }
        $this->_selectBuilder->limit->set($this->offset, $this->limit);
    }

    protected function buildWhere() {
        //TODO: move to ->getResult()
        $this->buildFilter();
    }

    protected function buildFilter() {
        $timeFilterCompoundWhere = null;
        $firtsTimeFilter = null;
        foreach ($this->filters as $filter) {
            if (array_key_exists($filter->getCode(), $this->dataColumns)) {
                $dataColumn = $this->dataColumns[$filter->getCode()];
                $filter->setCode($dataColumn->getName());
                if ($this->isTimeFilter($filter) && $this->existsTwoTimeFilters()) {
                    if (is_null($timeFilterCompoundWhere)) {
                        $timeFilterCompoundWhere = $this->addTimeFilterCompoundWhere(new Gpf_SqlBuilder_CompoundWhereCondition());
                        
                        $this->addTimeFilter($filter, $timeFilterCompoundWhere);
                        $firtsTimeFilter = $filter;
                    } else {
                        $this->addTimeFilter($filter, $timeFilterCompoundWhere, $firtsTimeFilter);
                    }
                } else {
                    $filter->addTo($this->_selectBuilder->where);
                }    
            } else {
                $this->addFilter($filter);
            }
        }
    }

    private function addTimeFilter(Gpf_SqlBuilder_Filter $filter, Gpf_SqlBuilder_CompoundWhereCondition $timeFilterCompoundWhere, $firtsTimeFilter = null) {
        $logicOperator = 'AND';
        if (!is_null($firtsTimeFilter) && Gpf_Common_DateUtils::getServerHours($firtsTimeFilter->getValue()) > Gpf_Common_DateUtils::getServerHours($filter->getValue())) {
            $logicOperator = 'OR';
        }
        $operator = $filter->getRawOperator();
        $timeFilterCompoundWhere->add("HOUR(".$filter->getCode().")", $operator->getSqlCode(), Gpf_Common_DateUtils::getServerHours($filter->getValue()), $logicOperator, $operator->getDoQuote());
    }

    private function isTimeFilter(Gpf_SqlBuilder_Filter $filter) {
        $operator = $filter->getRawOperator();
        if ($operator->getCode() == 'T>=' || $operator->getCode() == 'T<=' || $operator->getCode() == 'T<') {
            return true;
        }
        return false;
    }

    private function existsTwoTimeFilters() {
        $countTimeFilters = 0;
        foreach ($this->filters as $filter) {
            if ($this->isTimeFilter($filter)) {
                $countTimeFilters ++;
            }
            if ($countTimeFilters == 2) {
                return true; 
            }
        }
        return false;
    }

    private function addTimeFilterCompoundWhere(Gpf_SqlBuilder_CompoundWhereCondition $timeFilterCompoundWhere) {
        $this->_selectBuilder->where->addCondition($timeFilterCompoundWhere);
        return $timeFilterCompoundWhere;
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
    }

    abstract protected function buildFrom();

    protected function buildGroupBy() {
    }

    protected function getOffSet() {
        return $this->offset;
    }

    protected function getLimit() {
        return $this->limit;
    }

    /**
     * Created result RecordSet from data columns.
     * Can be overridden to add more columns to the result RecordSet
     *
     * @return Gpf_Data_RecordSet
     */
    protected function initResult() {
        $result = new Gpf_Data_RecordSet();
        foreach ($this->dataColumns as $column) {
            $result->getHeader()->add($column->getId());
        }
        return $result;
    }

    /**
     *
     * @return Gpf_Data_RecordSet
     */
    protected function loadResultData() {
        $result = $this->initResult();
        foreach ($this->createRowsIterator() as $row) {
            $result->add($row);
        }
        //TODO: to be removed, use HasTableFilter interface
        return $this->afterExecute($result);
    }

    /**
     * @return Gpf_View_GridService_RowsIterator
     */
    protected function createRowsIterator() {
        if($this instanceof Gpf_View_Grid_HasRowFilter) {
            return new Gpf_View_GridService_RowsIterator($this->_selectBuilder->getAllRowsIterator(), $this);
        }
        return new Gpf_View_GridService_RowsIterator($this->_selectBuilder->getAllRowsIterator());
    }

    //////////////////////////////////////////////////////////////////////////////
    // EXPORT refactor
    /**
     * Set name for cvs exported file. $fileName must be without extension.
     *
     * @param String $fileName
     */
    public function setExportFileName($fileName) {
        $this->_fileName = $fileName;
    }

    private function addPostParams(Gpf_Rpc_Params $params) {        
        if ($params->exists('fields')) {
        	$json = new Gpf_Rpc_Json();
            foreach ($params->get('fields') as $field) {
                if (!$params->exists($field[0])) {
                    $decodedItem = $json->decode($field[1]);
                    if ($decodedItem !== null || $decodedItem != '') {
                        $params->add($field[0], $decodedItem);
                    } else {
                        $params->add($field[0], $field[1]);
                    }
                }
            }
        }
    }

    protected function initParamsForCSVFile(Gpf_Rpc_Params $params) {
        $this->addPostParams($params);        
        $this->init($params);
    }

    /**
     * Returns csv file with data for grid
     *
     * @service
     *
     * @param $filters
     * @param $sort_col
     * @param $sort_asc
     * @param Gpf_Data_RecordSet $columns
     * @param Gpf_Data_RecordSer $views
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        $this->initParamsForCSVFile($params);

        if ($this->_fileName != null) {
            $fileName = $this->_fileName.".csv";
        } else {
            $fileName = "grid.csv";
        }
        $this->createResultSelect();
        $result = $this->initResult();
        $dataHeader = $this->createHeader($params->get("columns"));
        $dataHeader = $this->insertRequiredColumnsToCSVHeader($dataHeader);
        $csvGenerator = new Gpf_Csv_GeneratorResponse($fileName, $dataHeader, null, $this->createExportFileHeader($dataHeader));
        foreach ($iterator = $this->createRowsIterator() as $row) {
            $csvGenerator->add($row);
        }

        return $csvGenerator->getFile();
    }
    
    private function insertRequiredColumnsToCSVHeader($dataHeader) {
    	foreach ($this->_requiredColumns as $record) {
    		if ($record->get('id') != self::ACTIONS && !in_array($record->get('id'), $dataHeader)) {
    		  $dataHeader[] = $record->get('id');
    		}
    	}
    	return $dataHeader;
    }

    /**
     * @return array
     */
    protected function createHeader($views) {
        $header = array();
        for ($i = 2; $i < count($views); $i++) {
            if ($views[$i][0] === self::ACTIONS) {
                continue;
            }
            $header[] = $views[$i][0];
        }
        return $header;
    }

    //TODO - IIF Export Format - easy solution for adding other export button than CSV, if needed other formats need to refactor (protected for IIF export function used in Pap_Merchants_Payout_PayoutsHistoryGrid)
    /**
     *
     * @return Gpf_Data_RecordSet
     */
    protected function getResultForCSV() {
        $this->createResultSelect();
        return $this->loadResultData();
    }

    /**
     * @param array $header
     * @return array
     */
    protected function createExportFileHeader(array $dataHeader) {
        $this->initViewColumns();
        $header = array();
        foreach ($dataHeader as $id) {
            if (array_key_exists($id, $this->viewColumns)) {
                $header[] = $this->viewColumns[$id]->getName();
                continue;
            }
            $header[] = $id;
        }
        return $header;
    }

    /**
     * HACK: mosso MySQL servers can not handle large result sets so the select has to be splitted
     */
    protected function doMossoHack(Gpf_DbEngine_Table $primaryTable, $primaryTableAlias, $primaryColumnName) {
        $orderSelect = new Gpf_SqlBuilder_SelectBuilder();
        $orderSelect->cloneObj($this->_selectBuilder);
        $orderSelect->select = new Gpf_SqlBuilder_SelectClause();
        $orderSelect->select->add($primaryTableAlias.'.'.$primaryColumnName, 'idCol');
        foreach ($orderSelect->orderBy->getAllOrderColumns() as $orderColumns) {
            $dataColumn = $this->dataColumns[$orderColumns->getName()];
            $orderSelect->select->add($dataColumn->getName(), $dataColumn->getId());
        }

        $this->_selectBuilder->from = new Gpf_SqlBuilder_FromClause();
        $this->_selectBuilder->from->addSubselect($orderSelect, 'ors');
        $this->_selectBuilder->from->addInnerJoin($primaryTable->name(), $primaryTableAlias,
        $primaryTableAlias.'.'.$primaryColumnName.'=ors.idCol');
        $i = 0;
        foreach ($orderSelect->from->getAllFroms() as $fromClause) {
            if ($i++ == 0) {
                continue;
            }
            $this->_selectBuilder->from->addClause($fromClause);
        }
        $this->_selectBuilder->limit = new Gpf_SqlBuilder_LimitClause();
    }

}

class Gpf_View_GridService_NullRowFilter implements Gpf_View_Grid_HasRowFilter {
    /**
     * @param $row
     * @return DataRow or null
     */
    public function filterRow(Gpf_Data_Row $row) {
        return $row;
    }
}

class Gpf_View_GridService_RowsIterator implements Iterator {
    /**
     * @var Iterator
     */
    private $iterator;
    /**
     * @var Gpf_View_Grid_HasRowFilter
     */
    private $filter;

    public function __construct(Iterator $iterator, Gpf_View_Grid_HasRowFilter $filter = null) {
        $this->iterator = $iterator;
        $this->filter = $filter;
        if ($this->filter === null) {
            $this->filter = new Gpf_View_GridService_NullRowFilter();
        }
    }

    public function current() {
        return $this->row;
    }

    public function key() {
        return $this->row->get(Gpf_View_GridService::KEY_COLUMN_ID);
    }

    public function next() {
        $this->iterator->next();
        $this->skipNullRows();
    }

    public function rewind() {
        $this->iterator->rewind();
        $this->skipNullRows();

    }

    private function skipNullRows() {
        $this->row = null;
        while(true) {
            if(!$this->iterator->valid()) {
                return;
            }
            $this->row = $this->filter->filterRow($this->iterator->current());
            if ($this->row !== null) {
                return;
            }
            $this->iterator->next();
        }
    }

    public function valid() {
        return $this->row !== null;
    }
}

class Gpf_View_GridService_IdsIterator implements Iterator {
    /**
     *
     * @var Gpf_View_GridService_RowsIterator
     */
    private $iterator;
    private $index;

    public function __construct(Gpf_View_GridService_RowsIterator $iterator) {
        $this->iterator = $iterator;
    }

    public function current() {
        return $this->iterator->key();
    }

    public function key() {
        return $this->index;
    }

    public function next() {
        $this->index++;
        $this->iterator->next();
    }

    public function rewind() {
        $this->iterator->rewind();
        $this->index = 0;
    }

    public function valid() {
        return $this->iterator->valid();
    }
}

?>
