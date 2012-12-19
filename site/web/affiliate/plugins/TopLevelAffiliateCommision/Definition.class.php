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

class TopLevelAffiliateCommision_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'TopLevelAffiliateCommision';
        $this->name = $this->_('Top Level Affiliate Commision');
        $this->description = $this->_('Top level affiliate takes 100% commission from sub affiliates. It is not recommended to use this plugin with activated Top Level Affiliate Fixed Commision!');
        $this->version = '1.0.0';
        $this->configurationClassName = 'TopLevelAffiliateCommision_Config';

        $this->addRequirement('PapCore', '4.2.0.14');

        $this->addImplementation('Tracker.saveCommissions.beforeSaveTransaction', 'TopLevelAffiliateCommision_Main', 'beforeSave');
        $this->addImplementation('Tracker.saveAllCommissions', 'TopLevelAffiliateCommision_Main', 'modifyCommission');
        $this->addImplementation('Core.defineSettings', 'TopLevelAffiliateCommision_Main', 'initSettings');
    }
}

?>
