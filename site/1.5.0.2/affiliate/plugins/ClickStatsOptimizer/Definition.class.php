<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class ClickStatsOptimizer_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'ClickStatsOptimizer';
        $this->name = $this->_('Click Stats Optimizer');
        $this->description = $this->_('This plugin forbids saving click data1/2 values to click stats which makes the click tables smaller but you won\'t be able to filter clicks by data1/2. Data1/2 will be available in Raw clicks only.');
        $this->version = '1.0.0';
        
        $this->addRequirement('PapCore', '4.2.0.8');
        
        $this->addImplementation('Tracker.click.fillClickParams', 'ClickStatsOptimizer_Main', 'clearDataFields');
    }
}
?>
