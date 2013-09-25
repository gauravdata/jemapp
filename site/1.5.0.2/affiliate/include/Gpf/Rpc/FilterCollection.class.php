<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Filter.class.php 20774 2008-09-09 10:26:31Z mbebjak $
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
class Gpf_Rpc_FilterCollection extends Gpf_Object implements IteratorAggregate {

    /**
     * @var array of Gpf_SqlBuilder_Filter
     */
    private $filters;

    public function __construct(Gpf_Rpc_Params $params = null) {
        $this->filters = array();
        if ($params != null) {
            $this->init($params);
        }
    }
    
    public function add(array $filterArray) {
    	$this->filters[] = new Gpf_SqlBuilder_Filter($filterArray);
    }

    private function init(Gpf_Rpc_Params $params) {
        $filtersArray = $params->get("filters");
        if (!is_array($filtersArray)) {
            return;
        }
        foreach ($filtersArray as $filterArray) {
            $this->add($filterArray);
        }
    }

    /**
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->filters);
    }

    public function addTo(Gpf_SqlBuilder_WhereClause $whereClause) {
        foreach ($this->filters as $filter) {
            $filter->addTo($whereClause);
        }
    }

    /**
     * Returns first filter with specified code.
     * If filter with specified code does not exists null is returned.
     *
     * @param string $code
     * @return array<Gpf_SqlBuilder_Filter>
     */
    public function getFilter($code) {
    	$filters = array();
        foreach ($this->filters as $filter) {
            if ($filter->getCode() == $code) {
                $filters[] = $filter;
            }
        }
        return $filters;
    }
    
    public function isFilter($code) {
        foreach ($this->filters as $filter) {
            if ($filter->getCode() == $code) {
                return true;
            }
        }
        return false;
    }
    
    public function getFilterValue($code) {
        $filters = $this->getFilter($code);
        if (count($filters) == 1) {
            return $filters[0]->getValue();
        }
        return "";
    }

    public function matches(Gpf_Data_Record $row) {
        foreach ($this->filters as $filter) {
            if (!$filter->matches($row)) {
                return false;
            }
        }
        return true;
    }

    public function getSize() {
        return count($this->filters);
    }
}
?>
