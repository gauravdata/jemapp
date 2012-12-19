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
 * @package PostAffiliate
 */
class SaleFraudProtection_Main extends Gpf_Plugins_Handler {
    private static $instance = false;
    
    private function __construct() {
    }
    
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new SaleFraudProtection_Main();
        }
        return self::$instance;
    }

    public function initSettings($context) {
        $context->addDbSetting(SaleFraudProtection_Config::SECRET_KEY, 'secretkey');
        $context->addDbSetting(SaleFraudProtection_Config::PARAM_NAME, '1');
    }

    public function process(Pap_Contexts_Action $context) {
        $context->debug('Started checking md5 checksums');
        $checksum = $context->getExtraDataFromRequest(Gpf_Settings::get(SaleFraudProtection_Config::PARAM_NAME));
    
        $myChecksum = $this->createCheckSum($context->getTotalCostFromRequest(), $context->getOrderIdFromRequest());
        if ($checksum == $myChecksum) {
            $context->debug('Checkings md5 checksums finished. Checksums equals.');
            return;
        }
        $context->debug('Checking md5 checksums failed. Transaction not saved. Checksums: '.$checksum.' - '.$myChecksum);
        $context->setDoCommissionsSave(false);
    }
    
    public function loadCoupon(Gpf_Rpc_Form $form) {
    	$form->addField('hidedatanumber', Gpf_Settings::get(SaleFraudProtection_Config::PARAM_NAME));
    }
    
    public function createOfflineSale(Pap_Tracking_ActionObject $sale) {
    	$sale->setData(Gpf_Settings::get(SaleFraudProtection_Config::PARAM_NAME), $this->createCheckSum($sale->getTotalCost(), $sale->getOrderId()));
    }
    
    /**
	 * @return String
     */
    private function createCheckSum($totalCost, $orderId) {
    	return md5($totalCost.','.$orderId.','.Gpf_Settings::get(SaleFraudProtection_Config::SECRET_KEY));
    }
}
?>
