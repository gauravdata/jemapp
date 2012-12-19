<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Versions.class.php 18552 2008-06-17 12:59:40Z aharsani $
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
abstract class Gpf_Install_Step extends Gpf_Object {
    const PART_DONE_TYPE = 'part_done';
    const RESPONSE_TYPE_NAME = 'type';
    const NEXT = 'next';
    const NEXT_STEP_NAME = 'nextStep';
    protected $name;
    protected $code;
    
    public function __construct() {
    }
    
    /**
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Serializable
     */
    abstract protected function execute(Gpf_Rpc_Params $params);
    
    /**
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Serializable
     * @service
     * @anonym
     */
    abstract public function load(Gpf_Rpc_Params $params);
    
    /**
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Serializable
     * @service
     * @anonym
     */
    final public function goNext(Gpf_Rpc_Params $params) {
//        if($this->getCode() != Gpf_Install_Manager::getInstance()->getCurrentStep()) {
//            $form = new Gpf_Rpc_Form();
//            $form->setErrorMessage('Permission denied.');
//            return $form;
//        }
        return $this->execute($params);
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function getName() {
        return $this->name;
    }
    
    protected function setResponseType(Gpf_Rpc_Form $form, $type) {
        $form->setField(self::RESPONSE_TYPE_NAME, $type);
    }
    
    protected function setNextStep(Gpf_Rpc_Form $form) { 
        $this->setResponseType($form, self::NEXT);
        $form->setField(self::NEXT_STEP_NAME, Gpf_Install_Manager::getInstance()->getNextStep());
    }
}
?>
