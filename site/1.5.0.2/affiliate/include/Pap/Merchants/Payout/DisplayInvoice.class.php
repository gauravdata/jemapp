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
class Pap_Merchants_Payout_DisplayInvoice extends Gpf_Object implements Gpf_Rpc_Serializable {
    private $invoiceHtml;
    
	/**
     *
     * @service affiliate_invoice read
     * @param $fields
     */
    public function viewInvoice(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	$payoutId = $form->getFieldValue("Id");
    	
    	$payout = new Pap_Db_Payout();
    	$payout->setPrimaryKeyValue($payoutId);
    	
    	try {
    		$payout->load();
    		$this->invoiceHtml = $payout->getInvoice();
    		return $this;
    	} catch(Gpf_Exception $e) {
    		echo $e->getMessage();
    	}
        throw new Gpf_Exception($this->_('Cannot find invoice'));
    }
    
    /**
     *
     * @service affiliate_invoice read
     * @param $fields
     */
    public function previewInvoice(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $user = new Pap_Affiliates_User();
        
        $applyVat = $form->getFieldValue('applyVat');
        $payoutInvoice = $form->getFieldValue('payoutInvoice');
        
        
        $user->setId($form->getFieldValue('userid'));
        try {
            $user->load();
        } catch (Gpf_Exception $e) {
            $this->invoiceHtml = $this->_("You have to select user");
            return $this;
        }
        
        $currency = Pap_Common_Utils_CurrencyUtils::getDefaultCurrency();
        $payout = new Pap_Common_Payout($user, $currency, 1, 123456);
        $payout->setApplyVat($applyVat);
        $payout->generateInvoice($payoutInvoice);
        $this->invoiceHtml = $payout->getInvoice(); 
        return $this;
    }

    public function toObject() {
        throw new Gpf_Exception('Unsupported');
    }

    public function toText() {
        return $this->invoiceHtml;
    }
}

?>
