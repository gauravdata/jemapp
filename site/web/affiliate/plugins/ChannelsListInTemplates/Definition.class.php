<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class ChannelsListInTemplates_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'ChannelsListInTemplates';
        $this->name = $this->_('Channels list variable in affiliate panel templates');
        $this->description = $this->_('This plugin adds {$channels} variable to all affiliate panel templates. $channels is array of all affiliate channels where key is channel code and value is channel name');
        $this->version = '1.0.0';
        
        $this->addRequirement('PapCore', '4.2.11.6');
        
        $this->addImplementation('Pap_Affiliate.assignTemplateVariables', 'ChannelsListInTemplates_Main', 'assignTemplateVariables');
    }
}
?>
