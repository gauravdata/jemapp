<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CampaignForm.class.php 23751 2009-03-12 09:24:00Z mbebjak $
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
class Pap_Merchants_Campaign_CampaignDetailsAdditionalForm extends Gpf_Plugins_Config {

    protected function initFields() {
    	Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CampaignDetailsAdditionalForm.initFields', $this);
    }

    /**     
     * @service campaign write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CampaignDetailsAdditionalForm.save', $form);
        
        return $form;
    }

    /**
     * @service campaign read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	
    	Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CampaignDetailsAdditionalForm.load', $form);
    	
    	return $form;
    }
}
?>
