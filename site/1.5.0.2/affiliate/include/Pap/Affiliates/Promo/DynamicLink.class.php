<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Affiliates_Promo_DynamicLink extends Gpf_Object {

	/**
	 * @var Pap_Common_Banner
	 */
	private $banner;

	/**
	 * @service direct_link read_own
	 *
	 * @param Gpf_Rpc_Params $params
	 * @return Gpf_Rpc_Form
	 */
	public function getCode(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

		$this->initBanner($form->getFieldValue('Id'));
		$this->setChannel($form->getFieldValue('channel'));
		$user = $this->getUser($form);
		if ($form->isError()) {
			return $form;
		}
		$form->setField('code', $this->banner->getDynamicLinkCode($user, $form->getFieldValue('desturl')));

		return $form;
	}
	
	/**
	 * @param String $bannerId	 
	 */
	private function initBanner($bannerId) {
		$bannerFactory = new Pap_Common_Banner_Factory();
		$this->banner = $bannerFactory->getBanner($bannerId);
	}

	/**	 
	 * @param Gpf_Rpc_Form $form
	 * @return Pap_Common_User
	 */
	private function getUser(Gpf_Rpc_Form $form) {
		$user = new Pap_Common_User();
		try {
			$user->setId(Gpf_Session::getAuthUser()->getPapUserId());
			$user->load();
		} catch (Gpf_Exception $e) {
			$form->setErrorMessage($e->getMessage());
			return null;
		}
		return $user;
	}

	/**
	 * @param String $channelValue
	 */
	private function setChannel($channelValue) {
		$channel = new Pap_Db_Channel();
		$channel->setValue($channelValue);
		$channel->setPapUserId(Gpf_Session::getAuthUser()->getPapUserId());
		try {
			$channel->loadFromData(array(Pap_Db_Table_Channels::VALUE, Pap_Db_Table_Channels::USER_ID));
			$this->banner->setChannel($channel);
		} catch (Gpf_Exception $e) {
		}
	}
}

?>
