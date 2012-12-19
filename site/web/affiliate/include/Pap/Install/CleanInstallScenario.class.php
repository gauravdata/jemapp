<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Merchant.class.php 18071 2008-05-16 08:02:18Z aharsani $
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

class Pap_Install_CleanInstallScenario extends Gpf_Install_Scenario {

    public function __construct() {
        parent::__construct();
        $this->name = $this->_('Clean Install');
    }

    protected function initSteps() {
        $this->addStep(new Gpf_Install_SelectLanguage());
        $this->addStep(new Pap_Install_WelcomeCheckRequirements());
        $this->addStep(new Gpf_Install_AcceptLicense());
        $this->addStep(new Gpf_Install_CreateDatabase());
        $this->addStep(new Gpf_Install_CreateAccount());
        $this->addStep(new Gpf_Install_Finished());
    }

    public function silentDevelopmentInstall() {
        $db = new Gpf_Install_CreateDatabase();
        $db->create();

        $account = new Gpf_Install_CreateAccount();
        $account->createTestAccount(Pap_Branding::DEMO_MERCHANT_USERNAME, Pap_Branding::DEMO_PASSWORD, 'John', 'Merchant');
    }
}
?>
