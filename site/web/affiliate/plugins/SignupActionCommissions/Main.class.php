<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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
 * @package PostAffiliatePro plugins
 */
class SignupActionCommissions_Main extends Gpf_Plugins_Handler {

	public static function getHandlerInstance() {
 		return new SignupActionCommissions_Main();
 	}

 	public function addCommission(Pap_Contexts_Signup $signupContext) {
 	    if (($user = $signupContext->getUserObject()) == null) {
 	        return;
 	    } 
 	    
 	    $campaignId = Gpf_Settings::get(SignupActionCommissions_Config::AFTER_SIGNUP_ACTION_CAMPAIGNID);
 	    $actionCode = Gpf_Settings::get(SignupActionCommissions_Config::AFTER_SIGNUP_ACTION_CODE);
 	    
 	    if ($actionCode == '') {
 	        Gpf_Log::error('SignupActionCommissions plugin: No Action code set');
 	        return;
 	    }
        
 	    $saleTracker = new Pap_Tracking_ActionTracker();
 	    
        $action = $saleTracker->createAction($actionCode);
        $action->setAffiliateID($user->getId());    
        $action->setStatus($user->getStatus());
        $action->setOrderId(SignupActionCommissions_Config::DEFAULT_ORDER_ID);
        if ($campaignId != '') {
          $action->setCampaignID($campaignId);
        }
        try {
            $saleTracker->register();
        } catch (Gpf_Exception $e) {
            Gpf_Log::error('SignupActionCommissions plugin: '.$e->getMessage());
        }
        
 	}
 	
    public function initSettings($context) {
        $context->addDbSetting(SignupActionCommissions_Config::AFTER_SIGNUP_ACTION_CODE, '');
        $context->addDbSetting(SignupActionCommissions_Config::AFTER_SIGNUP_ACTION_CAMPAIGNID, '');
    }
}

?>
