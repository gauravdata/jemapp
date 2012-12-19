<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Menu.class.php 21654 2008-10-16 12:23:30Z mbebjak $
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
abstract class Gpf_Menu extends Gpf_Object {

	/**
	 * @var Gpf_Data_MenuItem
	 */
	private $menu;

	public function __construct() {
		$this->menu = new Gpf_Data_MenuItem(null, null);
		$this->createMenu();
	}

	/**
	 * This function should be implemented in each menu definition.
	 * Menu items should be added with function addItem
	 */
	abstract protected function createMenu();

	/**
	 * Read with service method all menu items
	 *
	 * @service menu read
	 */
	public function get(Gpf_Rpc_Params $params) {
		return $this->getNoRpc();
	}

	/**
	 * Add top level menu item
	 *
	 * @param string $contentCode String
	 * @param string $title
	 * @return Gpf_Data_MenuItem
	 */
	public function addItem($contentCode, $title) {
		return $this->menu->addItem($contentCode, $title);
	}

	/**
	 * @param string $code
	 * @return Gpf_Data_MenuItem
	 */
	public function getItem($code) {
		return $this->menu->getItem($code);
	}

	/**
	 * @return Gpf_Data_MenuItem
	 */
	public function getNoRpc() {
		return $this->menu;
	}

	/**
	 * Add privileges for overview screens. It is used only in client side code.
	 *
	 * @service import read
	 * @service export read
	 */
}

?>
