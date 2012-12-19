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

class Pap_Common_TransactionCompoundContext extends Gpf_Object {

    /*
     * @var Pap_Contexts_Tracking
     */
    private $context;

    /**
     * @var Pap_Common_Transaction
     */
    private $transaction;

    private $saveTransaction = true;
    
    public function __construct(Pap_Common_Transaction $transaction, Pap_Contexts_Tracking $context) {
        $this->transaction = $transaction;
        $this->context = $context;
    }

    /**
     * @return Pap_Contexts_Tracking
     */
    public function getContext(){
        return $this->context;
    }

    public function setSaveTransaction($value) {
    	$this->saveTransaction = $value; 
    }
    
    public function getSaveTransaction() {
    	return $this->saveTransaction;
    }
    
    /**
     * @return Pap_Common_Transaction
     */
    public function getTransaction() {
        return $this->transaction;
    }
}

?>
