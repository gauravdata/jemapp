<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: LoggingForm.class.php 18882 2008-06-27 12:15:52Z mfric $
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
class Pap_Merchants_Config_AffiliateGeneralSettingsForm extends Gpf_Object {
    
    /**
     * @service affiliate_settings read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	

        $form->setField("programName", Gpf_Settings::get(Pap_Settings::PROGRAM_NAME));
        $form->setField("programLogo", Gpf_Settings::get(Pap_Settings::PROGRAM_LOGO));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateGeneralSettingsForm.load', $form);
            
        return $form;
    }
	
    /**AFFILIATE_LOGOUT_URL
     * @service affiliate_settings write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
     	$form = new Gpf_Rpc_Form($params);
        
        Gpf_Settings::set(Pap_Settings::PROGRAM_NAME, $form->getFieldValue("programName"));
        Gpf_Settings::set(Pap_Settings::PROGRAM_LOGO, $form->getFieldValue("programLogo"));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateGeneralSettingsForm.save', $form);
        
        $form->setInfoMessage($this->_("Program name and logo saved"));
        return $form;
    }
    
    /**
     * @service affiliate_settings read
     * @param $fields
     */
    public function loadGeneral(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	
        $form->setField(Pap_Settings::AFFILIATE_LOGOUT_URL, Gpf_Settings::get(Pap_Settings::AFFILIATE_LOGOUT_URL));
        $form->setField(Pap_Settings::TIERS_VISIBLE_TO_AFFILIATE, Gpf_Settings::get(Pap_Settings::TIERS_VISIBLE_TO_AFFILIATE));
        $form->setField(Pap_Settings::AFFILIATE_CANNOT_CHANGE_HIS_USERNAME, Gpf_Settings::get(Pap_Settings::AFFILIATE_CANNOT_CHANGE_HIS_USERNAME));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateGeneralSettingsForm.loadGeneral', $form);
             
        return $form;
    }
	
    /**
     * @service affiliate_settings write
     * @param $fields
     */
    public function saveGeneral(Gpf_Rpc_Params $params) {
     	$form = new Gpf_Rpc_Form($params);
        
       	Gpf_Settings::set(Pap_Settings::AFFILIATE_LOGOUT_URL, $form->getFieldValue(Pap_Settings::AFFILIATE_LOGOUT_URL));
        
       	$tiersVisibleToAffilate = $form->getFieldValue(Pap_Settings::TIERS_VISIBLE_TO_AFFILIATE);
       	if($tiersVisibleToAffilate == '') {
       	    $tiersVisibleToAffilate = "-1";
       	}
       	Gpf_Settings::set(Pap_Settings::TIERS_VISIBLE_TO_AFFILIATE, $tiersVisibleToAffilate);
       	Gpf_Settings::set(Pap_Settings::AFFILIATE_CANNOT_CHANGE_HIS_USERNAME, $form->getFieldValue(Pap_Settings::AFFILIATE_CANNOT_CHANGE_HIS_USERNAME));
       	
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateGeneralSettingsForm.saveGeneral', $form);
            	
       	$form->setInfoMessage($this->_("Settings were saved"));
        return $form;
    }    
}

?>
