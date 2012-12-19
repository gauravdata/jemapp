<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic   (created by Rick Braddy / WinningWare.com for PostAffiliatePro)
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
class AWeber_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'AWeber';
        $this->name = $this->_('AWeber Autoresponder Integration');
        $this->description = $this->_('Register your new affiliates automatically to AWeber.  Press the Configure button and enter your AWeber autoresponder list name.  To use, simply enable the AWeber "Premium Web Cart" email parser, located in your AWeber List Settings.');
        $this->configurationClassName = 'AWeber_Config';
        $this->version = '1.0.0';
        $this->addRequirement('PapCore', '4.1.30.0');

        $this->addImplementation('Core.defineSettings', 'AWeber_Main', 'initSettings');
        $this->addImplementation('PostAffiliate.signup.after', 'Aweber_Main', 'sendMail');
    }
}
?>
