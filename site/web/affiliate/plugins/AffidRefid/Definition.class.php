<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Martin Pullmann
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class AffidRefid_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'AffiliateIdRefid';
        $this->name = $this->_('Referral ID same as Affiliate ID');
        $this->description = $this->_('Affiliate ID will be used as Referral IDs so both numbers will be the same. %sWe do not recommend to use this plugin with ReferralID length constrain plugin, Username ReferralID plugin and Mandatory referral id for affiliate plugin.%s', '<font style="color:red">', '</font>');
        $this->version = '1.0.0';

        $this->addRequirement('PapCore', '4.1.14.0');

        $this->addImplementation('PostAffiliate.User.generatePrimaryKey', 'AffidRefid_Main', 'setUseridRefid', 0);
        $this->addImplementation('PostAffiliate.User.onUpdate', 'AffidRefid_Main', 'setUseridRefid');
    }
}
?>
