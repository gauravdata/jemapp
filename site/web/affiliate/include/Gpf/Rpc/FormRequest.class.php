<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FormRequest.class.php 22730 2008-12-09 16:07:54Z mfric $
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
class Gpf_Rpc_FormRequest extends Gpf_Rpc_Request {
    /**
     * @var Gpf_Rpc_Form
     */
    private $fields;
    
    public function __construct($className, $methodName, Gpf_Api_Session $apiSessionObject = null) {
        parent::__construct($className, $methodName, $apiSessionObject);
        $this->fields = new Gpf_Rpc_Form();
    }
    
    public function send() {
        $this->addParam('fields', $this->fields->getFields());
        parent::send();
    }
    
    /**
     * @return Gpf_Rpc_Form
     */
    public function getForm() {
        $response = new Gpf_Rpc_Form();
        $response->loadFromObject($this->getStdResponse());
        return $response;
    }

    public function setField($name, $value) {
        if (is_scalar($value) || $value instanceof Gpf_Rpc_Serializable) {
            $this->fields->setField($name, $value);
        } else {
            throw new Gpf_Exception("Not supported value");
        }
    }
    
    public function setFields(Gpf_Data_IndexedRecordSet $fields) {
    	$this->fields->loadFieldsFromArray($fields->toArray());
    }    
}
?>
