<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
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

class CustomOptionsInTrendsReport_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'CustomOptionsInTrendsReport';
        $this->name = $this->_('Custom options in Trends Report');
        $this->description = $this->_('This plugin changes values visible in Trends Report. Restart is needed after activation of this plugin.');
        $this->version = '1.0.0';

        $this->addRequirement('PapCore', '4.4.11.0');
        $this->addRequirement('ActionCommission', '1.0.0');

        $this->addImplementation('PostAffiliate.StatisticsBase.initDataTypes', 'CustomOptionsInTrendsReport_Main', 'initDataTypes', 8);
        $this->addImplementation('PostAffiliate.StatisticsBase.getDefaultDataType', 'CustomOptionsInTrendsReport_Main', 'getDefaultDataType', 8);
    }
}

?>
