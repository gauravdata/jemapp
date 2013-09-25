<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak, Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: ApplicationSettings.class.php 19930 2008-08-18 12:33:09Z aharsani $
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
class Pap_LoginApplicationSettings extends Gpf_ApplicationSettings {

    protected function loadSetting() {
        parent::loadSetting();
        $this->addValue("DEMO_MODE", Gpf_Application::isDemo() ? "Y" : "N");
        $this->addValue(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO, Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO));
    }
}
?>
