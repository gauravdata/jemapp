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

/**
 * @package PostAffiliatePro
 */
class SubaffiliateFirstSaleBonus_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'SubaffiliateFirstSaleBonus';
        $this->name = $this->_('Subaffiliate first sale extra bonus');
        $this->description = $this->_('This plugin will create an extra bonus for affiliate, when his sub-affiliate make first sale.');
        $this->version = '1.0.0';
        $this->configurationClassName = 'SubaffiliateFirstSaleBonus_Config';
        
        $this->addRequirement('PapCore', '4.5.41.2');

        $this->addImplementation('Tracker.saveCommissions.beforeSaveTransaction', 'SubaffiliateFirstSaleBonus_Main', 'process');
        $this->addImplementation('Core.defineSettings', 'SubaffiliateFirstSaleBonus_Main', 'initSettings');
    }
}

?>
