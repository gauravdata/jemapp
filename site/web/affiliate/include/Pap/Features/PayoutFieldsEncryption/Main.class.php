<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
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
class Pap_Features_PayoutFieldsEncryption_Main extends Gpf_Plugins_Handler {

    /**
     * @return Pap_Features_PayoutFieldsEncryption_Main
     */
    public static function getHandlerInstance() {
        return new Pap_Features_PayoutFieldsEncryption_Main();
    }
    
    public function initSettings($context) {
        $context->addFileSetting(Pap_Features_PayoutFieldsEncryption_Config::ENCRYPT_KEY, '');
        $context->addFileSetting(Pap_Features_PayoutFieldsEncryption_Config::ENCRYPT_IV, '');
    }
    
    public function encodeValue(Gpf_Plugins_ValueContext $value) {
        $value->set($this->getEncoder()->encrypt($value->get()));
    }
    
    public function decodeValue(Gpf_Plugins_ValueContext $value) {
        $value->set($this->getEncoder()->decrypt($value->get()));
    }
    
    public function recodeAllValues($newKey, $newIv) {        
        $oldEncoder = $this->getEncoder();
        $newEncoder = new Pap_Features_PayoutFieldsEncryption_Encoder($newKey, $newIv);
        
        $userOptions = new Pap_Db_UserPayoutOption();

        foreach ($userOptions->loadCollection() as $userOption) {
            $cleanValue = $oldEncoder->decrypt($userOption->get(Pap_Db_Table_UserPayoutOptions::VALUE));
            $userOption->set(Pap_Db_Table_UserPayoutOptions::VALUE, $newEncoder->encrypt($cleanValue));
            $userOption->save();
        }
   }
    
    /**
     * @return Pap_Features_PayoutFieldsEncryption_Encoder
     */
    private function getEncoder() {
        return new Pap_Features_PayoutFieldsEncryption_Encoder(
                      Gpf_Settings::get(Pap_Features_PayoutFieldsEncryption_Config::ENCRYPT_KEY),
                      Gpf_Settings::get(Pap_Features_PayoutFieldsEncryption_Config::ENCRYPT_IV));
    }
}
?>
