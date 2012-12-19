<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Main.class.php 22261 2008-11-11 13:26:19Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

abstract class Gpf_Ui_Controller_Main implements Gpf_Rpc_Serializable {
    private $url;
    /**
     *
     * @var Gpf_Ui_Page
     */
    private static $currentController;

    /**
     * @return Gpf_Ui_Page
     */
    public static function getController() {
        return self::$currentController;
    }

    /**
     *
     * @service
     * @anonym
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Ui_Controller_Main
     */
    public function execute(Gpf_Rpc_Params $params) {
        try {
            $form = new Gpf_Rpc_Form($params);
            
            $this->url = $form->getFieldValue('page');
            $params = '';
            foreach ($form as $record) {
                if($record->get('name') !== 'page') {
                    $params .= $record->get('name') . '=' . $record->get('value') . '&'; 
                }
            }
            $params = rtrim($params, '&');
            if($params != '') {
                $this->url .= '?' . $params;
            }
        } catch (Exception $e) {
        }
        return $this;
    }

    public function toText() {
        self::$currentController = $this->createController();
        return self::$currentController->render();
    }

    public function toObject() {
        throw new Gpf_Exception("Unsupported");
    }

    /**
     * @return Gpf_Ui_Controller_ActionParser
     */
    abstract protected function createActionParser();

    /**
     * @return Gpf_Ui_Page
     */
    protected function createController() {
        $urlParser = $this->createActionParser();
        $urlParser->parse($this->url);
        return $urlParser->createActionController();
    }
}
