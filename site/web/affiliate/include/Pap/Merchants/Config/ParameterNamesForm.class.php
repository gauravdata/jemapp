<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: ParameterNamesForm.class.php 16669 2008-03-25 16:13:22Z mjancovic $
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
class Pap_Merchants_Config_ParameterNamesForm extends Gpf_Object {  
    
    /**
     * @service parameter_names read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $form->setField('affiliateId',
            Gpf_Settings::get(Pap_Settings::PARAM_NAME_USER_ID));
            
        $form->setField('bannerId',
            Gpf_Settings::get(Pap_Settings::PARAM_NAME_BANNER_ID));
       
        $form->setField('campaignId',
            Gpf_Settings::get(Pap_Settings::PARAM_NAME_CAMPAIGN_ID));
            
        $form->setField('data1',
            Gpf_Settings::get(Pap_Settings::PARAM_NAME_EXTRA_DATA . '1'));
            
        $form->setField('data2',
            Gpf_Settings::get(Pap_Settings::PARAM_NAME_EXTRA_DATA . '2'));
            
        $form->setField('destinationURL',
            Gpf_Settings::get(Pap_Settings::PARAM_NAME_DESTINATION_URL));
                  
        return $form;
    }
    
    /**
     *
     * @service parameter_names write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        Gpf_Settings::set(Pap_Settings::PARAM_NAME_USER_ID, $form->getFieldValue('affiliateId'));
        Gpf_Settings::set(Pap_Settings::PARAM_NAME_BANNER_ID, $form->getFieldValue('bannerId'));
        Gpf_Settings::set(Pap_Settings::PARAM_NAME_CAMPAIGN_ID, $form->getFieldValue('campaignId'));
        Gpf_Settings::set(Pap_Settings::PARAM_NAME_EXTRA_DATA . '1', $form->getFieldValue('data1'));
        Gpf_Settings::set(Pap_Settings::PARAM_NAME_EXTRA_DATA . '2', $form->getFieldValue('data2'));
        Gpf_Settings::set(Pap_Settings::PARAM_NAME_DESTINATION_URL, $form->getFieldValue('destinationURL'));
        
        $form->setInfoMessage($this->_("Parameter names saved"));
        return $form;
    }
}

?>
