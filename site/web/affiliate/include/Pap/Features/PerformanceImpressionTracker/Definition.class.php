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

class Pap_Features_PerformanceImpressionTracker_Definition extends Gpf_Plugins_Definition  {
	public function __construct() {
		$this->codeName = 'PerformanceImpressionTracker';
		$this->name = $this->_('Performance Impression Tracker');
		$this->description = $this->_('High performance impression tracker can handle more impressions than standard tracker. By turning this impression tracker on, impressions are cached and then processed by cron job. This feature is only for %s version 4.3 or bellow. In version 4.4 and above this feature is implemented directly into the core.', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO)) ;
		$this->version = '1.0.0';
		//this class is here only for compatibility reasons with PAP 4.3, in PAP 4.4 functionality of this plugin was implemented to the core
		$this->pluginType = self::PLUGIN_TYPE_SYSTEM;
	}
}
?>
