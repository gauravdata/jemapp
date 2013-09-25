<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MashupWidget.class.php 20051 2008-08-21 16:21:36Z aharsani $
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
abstract class Gpf_Ui_MashupWidget extends Gpf_Ui_Widget {
    /**
     *
     * @var array
     */
    private $clientWidgets = array();
    
    /**
     *
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Object
     */
    public function getWidget(Gpf_Rpc_Params $params) {
        return $this->getWidgetResponse($this->renderHtml());
    }

    /**
     *
     * @param unknown_type $html
     * @return Gpf_Rpc_Object
     */
    private function getWidgetResponse($html = null) {
        $response = new Gpf_Rpc_Object();
        $response->html = $html;
        $response->code = $this->getCode();
        $response->widgets = new stdClass();
        foreach ($this->clientWidgets as $widgetClass => $widgets) {
            $response->widgets->$widgetClass = $widgets->toObject();
        }
        return $response;
    }

    abstract protected function renderHtml();

    final public function render() {
        $html = $this->renderHtml();
        Gpf_Rpc_CachedResponse::add($this->getWidgetResponse(), get_class($this), 'getWidget');
        return '<div id="' . $this->getCode() . '">' . $html . '</div>';
    }

    protected function initClientWidget(Gpf_Ui_ClientWidget $widget) {
        if(array_key_exists($widget->getName(), $this->clientWidgets)) {
            throw new Gpf_Exception("Widget name already exist");
        }
        $this->clientWidgets[$widget->getName()] = $widget;
    }

    /**
     *
     * @param unknown_type Gpf_Ui_ClientWidget
     */
    protected function getClientWidget($name) {
        if(!array_key_exists($name, $this->clientWidgets)) {
            throw new Gpf_Exception("Widget doesn't exist");
        }
        return $this->clientWidgets[$name];
    }

    /**
     *
     * @param unknown_type $name
     * @param Gpf_Data_Record $data
     * @return String
     */
    protected function addClientWidget($name, Gpf_Data_Row $data) {
        $widget = $this->getClientWidget($name);
        $widget->addData($data);
        return $widget->getLastId();
    }
}

?>
