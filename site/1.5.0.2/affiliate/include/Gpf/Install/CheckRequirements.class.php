<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Gpf_Install_CheckRequirements extends Gpf_Install_Step {
    
    public function __construct() {
        parent::__construct();
        $this->code = 'Chek-Requirements';
        $this->name = $this->_('Check Requirements'); 
    }
    
    /**
     *
     * @service
     * @anonym
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function getHtml(Gpf_Rpc_Params $params) {
        $smarty = new Gpf_Templates_Template("check_requirements.stpl");
        return $smarty->getDataResponse();
    }
    
    /**
     * @param Gpf_Rpc_Params $params
     * @service
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
    }
    
    /**
     *
     * @return Gpf_Install_Requirements
     */
    protected function getRequirements() {
        return new Gpf_Install_Requirements();
    }
    
    /**
     *
     * @return Gpf_Rpc_Form
     */
    private function processStep() {
        $form = new Gpf_Rpc_Form();
        
        if(!$this->getRequirements()->isValid()) {
            $form->setErrorMessage($this->_("Please check system requirements and try again"));
        }
        return $form;
    }
    
    /**
     *
     * @return Gpf_Rpc_Form
     */
    protected function execute(Gpf_Rpc_Params $params) {
        $formParams = new Gpf_Rpc_Form($params);
        $form = $this->processStep();

        if($form->isSuccessful() && "1" != $formParams->getFieldValue('check')) {
            $this->setNextStep($form);
        }
        return $form;
    }
}
?>
