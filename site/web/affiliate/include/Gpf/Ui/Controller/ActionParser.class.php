<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 22261 2008-11-11 13:26:19Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

class Gpf_Ui_Controller_ActionParser extends Gpf_Object {
    /**
     *
     * @var Gpf_Ui_Controller_Action
     */
    protected $action;

    /**
     *
     * @var Gpf_Ui_Controller_Url
     */
    protected $url;
    private $actionLength = 1;
    protected $actionControllers = array();

    public function __construct() {
    }

    /**
     *
     * keyword1-keywordN_param1_paramN_ACTIONVALUE?qParam1=qValue1&qParamN=qValueN
     *
     * @param unknown_type $url
     */
    public function parse($url) {
        $urlParser = new Gpf_Ui_Controller_UrlParser();
        $this->url = $urlParser->parse($url);

        $this->action = new Gpf_Ui_Controller_Action();
        $this->parseAction();

        parse_str($this->url->getQuery(), $params);

        foreach ($params as $name => $value) {
            $this->action->setQueryParam($name, $value);
        }
    }
    
    public function getUrl(Gpf_Ui_Page $controller) {
        $url = new Gpf_Ui_Controller_Url();

        $pathString = '';
        if($controller->getKeyword() != '') {
            $pathString = $controller->getKeyword() . '_';
        }
        foreach ($controller->getParams() as $name => $value) {
            $pathString .= $name . $value . '_';
        }
        $pathString .= $controller->getCode() . $controller->getValue();
        $url->setPathString($pathString);

        $query = '';
        foreach ($controller->getQueryParams() as $name => $value) {
            $query .= $name . '=' . urlencode($value) . '&';
        }
        $query = rtrim($query, '&');
        $url->setQuery($query);
        return $url; 
    }
    
    public function getUrlString(Gpf_Ui_Page $controller) {
        $urlParser = new Gpf_Ui_Controller_UrlParser();
        return $urlParser->toString($this->getUrl($controller));
    }

    /**
     *
     * @return Gpf_Ui_Page
     */
    public function createActionController() {
        if(array_key_exists($this->action->getActionCode(), $this->actionControllers)) {
            $class = $this->actionControllers[$this->action->getActionCode()];
            $reflectionClass = new ReflectionClass($class);
            $handler = $reflectionClass->newInstance();
            $handler->setAction($this->action);
            $handler->setUrlController($this->url);
            return $handler;
        }
        throw new Gpf_Exception('No page controller found');
    }

    protected function parseAction() {
        $lastPath = $this->url->getLastPath();

        $pos = strrpos($lastPath, '_');
        if(false !== $pos) {
            $this->action->setActionCode(substr($lastPath, $pos + 1, $this->actionLength));
            $this->action->setValue(substr($lastPath, $pos + 1 + $this->actionLength));
            $lastPath = substr($lastPath, 0, $pos);
        } else {
            $this->action->setActionCode($lastPath);
            return;
        }
        
        $pos = strpos($lastPath, '_');
        if(false !== $pos) {
            $params = substr($lastPath, $pos + 1);
            foreach (explode('_', $params) as $param) {
                $this->action->setParam(substr($param, 0, 1), substr($param, 1));
            }
            $lastPath = substr($lastPath, 0, $pos);
        }
        $this->action->setKeyword($lastPath);
    }

}
