<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Action.class.php 24612 2009-06-11 13:28:02Z aharsani $
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
class Gpf_Rpc_Action extends Gpf_Object implements Gpf_Rpc_Serializable {
    private $errorMessage = "";
    private $infoMessage = "";
    private $successCount = 0;
    private $errorCount = 0;
    /**
     * @var Gpf_Rpc_Params
     */
    private $params; 
    
    const IDS = 'ids';
    const IDS_REQUEST = 'idsRequest';
    
    public function __construct(Gpf_Rpc_Params $params, $infoMessage = '', $errorMessage = '') {
        $this->params = $params;
        $this->infoMessage = $infoMessage;
        $this->errorMessage = $errorMessage;
    }

    public function getIds() {
        if ($this->params->exists(self::IDS)) {
            return new ArrayIterator($this->params->get(self::IDS));
        }
        if ($this->params->exists(self::IDS_REQUEST)) {
            return $this->getRequestIdsIterator();
        }
        throw new Gpf_Exception('No ids selected');
    }
    
    public function getParam($name) {
        return $this->params->get($name);
    }
    
    public function existsParam($name) {
        return $this->params->exists($name);
    }
    
    protected function getRequestIdsIterator() {
        $json = new Gpf_Rpc_Json();
        $requestParams = new Gpf_Rpc_Params($json->decode($this->params->get(self::IDS_REQUEST)));
        $c = $requestParams->getClass();
        $gridService = new $c;
        if(!($gridService instanceof Gpf_View_GridService)) {
            throw new Gpf_Exception(sprintf('%s is not Gpf_View_GridService class.', $requestParams->getClass()));
        }
        return $gridService->getIdsIterator($requestParams);
    }
    
    public function toObject() {
        $response = new stdClass();
        $response->success = Gpf::YES;
        
        $response->errorMessage = "";
        if ($this->errorCount > 0) {
            $response->success = "N";
            $response->errorMessage = $this->_($this->errorMessage, $this->errorCount);
        }
        
        $response->infoMessage = "";
        if ($this->successCount > 0) {
            $response->infoMessage = $this->_($this->infoMessage, $this->successCount);
        }
        
        return $response;
    }
    
    public function loadFromObject(stdClass $object) {
        $this->errorMessage = $object->errorMessage;
        $this->infoMessage = $object->infoMessage;

        if($object->success == Gpf::NO) {
            $this->addError();
        }
    }
    
    public function isError() {
        return $this->errorCount > 0;
    }
    
    public function toText() {
        if ($this->isError()) {
            return $this->_($this->errorMessage, $this->errorCount);
        } else {
            return $this->_($this->infoMessage, $this->successCount);
        }
    }

    public function setErrorMessage($message) {
        $this->errorMessage = $message;
    }
    
    public function getErrorMessage() {
        return $this->errorMessage;
    }
    
    public function setInfoMessage($message) {
        $this->infoMessage = $message;
    }

    public function addOk() {
        $this->successCount++;
    }

    public function addError() {
        $this->errorCount++;
    }
    
}

?>
