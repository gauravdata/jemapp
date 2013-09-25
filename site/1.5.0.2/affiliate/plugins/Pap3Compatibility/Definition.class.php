<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap3Compatibility_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'Pap3Compatibility';
        $this->name = $this->_('PAP3 Compatibility plugin');
        $this->description = $this->_('Handles tracking scripts compatibility with PAP3 and data migration from PAP3 to PAP4.');
        $this->version = '1.0.0';
        $this->help = '';

        $this->addRequirement('PapCore', '4.0.4.6');
    }
}
?>
