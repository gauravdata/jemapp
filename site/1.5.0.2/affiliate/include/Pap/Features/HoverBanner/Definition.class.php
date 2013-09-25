<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_HoverBanner_Definition extends Gpf_Plugins_Definition  {
	public function __construct() {
		$this->codeName = 'HoverBanner';
		$this->name = $this->_('Hover Banner');
		$this->description = $this->_('Enables hover (DIV on page) banner');
		$this->version = '1.0.0';
		$this->pluginType = self::PLUGIN_TYPE_FEATURE;

		$this->addImplementation('PostAffiliate.BannerFactory.getBannerObjectFromType', 'Pap_Features_HoverBanner_Main', 'getBanner');
	}

	public function onDeactivate() {
		$delete = new Gpf_SqlBuilder_DeleteBuilder();
		$delete->from->add(Pap_Db_Table_Banners::getName());
		$delete->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_HoverBanner_Hover::TYPE_HOVER);
		$delete->execute();
	}
}
?>
