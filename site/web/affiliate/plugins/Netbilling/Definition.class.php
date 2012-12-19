<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
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

class Netbilling_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'Netbilling';
        $this->name = $this->_('Netbilling integration plugin');
        $this->description = $this->_('This plugin handles Netbilling notifications (integration of Post Affiliate with Netbilling).');
        $this->version = '1.0.0';      
        $this->addRequirement('PapCore', '4.2.0.18');
    }
}
?>
