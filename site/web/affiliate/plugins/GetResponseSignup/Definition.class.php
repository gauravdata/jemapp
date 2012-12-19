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
class GetResponseSignup_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'GetResponseSignup';
        $this->name = $this->_('GetResponse signup');
        $this->description = $this->_('After signup of affiliate to Post Affiliate Pro, this plugin will register user also in GetResponse service. Plugins requires you to enter GetResponse API Key, which is offered only for customers paying for GetResponse services. GetResponse is an easy-to-use, web-based email marketing solution that can help you to build your permission-based mailing lists, maximize your conversion ratios, increase your profitability and build customer confidence. Visit GetResponse here  at %s', '<a href="http://www.GetResponse.com/index/qualityunit" target="_blank">http://www.GetResponse.com</a>');
        $this->version = '1.0.0';
        $this->help = '';
        $this->configurationClassName = 'GetResponseSignup_Config';

        $this->addRequirement('PapCore', '4.1.4.6');

        $this->addImplementation('Core.defineSettings', 'GetResponseSignup_Main', 'initSettings');
        $this->addImplementation('PostAffiliate.affiliate.userStatusChanged', 'GetResponseSignup_Main', 'userStatusChanged');
        $this->addImplementation('PostAffiliate.User.afterDelete', 'GetResponseSignup_Main', 'userDeleted');
        $this->addImplementation('PostAffiliate.affiliate.firsttimeApproved', 'GetResponseSignup_Main', 'signupToGetResponse');
        $this->addImplementation('PostAffiliate.User.afterSave', 'GetResponseSignup_Main', 'changeEmail');
    }
}
?>
