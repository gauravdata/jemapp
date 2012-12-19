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
abstract class Pap_Merchants_Config_AutoDeleteRawClicksValidator extends Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator {

    /**
     * @var Gpf_Rpc_Form
     */
    protected $form;

    public function __construct(Gpf_Rpc_Form $form) {
        $this->form = $form;
    }

    /**
     * @param $value
     * @return boolean
     */
    public function validate($value) {
        if ($this->getAutoDeleteRawClicks() === '0') {
            return true;
        }
        $compareValue = $this->getCompareValue();
        if (($compareValue = $this->checkZero($compareValue)) === false) {
            return true;
        }
        $compareValue = $this->computeCompareValue($compareValue);
        return parent::validate($value) && $this->isBiggerOrEqual($value, $compareValue);
    }

    /**
     * @return String
     */
    protected abstract function getAutoDeleteRawClicks();

    /**
     * @return String
     */
    protected abstract function getCompareValue();

    /**
     * @return Number
     */
    protected function computeCompareValue($compareValue) {
        return $compareValue;
    }

    private function checkZero($value) {
        if (!($value > 0)) {
            return false;
        }
        return $value;
    }

    private function isBiggerOrEqual($big, $small) {
        if ($big >= $small) {
            return true;
        }
        return false;
    }
}
?>
