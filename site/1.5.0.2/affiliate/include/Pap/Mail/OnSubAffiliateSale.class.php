<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package ShopMachine
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Pap_Mail_OnSubAffiliateSale extends Pap_Mail_SaleMail {

    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'on_subaffiliate_sale.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Affiliate - New Subaffiliate Sale / Lead');
        $this->subject = Gpf_Lang::_runtime('New subaffiliate sale / lead');
    }

}
