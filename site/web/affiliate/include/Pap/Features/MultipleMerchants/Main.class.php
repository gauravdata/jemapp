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

/**
 * @package PostAffiliatePro
 */
class Pap_Features_MultipleMerchants_Main extends Gpf_Plugins_Handler {

	/**
	 * @return Pap_Features_MultipleMerchants_Main
	 */
	public static function getHandlerInstance() {
		return new Pap_Features_MultipleMerchants_Main();
	}

	public function addToMenu(Pap_Merchants_Menu $menu) {
		$menu->getItem('Tools')->addItem('Merchants', $this->_('Merchants'));
		$menu->getItem('Tools')->addItem('Merchant-Role-Privileges', $this->_('Roles'));

		return Gpf_Plugins_Engine::PROCESS_CONTINUE;
	}
}
?>
