<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Service.class.php 27074 2010-02-04 08:56:03Z mjancovic $
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
class Gpf_Api_AuthService extends Gpf_Auth_Service {

	private $remoteApiVersion;

    /**
     * @service
     * @anonym
     * @param $username
     * @param $password
     * @param $accountId
     * @param $rememberMe
     * @param $language
     * @return Gpf_Rpc_Serializable
     */
	public function authenticate(Gpf_Rpc_Params $params) {
		$loginForm = new Gpf_Rpc_Form($params);
		$this->remoteApiVersion = $loginForm->getFieldValue('apiVersion');
		return parent::authenticate($params);
	}

	/**
	 * Retunr application version installed on server
	 *
     * @service
     * @anonym
	 * @param Gpf_Rpc_Params $params
	 * @return Gpf_Rpc_Form
	 */
	public function getAppVersion(Gpf_Rpc_Params $params) {
	    $form = new Gpf_Rpc_Form($params);
	    $form->setField('version', Gpf_Application::getInstance()->getVersion());
	    return $form;
	}

	/**
	 * @return Gpf_Rpc_Form
	 */
	protected function createResponseForm() {
		$form = parent::createResponseForm();
		$form->setField('correspondsApi', Gpf::YES);
		if (!$this->corresponsApiVersion()) {
			$form->setField('correspondsApi', Gpf::NO);
		}
		return $form;
	}

	private function corresponsApiVersion() {
		return $this->remoteApiVersion === Gpf_Api_Session::getAPIVersion(Gpf_Api_DownloadAPI::API_BASE_PATH . Gpf_Application::getInstance()->getApiFileName());
	}
}
?>
