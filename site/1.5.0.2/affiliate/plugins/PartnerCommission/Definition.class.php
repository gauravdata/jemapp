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

class PartnerCommission_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'PartnerCommission';
        $this->name = $this->_('Partner Commission');
        $this->description = $this->_('Affiliate that has no parent (partner) will receive commission that is computed as totalcost - sum of commissions given to other affiliates in this sale');
        $this->version = '1.0.0';

        $this->addRequirement('PapCore', '4.2.0.14');

        $this->addImplementation('Tracker.saveAllCommissions', 'PartnerCommission_Main', 'modifyCommission');
    }
}

?>
