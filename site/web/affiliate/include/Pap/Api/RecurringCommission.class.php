<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
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

class Pap_Api_RecurringCommission extends Pap_Api_Object {
	
    public function __construct(Gpf_Api_Session $session) {
        parent::__construct($session);
        $this->class = 'Pap_Features_RecurringCommissions_RecurringCommissionsForm';
    }
    
    public function setOrderId($value) { 
    	$this->setField('orderid', $value);    
    }
    
    public function getId() {
        return $this->getField('recurringcommissionid');
    }
    
    protected function getPrimaryKey() {
    	return "id";
    }

    protected function getGridRequest() {
		return new Pap_Api_RecurringCommissionsGrid($this->getSession());
    }  
    
    public function createCommissions() {
        $request = new Gpf_Rpc_ActionRequest('Pap_Features_RecurringCommissions_RecurringCommissionsForm',
                                             'createCommissions', $this->getSession());
        $request->addParam('id', $this->getId());
        $request->addParam('orderid', $this->getField('orderid'));
        $request->sendNow();
        $action = $request->getAction();
        if ($action->isError()) {
            throw new Gpf_Exception($action->getErrorMessage());
        }
    }
}
?>
