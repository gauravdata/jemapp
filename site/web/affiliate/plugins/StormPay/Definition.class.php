<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class StormPay_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'StormPay';
        $this->name = $this->_('StormPay callback handling');
        $this->description = $this->_('This plugin handles StormPay notifications (integration of Post Affiliate with StormPay)');
        $this->version = '1.0.0';
        
        $this->addRequirement('PapCore', '4.0.4.6');
    }
}
?>
