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

/**
 * @package PostAffiliatePro
 */
class SeoTargetUrlVariable_Definition extends Gpf_Plugins_Definition {
	
	const NAME_MINIMUM_TOTALCOST = 'minimumTotalcost';

    public function __construct() {
        $this->codeName =  'SeoTargetUrlVariable';
        $this->name = $this->_('seo_targeturl for banner format');
        $this->description = $this->_('This plugin enables you to use variable {$seo_targeturl} in banner format. It is computed not as usual SEO url but uses domain of the banner. You need to have SEO tracking code on all banner destination urls!');
        $this->version = '1.0.0';               
        $this->addRequirement('PapCore', '4.1.25.0');
        
        $this->addImplementation('PostAffiliate.Banner.replaceUrlConstants', 'SeoTargetUrlVariable_Main', 'replaceVariables');
    }
}

?>
