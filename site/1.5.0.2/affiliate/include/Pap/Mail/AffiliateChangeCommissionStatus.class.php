<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Pap_Mail_AffiliateChangeCommissionStatus extends Pap_Mail_SaleMail {

    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'affiliate_on_change_commission_status.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Affiliate - On change  Sale / Lead status');
        $this->subject = Gpf_Lang::_runtime('Change sale / lead status');
    }
}
