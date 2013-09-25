<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class AutoResponsePlus_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'AutoResponsePlus';
        $this->name = $this->_('Auto response plus');
        $this->description = $this->_('Register your new affiliates automatically to AutoResponsePlus<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>',Gpf_Application::getKnowledgeHelpUrl('features-and-functions/auto-response-plus-plugin-configuration.html'));
        $this->configurationClassName = 'AutoResponsePlus_Config';
        $this->version = '1.0.1';
        $this->addRequirement('PapCore', '4.1.30.0');

        $this->addImplementation('Core.defineSettings', 'AutoResponsePlus_Main', 'initSettings');
        $this->addImplementation('PostAffiliate.signup.after', 'AutoResponsePlus_Main', 'sendMail');
    }
}
?>
