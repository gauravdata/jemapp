<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Data.class.php 27677 2010-04-05 13:40:58Z jsimon $
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
class Gpf_Rpc_Data extends Gpf_Object implements Gpf_Rpc_Serializable {
	const NAME  = "name";
    const VALUE = "value";
    const DATA = "data";
    const ID = "id";
    
	/**
	 * @var Gpf_Data_IndexedRecordSet
	 */
    private $params;
    
    /**
     * @var string
     */
    private $id;
    
    
    /**
     * @var Gpf_Rpc_FilterCollection
     */
    private $filters;
    
    /**
     * @var Gpf_Data_IndexedRecordSet
     */
    private $response;
    
    /**
     *
     * @return Gpf_Data_IndexedRecordSet
     */
    public function getParams() {
        return $this->params;
    }
    
    /**
     * Create instance to handle DataRequest
     *
     * @param Gpf_Rpc_Params $params
     */
    public function __construct(Gpf_Rpc_Params $params = null) {
    	if($params === null) {
    	    $params = new Gpf_Rpc_Params();
    	}
        
    	$this->filters = new Gpf_Rpc_FilterCollection($params);
        
    	$this->params = new Gpf_Data_IndexedRecordSet(self::NAME);
    	$this->params->setHeader(array(self::NAME, self::VALUE));
        
        if ($params->exists(self::DATA) !== null) {
            $this->loadParamsFromArray($params->get(self::DATA));
        }
        
        $this->id = $params->get(self::ID);
        
        $this->response = new Gpf_Data_IndexedRecordSet(self::NAME);
        $this->response->setHeader(array(self::NAME, self::VALUE));
    }
    
   /**
     * Return id
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Return parameter value
     *
     * @param String $name
     * @return unknown
     */
    public function getParam($name) {
        try {
           return $this->params->getRecord($name)->get(self::VALUE);
        } catch (Gpf_Data_RecordSetNoRowException $e) {
           return null;
        }
    }
    
    public function setParam($name, $value) {
        self::setValueToRecordset($this->params, $name, $value);
    }
    
    public function loadFromObject(array $object) {
        $this->response->loadFromObject($object);
        $this->params->loadFromObject($object);
    }
        
    /**
     * @return Gpf_Rpc_FilterCollection
     */
    public function getFilters() {
    	return $this->filters;
    }

    private static function setValueToRecordset(Gpf_Data_IndexedRecordSet $recordset, $name, $value) {
        try {
           $record = $recordset->getRecord($name);
        } catch (Gpf_Data_RecordSetNoRowException $e) {
           $record = $recordset->createRecord();
           $record->set(self::NAME, $name);
           $recordset->addRecord($record);
        }
        $record->set(self::VALUE, $value);
    }
    
    public function setValue($name, $value) {
        self::setValueToRecordset($this->response, $name, $value);
    }
    
    public function getSize() {
        return $this->response->getSize();
    }
    
    public function getValue($name) {
        try {
            return $this->response->getRecord($name)->get(self::VALUE);
        } catch (Gpf_Data_RecordSetNoRowException $e) {
        }
        return null;
    }
    
    public function toObject() {
    	return $this->response->toObject();
    }

    public function toText() {
    	return $this->response->toText();
    }

    private function loadParamsFromArray($data) {
        for ($i = 1; $i < count($data); $i++) {
            $this->params->add($data[$i]);
        }
    }
}
?>
