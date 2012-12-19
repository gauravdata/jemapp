<?php 
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class Pap_Common_ActionProcessorCompoundContext extends Gpf_Object {
    
    /*
     * @var Pap_Contexts_Tracking
     */
    private $context;
    
    /**
     * @var Pap_Tracking_Action_ActionProcessor
     */
    private $actionProcessor;
    
    private $isCommissionsAlreadySaved = false;
    
    public function __construct(Pap_Contexts_Action $context, Pap_Tracking_Action_ActionProcessor $actionProcessor){
        $this->context = $context;
        $this->actionProcessor = $actionProcessor;
    }
    
    /**
     * @return Pap_Contexts_Action
     */
    public function getContext(){
        return $this->context;
    }

    /**
     * @return Pap_Tracking_Action_ActionProcessor
     */
    public function getActionProcessor() {
        return $this->actionProcessor;
    }
    
    public function setCommissionsAlreadySaved($value) {
        $this->isCommissionsAlreadySaved = $value;
    }
 
    public function getCommissionsAlreadySaved() {
        return $this->isCommissionsAlreadySaved;
    }
}

?>
