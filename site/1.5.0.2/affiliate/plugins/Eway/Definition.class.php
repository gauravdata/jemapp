<?php

/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Eway_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'Eway';
        $this->name = $this->_('eWAY handling');
        $this->description = $this->_('This plugin handles eWAY notifications (integration of Post Affiliate with eWAY)');
        $this->version = '1.0.1';
        $this->configurationClassName = 'Eway_Config';
        
        $this->addRequirement('PapCore', '4.2.10.4');
        
        $this->addImplementation('Core.defineSettings', 'Eway_Main', 'initSettings');
    }
}
?>
