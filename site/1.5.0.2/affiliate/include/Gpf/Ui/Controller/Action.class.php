<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Action.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

class Gpf_Ui_Controller_Action extends Gpf_Object {
    private $actionCode;
    private $value;
    private $keyword;
    private $params = array();
    private $queryParams = array();

    public function setActionCode($code) {
        $this->actionCode = $code;
    }
    public function setValue($value) {
        $this->value = $value;
    }
    
    public function setParam($name, $value) {
        $this->params[$name] = $value;    
    }
    
    public function getParam($name) {
        if(isset($this->params[$name])) {
            return $this->params[$name];
        }    
        return null;
    }
    
    public function getParams() {
        return $this->params;    
    }
    
    public function getQueryParams() {
        return $this->queryParams;    
    }
    
    public function setQueryParam($name, $value = null) {
        if($value === null) {
            unset($this->queryParams[$name]);
            return;
        }
        $this->queryParams[$name] = $value;    
    }
    
    public function setKeyword($keyword) {
        $this->keyword = $keyword;
    }
    
    public function getKeyword() {
        return $this->keyword;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function getActionCode() {
        return $this->actionCode;
    }
}
