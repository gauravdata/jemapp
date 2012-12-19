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
class Pap_Affiliates_Reports_DisplayInvoice extends Gpf_Object implements Gpf_Rpc_Serializable {
    private $invoiceHtml;
	
    /**
     *
     * @service affiliate_invoice read_own
     * @param $fields
     */
    public function viewInvoice(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	$payoutId = $form->getFieldValue("Id");
    	
    	$payout = new Pap_Db_Payout();
    	$payout->setPrimaryKeyValue($payoutId);
    	$payout->setUserId(Gpf_Session::getAuthUser()->getPapUserId());
    	
    	try {
    		$payout->loadFromData(array(Pap_Db_Table_Payouts::ID, Pap_Db_Table_Payouts::USER_ID));
    		$this->invoiceHtml = $payout->getInvoice();
    		return $this;
    	} catch(Gpf_Exception $e) {
    	}
        throw new Gpf_Exception($this->_('Cannot find invoice'));
    }
    
    public function toObject() {
        throw new Gpf_Exception('Unsupported');
    }

    public function toText() {
        return $this->invoiceHtml;
    }
}

?>
