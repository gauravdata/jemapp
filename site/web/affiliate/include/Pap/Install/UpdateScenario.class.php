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

class Pap_Install_UpdateScenario extends Gpf_Install_Scenario {
    
    private $updateFiles;
    
    public function __construct() {
        parent::__construct();
        $this->name = $this->_('Application Update');
    }
    
    protected function initSteps() {
        $this->addStep(new Gpf_Install_SelectLanguage());
        $this->addStep(new Pap_Install_UpdateCheckRequirements());
        $updateFiles = new Gpf_Install_UpdateApplicationFiles();
        if ($updateFiles->isAvailable()) {
            $this->addStep($updateFiles);
            $this->addStep(new Gpf_Install_IntegrityCheck());
        }
        $this->addStep(new Gpf_Install_UpdateApplication());
        $this->addStep(new Gpf_Install_Finished());
    }
    
    public function silentDevelopmentInstall() {
        $update = new Gpf_Install_UpdateApplication();
        $update->updateVersion();
        $update->updateAccount();
    }
}
?>
