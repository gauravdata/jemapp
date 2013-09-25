<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: FormService.class.php 23061 2009-01-12 10:15:20Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
abstract class Pap_Merchants_Config_DynamicFormDefinition extends Gpf_Object {
    
    private $formId;
    private $accountId;
    
    public function __construct($formId, $accountId = null) {
        $this->formId = $formId;
        if ($accountId != null) {
            $this->accountId = $accountId;
        } else {
            $this->accountId = Gpf_Session::getAuthUser()->getAccountId();
        }
    }
    
    abstract protected function initFields();
    
    public function check() {
        $this->initFields();
    }
    
    protected function addField($code, $name, $type, $status, $order) {
        $formField = new Gpf_Db_FormField();
        
        $formField->setFormId($this->formId);
        $formField->setCode($code);
        try {
            $formField->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $formField->setName($name);
            $formField->setType($type);
            $formField->setStatus($status);
            $formField->setAccountId($this->accountId);
            $formField->insert();
        }
    }
}

?>
