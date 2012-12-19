<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 20390 2008-08-29 13:11:12Z mbebjak $
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
class Pap_Affiliates_MerchantInfo extends Pap_Merchants_Config_MerchantForm {

    protected function getId(Gpf_Rpc_Form $form) {
       return Gpf_Settings::get(Pap_Settings::DEFAULT_MERCHANT_ID);
    }
    
    /**
     * @service merchant read_own
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = parent::load($params);
        
        $form->setField(Pap_Db_Table_Users::NOTE, $this->_localize($form->getFieldValue(Pap_Db_Table_Users::NOTE)));
        $form->setField(self::WELCOME_MESSAGE, $this->_localize($form->getFieldValue(self::WELCOME_MESSAGE)));
        
        $form->setField("rpassword", "");
        $form->setField("authtoken", "");
        $form->setField("roleid", "");
        
        return $form;
    }
    
}

?>
