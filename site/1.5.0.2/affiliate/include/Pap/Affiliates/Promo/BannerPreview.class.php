<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 22817 2008-12-15 09:20:35Z mjancovic $
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
class Pap_Affiliates_Promo_BannerPreview implements Gpf_Rpc_Serializable {

	private $preview;

	/**
	 * Get banner preview for iframe element
	 *
	 * @service banner read
	 * @param Gpf_Rpc_Params $params
	 * @return Pap_Affiliates_Promo_BannerPreview
	 */
	public function getFrameBannerPreview(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);
		$bannerFactory = new Pap_Common_Banner_Factory();
		$banner = $bannerFactory->getBanner($form->getFieldValue('bannerId'));
		$this->preview = $banner->getPreview($this->getUser());
		
		return $this;
	}

	/**
	 * Get banner preview for Window element
	 *
	 * @service banner read
	 * @param Gpf_Rpc_Params $params
	 * @return Pap_Affiliates_Promo_BannerPreview
	 */
	public function getWindowBannerPreview(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);
		$bannerFactory = new Pap_Common_Banner_Factory();
		$banner = $bannerFactory->getBanner($form->getFieldValue('bannerId'));
		
		$template = new Gpf_Templates_Template('html_window_preview.stpl');
		$template->assign('bannercode', $banner->getPreview($this->getUser()));
		$this->preview = $template->getHTML();
		
		return $this;
	}

	public function toObject() {
		throw new Gpf_Exception('Unsupported');
	}

	public function toText() {
		return $this->preview;
	}

	private function getUser() {
		$user = new Pap_Affiliates_User();
		$user->setId(Gpf_Session::getAuthUser()->getPapUserId());
		$user->load();

		return $user;
	}
}
