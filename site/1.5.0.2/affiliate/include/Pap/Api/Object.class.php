<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */

class Pap_Api_Object extends Gpf_Object {
    private $session;
    protected $class = '';
    private $message = '';
    
    const FIELD_NAME  = "name";
    const FIELD_VALUE = "value";
    const FIELD_ERROR = "error";
    const FIELD_VALUES = "values";
            
    /**
     * @var Gpf_Data_IndexedRecordSet
     */
    private $fields;
        
    public function __construct(Gpf_Api_Session $session) {
    	$this->session = $session;
        $this->fields = new Gpf_Data_IndexedRecordSet(self::FIELD_NAME);

        $header = new Gpf_Data_RecordHeader();
        $header->add(self::FIELD_NAME);
        $header->add(self::FIELD_VALUE);
        $header->add(self::FIELD_VALUES);
        $header->add(self::FIELD_ERROR);
        
        $this->fields->setHeader($header);
    }
    
    public function setField($name, $value) {
        $record = $this->fields->createRecord($name);
        $record->set(self::FIELD_VALUE, $value);
        
        $this->fields->add($record);
    }
    
    public function getField($name) {
       	try {
       		$record = $this->fields->getRecord($name);
       		return $record->get(self::FIELD_VALUE);
       	} catch(Exception $e) {
       		return '';
       	}
    }
    
    public function addErrorMessages(Gpf_Data_IndexedRecordSet $fields) {
    	foreach($fields as $field) {
			if($field->get(self::FIELD_ERROR) != '') {
   				$this->message .= $field->get(self::FIELD_NAME).' - '.$field->get(self::FIELD_ERROR).'<br>';
			}
		}    	
    }
    
    public function setFields(Gpf_Data_IndexedRecordSet $fields) {
    	foreach($fields as $field) {
			$this->setField($field->get(self::FIELD_NAME), $field->get(self::FIELD_VALUE)); 
		}    	
    }
    
    public function getFields() {
    	return $this->fields;
    }
    
    public function getSession() {
    	return $this->session;
    }
    
    public function getMessage() {
    	return $this->message;
    }
    
    protected function getPrimaryKey() {
    	throw new Exception("You have to define method getPrimaryKey() in the extended class!"); 
    }
    
    protected function getGridRequest() {
    	throw new Exception("You have to define method getGridRequest() in the extended class!"); 
    }
    
    protected function fillFieldsToGridRequest($request) {
        foreach($this->fields as $field) {
            if($field->get(self::FIELD_VALUE) != '') {
                $request->addFilter($field->get(self::FIELD_NAME), "L", $field->get(self::FIELD_VALUE));
            }
        }
    }
        
    protected function getPrimaryKeyFromFields() {
    	$request = $this->getGridRequest();    	
		if($request == null) {
			throw new Exception("You have to set ".$this->getPrimaryKey()." before calling load()!");
		}
		
        $this->fillFieldsToGridRequest($request);		
		
        $request->setLimit(0, 1);
		$request->sendNow();
		$grid = $request->getGrid();
		if($grid->getTotalCount() == 0) {
			throw new Exception("No rows found!");
		}
		if($grid->getTotalCount() > 1) {
			throw new Exception("Too may rows found!");
		}
		$recordset = $grid->getRecordset();
	    
		foreach($recordset as $record) {
    		$this->setField($this->getPrimaryKey(), $record->get($this->getPrimaryKey()));
    		break;
		}
    }
    
    protected function afterCallRequest() {
    }
    
    private function primaryKeyIsDefined() {
    	$field =  $this->getField($this->getPrimaryKey());
    	if($field == null || $field == '') {
    		return false;
    	}
    	return true;
    }
    
    /**
     * function checks if at least some field is filled 
     * (we'll use that field as filter for the grid)
     *
     */
    private function someFieldIsFilled() {
    	foreach($this->fields as $field) {
    		if($field->get(self::FIELD_VALUE) != '') {
    			return true;
    		}
    	}
    	
    	return false;
    }
    
    private function callRequest($method) {
    	$this->message = '';
 	
    	$request = new Gpf_Rpc_FormRequest($this->class, $method, $this->session);
        $this->beforeCallRequest($request);
	   	foreach($this->getFields() as $field) {
	   		if($field->get(self::FIELD_VALUE) != null) {
				$request->setField($field->get(self::FIELD_NAME), $field->get(self::FIELD_VALUE));
	   		}    	
		}
    	
		try {
    		$request->sendNow();
		} catch(Gpf_Exception $e) {
			if(strpos($e->getMessage(), 'Row does not exist') !== false) {
				throw new Exception("Row with this ID does not exist");
			}
		}
		
		$form = $request->getForm();
		if($form->isError()) {
			$this->message = $form->getErrorMessage();
			$this->addErrorMessages($form->getFields());
			return false;
		} else {
			$this->message = $form->getInfoMessage();
		}
		
		$this->setFields($form->getFields());

    	$this->afterCallRequest();
    	
		return true;
	}
    
	/**
	 * @throws Exception
	 */
    public function load() {
		if(!$this->primaryKeyIsDefined()) {
			if($this->getGridRequest() == null) {
				throw new Exception("You have to set ".$this->getPrimaryKey()." before calling load()!");
			}
			
			if(!$this->someFieldIsFilled()) {
				throw new Exception("You have to set at least one field before calling load()!");
			}
			
			$this->getPrimaryKeyFromFields();
		}
		
		$this->setField("Id", $this->getField($this->getPrimaryKey()));	
		
    	return $this->callRequest("load");
    }
    
    /**
     * @throws Exception
     */
    public function save() {
    	if(!$this->primaryKeyIsDefined()) {
    		throw new Exception("You have to set ".$this->getPrimaryKey()." before calling save()!");
    	}
   		$this->setField("Id", $this->getField($this->getPrimaryKey()));	
    	
    	return $this->callRequest("save");
    }
    
    public function add() {
    	$this->fillEmptyRecord(); 
    	    	
    	return $this->callRequest("add");
    }
    
    protected function beforeCallRequest(Gpf_Rpc_FormRequest $request) {
    }
}
?>
