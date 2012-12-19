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

class Pap_Installer extends Gpf_Install_Module {
    
    public function __construct() {
        parent::__construct('com.qualityunit.pap.Installer', 'install', 'I');
    }

    /**
     *
     * @return Pap_Install_Manager
     */
    protected function createInstallManager() {
        return new Pap_Install_Manager();
    }
    
    protected function initStyleSheets() {
        parent::initStyleSheets();
        $this->addStyleSheets(Pap_Module::getStyleSheets());
    }
        
    protected function assignTemplateVariables($template) {
        parent::assignTemplateVariables($template);
        Pap_Module::assignTemplateVariables($template);
    }

    
    protected function getCachedTemplateNames() {
        return array_merge(parent::getCachedTemplateNames(),
                           array('installer_main', 'installer_step', 'tooltip_popup','form_field','installer_select_language'));
    }
}
?>
