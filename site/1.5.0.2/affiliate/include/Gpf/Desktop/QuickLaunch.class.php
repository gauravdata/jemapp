<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: WindowManager.class.php 18659 2008-06-19 15:29:56Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Desktop_QuickLaunch extends Gpf_Object {

	const SHOW_QUICK_LAUNCH = 'showQuickLaunch';
	const SHOW_QUICK_LAUNCH_DEFAULT_VALUE = 'Y';

	/**
	 * @service quicklaunch read
	 * @param Gpf_Rpc_Params $params
	 */
	public function load(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form();
		$form->addField(self::SHOW_QUICK_LAUNCH, $this->getShowQuickLaunch());
		return $form;
	}

	/**
	 * @service quicklaunch write
	 * @param Gpf_Rpc_Params $params
	 */
	public function save(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

		try {
			Gpf_Db_UserAttribute::saveAttribute(self::SHOW_QUICK_LAUNCH, $form->getFieldValue(self::SHOW_QUICK_LAUNCH));
		} catch (Gpf_Exception $e) {
			$form->setErrorMessage($this->_('Failed to save quick launch settings with error %s', $e->getMessage()));
			return $form;
		}

		$form->setInfoMessage($this->_('Quick launch saved.'));

		return $form;
	}

	/**
	 * @return String
	 */
	public function getShowQuickLaunch() {
		try {
			$attributes = $this->getAttributes();
			if (isset($attributes[self::SHOW_QUICK_LAUNCH])) {
				return $attributes[self::SHOW_QUICK_LAUNCH];
			}
		} catch (Gpf_Exception $e) {
		}
		return self::SHOW_QUICK_LAUNCH_DEFAULT_VALUE;
	}

	/**
	 * @return array
	 */
	private function getAttributes() {
		$attributes = Gpf_Db_UserAttribute::getSettingsForGroupOfUsers(array(self::SHOW_QUICK_LAUNCH),
		array($this->getAccountUserId()));

		if (isset($attributes[$this->getAccountUserId()])) {
			return $attributes[$this->getAccountUserId()];
		}
		throw new Gpf_Exception($this->_('Settings not exists, load default settings.'));
	}

	/**
	 * @return String
	 */
	private function getAccountUserId() {
		return Gpf_Session::getInstance()->getAuthUser()->getAccountUserId();
	}
}
?>
