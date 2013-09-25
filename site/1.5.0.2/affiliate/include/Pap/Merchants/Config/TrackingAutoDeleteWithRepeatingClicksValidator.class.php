<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TrackingForm.class.php 29089 2010-08-16 10:58:38Z iivanco $
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
 
class Pap_Merchants_Config_TrackingAutoDeleteWithRepeatingClicksValidator extends Pap_Merchants_Config_AutoDeleteRawClicksValidator {

    protected function getAutoDeleteRawClicks() {
        return $this->form->getFieldValue(Pap_Settings::AUTO_DELETE_RAWCLICKS);
    }

    protected function getCompareValue() {
        return Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME);
    }

    protected function computeCompareValue($compareValue) {
        return $compareValue / (60 * 60 * 24);
    }

    public function getText() {
        return $this->_('Must be bigger then "Recognize multiple repeating clicks" defined in Fraud protection');
    }
}
?>
