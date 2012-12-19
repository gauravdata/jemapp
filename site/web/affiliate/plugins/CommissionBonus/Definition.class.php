<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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

class CommissionBonus_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'CommissionBonus';
        $this->name = $this->_('Commission Bonus');
        $this->description = $this->_('With this plugin you can add bonus commission to affiliate if action is created with total cost greater than specified value. You can configure Bonus commission in Edit commission type dialog. It is not recommended to activate this plugin at the same time with Split Commission feature.');
        $this->version = '1.0.0';

        $this->addRequirement('PapCore', '4.2.10.4');

        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.initFields',
            'CommissionBonus_Main', 'initFields');
        
        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.save',
            'CommissionBonus_Main', 'save');
        
        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.load',
            'CommissionBonus_Main', 'load');
       
        $this->addImplementation('Tracker.saveCommissions.beforeSaveTransaction',
            'CommissionBonus_Main', 'saveCommission');
    }
}

?>
