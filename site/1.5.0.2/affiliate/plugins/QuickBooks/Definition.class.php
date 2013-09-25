<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class QuickBooks_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'QuickBooks';
        $this->name = $this->_('QuickBooks export to IIF format');
        $this->description = $this->_('Plugin will add option to export your payout history data to QuickBooks IIF file format. Note: this plugin needs restart to work properly');
        $this->version = '1.0.0';
        $this->configurationClassName = 'QuickBooks_Config';

        $this->addRequirement('PapCore', '4.2.3.2');
        
        $this->addImplementation('Core.defineSettings', 'QuickBooks_Main', 'initSettings');
    }
}
?>
