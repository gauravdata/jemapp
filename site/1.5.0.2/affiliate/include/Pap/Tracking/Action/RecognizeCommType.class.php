<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
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
class Pap_Tracking_Action_RecognizeCommType extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
    
    public function recognize(Pap_Contexts_Tracking $context) {
        return $this->getCommissionType($context);
    }
    
     /**
     * recognizes commission type for campaign
     *
     * @param Pap_Plugins_Tracking_Action_Context $context
     */
    public function getCommissionType(Pap_Contexts_Action $context) {
        $campaign = $context->getCampaignObject();
        
        $context->debug('Recognizing commission type started');
        $actionCode = $context->getActionCodeFromRequest();
        if ($actionCode == null || $actionCode == '') {
            $type = Pap_Common_Constants::TYPE_SALE;
        } else {
            $type = Pap_Common_Constants::TYPE_ACTION;
        }
        
        try {
            $context->debug('    Checking commission type : '.$type.' is in campaign');
            $commissionType = $campaign->getCommissionTypeObject($type, $context->getActionCodeFromRequest(), $context->getVisit()->getCountryCode());
        } catch (Pap_Tracking_Exception $e) {          
            $context->debug("    STOPPING, This commission type is not supported by current campaign or is NOT enabled! ");
            throw $e;
        }
        $context->setCommissionTypeObject($commissionType);
        
        $context->getTransaction(1)->setType($type);
        $context->debug('    Commission type set to: '.$type.', ID: '.$commissionType->getId());
        $context->debug('Recognizing commission type ended');
        $context->debug("");
    }    
}

?>
