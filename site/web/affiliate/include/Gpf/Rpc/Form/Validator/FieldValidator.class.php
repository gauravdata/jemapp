<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FormHandler.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Rpc_Form_Validator_FieldValidator extends Gpf_Object {

    /**
     * @var array<Gpf_Rpc_Form_Validator_Validator>
     */
    private $validators;
    private $fieldLabel;
    private $errorMsg;


    public function __construct($fieldLabel) {
        $this->fieldLabel = $fieldLabel;
        $this->validators = array();
        $this->errorMsg = '';
    }

    /**
     * @param $validator
     */
    public function addValidator(Gpf_Rpc_Form_Validator_Validator $validator) {
        $this->validators[] = $validator;
    }
    
    public function getMessage() {
        return $this->errorMsg;
    }

    /**
     * @param $value
     * @return boolean
     */
    public function validate($value) {
        $this->errorMsg = '';
        foreach ($this->validators as $validator) {          
            if (!$validator->validate($value)) {
                $this->errorMsg .= ($this->errorMsg !== '' ? ', ' : '') . $this->translateFieldLabel($validator);
            }
        }
        if ($this->errorMsg !== '') {
            return false;
        }
        return true;
    }
    
    private function translateFieldLabel(Gpf_Rpc_Form_Validator_Validator $validator) {
        return Gpf_Lang::_replaceArgs($validator->getText(), $this->fieldLabel);
    }
}
?>
