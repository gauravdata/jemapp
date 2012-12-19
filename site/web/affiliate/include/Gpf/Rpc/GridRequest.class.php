<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RecordSetRequest.class.php 18002 2008-05-13 18:31:39Z aharsani $
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
class Gpf_Rpc_GridRequest extends Gpf_Rpc_Request {

	private $filters = array();
	
	private $limit = '';
	private $offset = '';
	
	private $sortColumn = '';
	private $sortAscending = false;
	
    /**
     * @return Gpf_Data_Grid
     */
    public function getGrid() {
        $response = new Gpf_Data_Grid();
        $response->loadFromObject($this->getStdResponse());
        return $response;
    }
    
    public function getFilters() {
        return $this->filters;
    }

    /**
     * 
     * @return Gpf_Rpc_Params
     */
    public function getParams() {
        return $this->params;
    }

	/**
     * adds filter to grid
     *
     * @param unknown_type $code
     * @param unknown_type $operator
     * @param unknown_type $value
     */
    public function addFilter($code, $operator, $value) {
    	$this->filters[] = new Gpf_Data_Filter($code, $operator, $value);
    }
    
    public function setLimit($offset, $limit) {
    	$this->offset = $offset;
    	$this->limit = $limit;
    }
    
    public function setSorting($sortColumn, $sortAscending = false) {
    	$this->sortColumn = $sortColumn;
    	$this->sortAscending = $sortAscending;
    }
    
    public function send() {
    	if(count($this->filters) > 0) {
    		$this->addParam("filters", $this->getFiltersParameter());
    	}
		if($this->sortColumn !== '') {
			$this->addParam("sort_col", $this->sortColumn);
			$this->addParam("sort_asc", ($this->sortAscending ? 'true' : 'false'));
		}
		if($this->offset !== '') {
			$this->addParam("offset", $this->offset);
		}
		if($this->limit !== '') {
			$this->addParam("limit", $this->limit);
		}
		
    	parent::send();
    }
    
    protected function getFiltersParameter() {
    	$filters = new Gpf_Rpc_Array();
    	
    	foreach($this->filters as $filter) {
    		$filters->add($filter);
    	}
    	
    	return $filters;
    }
}


?>
