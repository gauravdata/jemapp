<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FormRequest.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Rpc_DataRequest extends Gpf_Rpc_Request {
    /**
     * @var Gpf_Rpc_Data
     */
    private $data;
    
    private $filters = array();
    
    public function __construct($className, $methodName, Gpf_Api_Session $apiSessionObject = null) {
        parent::__construct($className, $methodName, $apiSessionObject);
        $this->data = new Gpf_Rpc_Data();
    }
    
    /**
     * @return Gpf_Rpc_Data
     */
    public function getData() {
        $response = new Gpf_Rpc_Data();
        $response->loadFromObject($this->getStdResponse());
        return $response;
    }

    public function setField($name, $value) {
        if (is_scalar($value) || $value instanceof Gpf_Rpc_Serializable) {
            $this->data->setParam($name, $value);
        } else {
            throw new Gpf_Exception("Not supported value");
        }
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
    
    public function send() {
        $this->addParam('data', $this->data->getParams());
        
        if(count($this->filters) > 0) {
            $this->addParam("filters", $this->addFiltersParameter());
        }
        parent::send();
    }
    
    private function addFiltersParameter() {
        $filters = new Gpf_Rpc_Array();
        
        foreach($this->filters as $filter) {
            $filters->add($filter);
        }
        
        return $filters;
    }
}
?>
