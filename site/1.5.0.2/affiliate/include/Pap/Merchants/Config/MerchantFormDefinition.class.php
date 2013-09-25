<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: FormService.class.php 23061 2009-01-12 10:15:20Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Config_MerchantFormDefinition extends Pap_Merchants_Config_DynamicFormDefinition {
    
    public function __construct($accountId = null) {
        parent::__construct("merchantForm", $accountId);
    }
    
    protected function initFields() {
        $this->addField('data1', Gpf_Lang::_runtime('ICQ'), 'T', 'O', 0);
        $this->addField('data2', Gpf_Lang::_runtime('MSN Messenger'), 'T', 'O', 0);
        $this->addField('data3', Gpf_Lang::_runtime('Skype'), 'T', 'O', 0);
        $this->addField('data4', Gpf_Lang::_runtime('Yahoo Messenger'), 'T', 'O', 0);
        $this->addField('data5', Gpf_Lang::_runtime('Google talk'), 'T', 'O', 0);
        $this->addField('data6', Gpf_Lang::_runtime('Contact email'), 'T', 'O', 0);
        $this->addField('data7', Gpf_Lang::_runtime('Phone'), 'T', 'O', 0);
        $this->addField('data8', Gpf_Lang::_runtime('Fax'), 'T', 'O', 0);
        $this->addField('data9', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data10', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data11', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data12', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data13', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data14', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data15', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data16', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data17', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data18', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data19', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data20', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data21', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data22', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data23', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data24', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
        $this->addField('data25', Gpf_Lang::_runtime('Unused'), 'T', 'D', 0);
    }
}
?>
