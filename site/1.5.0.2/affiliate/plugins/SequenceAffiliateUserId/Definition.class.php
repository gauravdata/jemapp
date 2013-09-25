<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class SequenceAffiliateUserId_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'SequenceAffiliateUserId';
        $this->name = $this->_('Generate Affiliate User ID as autoincremented sequence');
        $this->description = $this->_('Affiliate user ids will be genrated as numeric sequence from specified number. New affiliates will have userid set to previous_userid+1. %sWe do not recommend to use this plugin with Mandatory referral id for affiliate plugin.%s', '<font style="color:red">', '</font>');
        $this->version = '1.0.0';
        $this->configurationClassName = 'SequenceAffiliateUserId_Config';

        $this->addRequirement('PapCore', '4.4.11.0');
        $this->addImplementation('Core.defineSettings', 'SequenceAffiliateUserId_Main', 'initSettings');
        $this->addImplementation('PostAffiliate.User.generatePrimaryKey', 'SequenceAffiliateUserId_Main', 'generatePrimaryKey');
    }
}
?>
