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
class Gpf_Rpc_Form_Validator_FormValidatorCollection extends Gpf_Object {
    
    /**
     * @var array<Gpf_Rpc_Form_Validator_FieldValidator>
     */
    private $validators;
    /**
     * @var Gpf_Rpc_Form
     */
    private $form;
    
    public function __construct(Gpf_Rpc_Form $form) {
        $this->form = $form;
        $this->validators = array();
    }
    
    /**
     * @param $fieldName
     * @param $validator
     */
    public function addValidator(Gpf_Rpc_Form_Validator_Validator $validator, $fieldName, $fieldLabel = null) {
        if (!array_key_exists($fieldName, $this->validators)) {
            $this->validators[$fieldName] = new Gpf_Rpc_Form_Validator_FieldValidator(($fieldLabel === null ? $fieldName : $fieldLabel));
        }
        $this->validators[$fieldName]->addValidator($validator);
    }
    
    /**
     * @return boolean
     */
    public function validate() {
        $errorMsg = false;
        foreach ($this->validators as $fieldName => $fieldValidator) {
            if (!$fieldValidator->validate($this->form->getFieldValue($fieldName))) {
                $errorMsg = true;
                $this->form->setFieldError($fieldName, $fieldValidator->getMessage());
            }
        }
        if ($errorMsg) {
            $this->form->setErrorMessage($this->_('There were errors, please check highlighted fields'));
        }
        return !$errorMsg;
    }
}
?>
