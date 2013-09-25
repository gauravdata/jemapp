<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RichListBox.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
abstract class Gpf_Ui_Page extends Gpf_ModuleBase { 
    
    /**
     *
     * @var Gpf_Ui_Controller_Action
     */
    protected $action;

    private $outputController = null;
    private $widgetParams = array();
    protected $bodyTemplateName = 'module_body.stpl';
    
    /**
     * @var Gpf_Ui_Controller_Url
     */
    protected $urlController;

    public function __construct($gwtModuleName, $panelName, $roleType = '') {
        parent::__construct($gwtModuleName, $panelName, $roleType);
        $this->action = new Gpf_Ui_Controller_Action();
    }
    
    public function __clone() {
        $this->action = clone $this->action;
    }

    public function setCode($code) {
        $this->action->setActionCode($code);
    }

    public function setValue($value) {
        $this->action->setValue($value);
    }

    public function setKeyword($keyword) {
        $this->action->setKeyword($keyword);
    }

    public function getParams() {
        return $this->action->getParams();
    }

    public function getQueryParams() {
        return $this->action->getQueryParams();
    }

    public function setQueryParam($name, $value) {
        $this->action->setQueryParam($name, $value);
    }

    public function getQueryParam($paramName) {
        $params = $this->action->getQueryParams();
        if(array_key_exists($paramName, $params)) {
            return $params[$paramName];
        }
        throw new Gpf_Ui_Controller_NoQueryParameter($paramName);
    }

    public function getKeyword() {
        return $this->action->getKeyword();
    }

    public function getValue() {
        return $this->action->getValue();
    }

    public function getCode() {
        return $this->action->getActionCode();
    }

    public function getUrlPath() {
        $urlParser = new Gpf_Ui_Controller_UrlParser();
        return $urlParser->getUrlPath($this->getUrlObject());
    }
    
    public function getUrlQuery() {
        $urlParser = new Gpf_Ui_Controller_UrlParser();
        return $urlParser->getUrlQuery($this->getUrlObject());
    }
    
    public function setUrlController(Gpf_Ui_Controller_Url $url) {
        $this->urlController = $url;
    }
    
    /**
     * @return Gpf_Ui_Controller_Url
     */
    public function getUrlController() {
        return $this->urlController;
    }
    
    /**
     * @return Gpf_Ui_Controller_Url
     */
    protected function getUrlObject() {
        $this->encodeWidgetParams();
        $actionParser = new Gpf_Ui_Controller_ActionParser();
        return $actionParser->getUrl($this);
    }

    public function getUrl() {
        $urlParser = new Gpf_Ui_Controller_UrlParser();
        return $urlParser->toString($this->getUrlObject());
    }

    public function setAction(Gpf_Ui_Controller_Action $action) {
        $this->action = $action;
    }
    
    /**
     * @return Gpf_Ui_Controller_Action
     */
    public function getAction() {
        return $this->action;
    }

    public function setBodyTemplateName($name) {
        $this->bodyTemplateName = $name;
    }
    
    /**
     *
     * @return Gpf_Ui_Page
     */
    protected function getOutputController() {
        if($this->outputController !== null) {
            return $this->outputController;
        }
        return $this;
    }

    
    protected function forward(Gpf_Ui_Page $controller) {
        $controller->setAction($this->action);
        $controller->processPage();
        $this->outputController = $controller->getOutputController();
    }

    /**
     * @return Gpf_Templates_Template
     */
    protected function getBodyTemplate() {
        $template = new Gpf_Templates_Template($this->bodyTemplateName);
        $template->assign('content', $this->getContentTemplate()->getHTML());
        return $template;
    }
    
    /**
     * @return Gpf_Templates_Template
     */
    abstract protected function getContentTemplate();
        
    abstract public function processPage();

    public function parseWidgetParams(Gpf_Ui_Widget $widget) {
        try {
            $params = explode(',', $this->getQueryParam($widget->getCode()));
        } catch (Exception $e) {
            return;
        }

        $widgetParams = array();
        foreach ($params as $value) {
            $pair = explode(':', $value);
            $widgetParams[$pair[0]] = $pair[1];
        }
        $this->widgetParams[$widget->getCode()] = $widgetParams;
    }

    public function getWidgetParam(Gpf_Ui_Widget $widget, $name) {
        if(isset($this->widgetParams[$widget->getCode()][$name])) {
            return $this->widgetParams[$widget->getCode()][$name];
        }
        return null;
    }

    public function setWidgetParam(Gpf_Ui_Widget $widget, $name, $value) {
        $this->widgetParams[$widget->getCode()][$name] = $value;
    }

    public function clearWidgetParam(Gpf_Ui_Widget $widget) {
        $this->action->setQueryParam($widget->getCode());
        unset($this->widgetParams[$widget->getCode()]);
    }

    protected function encodeWidgetParams() {
        foreach ($this->widgetParams as $code => $params) {
            $this->setQueryParam($code, $this->encodeParams($params));
        }
    }
    
    public function render() {
        $this->processPage();
        $page = $this->getOutputController();            
        return $page->startAndGet();
    }
    
    public function startAndGet() {
        return parent::startAndGet();
    }
    
    private function encodeParams($params) {
        $encoded = '';
        foreach ($params as $name => $value) {
            $encoded .= $name . ':' . $value . ',';
        }
        return rtrim($encoded, ',');
    }
}
