<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package GwtPhpFramework
*   @author Maros Fric
*   @since Version 1.0.0
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
class Gpf_Api_Session extends Gpf_Object {
    const MERCHANT = 'M';
    const AFFILIATE = 'A';

	private $url;
	private $sessionId = '';
	private $debug = false;
	private $message = '';
	private $roleType = '';

	public function __construct($url) {
		$this->url = $url;
	}
	/**
	 *
	 * @param $username
	 * @param $password
	 * @param $roleType Gpf_Api_Session::MERCHANT or Gpf_Api_Session::AFFILIATE
	 * @param $languageCode language code (e.g. en-US, de-DE, sk, cz, du, ...)
	 * @return boolean true if user was sucesfully logged
	 */
	public function login($username, $password, $roleType = self::MERCHANT, $languageCode = null) {
		$request = new Gpf_Rpc_FormRequest("Gpf_Api_AuthService", "authenticate");
		$request->setUrl($this->url);
		$request->setField("username", $username);
		$request->setField("password", $password);
		$request->setField("roleType", $roleType);
		$request->setField('isFromApi', Gpf::YES);
		$request->setField('apiVersion', self::getAPIVersion());
		if($languageCode != null) {
		    $request->setField("language", $languageCode);
		}

		$this->roleType = $roleType;

		try {
			$request->sendNow();
		} catch(Exception $e) {
			$this->setMessage("Connection error: ".$e->getMessage());
			return false;
		}

		$form = $request->getForm();
		$this->checkApiVersion($form);

		$this->message = $form->getInfoMessage();

		if($form->isSuccessful() && $form->existsField("S")) {
			$this->sessionId = $form->getFieldValue("S");
			$this->setMessage($form->getInfoMessage());
			return true;
		}

		$this->setMessage($form->getErrorMessage());
		return false;
	}

    /**
     * Get version of installed application
     *
     * @return string version of installed application
     */
    public function getAppVersion() {
        $request = new Gpf_Rpc_FormRequest("Gpf_Api_AuthService", "getAppVersion");
        $request->setUrl($this->url);

        try {
            $request->sendNow();
        } catch(Exception $e) {
            $this->setMessage("Connection error: ".$e->getMessage());
            return false;
        }

        $form = $request->getForm();
        return $form->getFieldValue('version');
    }


	public function getMessage() {
		return $this->message;
	}

	private function setMessage($msg) {
		$this->message = $msg;
	}

	public function getDebug() {
		return $this->debug;
	}

	public function setDebug($debug = true) {
		$this->debug = $debug;
	}

	public function getSessionId() {
		return $this->sessionId;
	}

	public function getRoleType() {
		return $this->roleType;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getUrlWithSessionInfo($url) {
	    if (strpos($url, '?') === false) {
	        return $url . '?S=' . $this->getSessionId();
	    }
	    return $url . '&S=' . $this->getSessionId();
	}

	/**
	 * Check API version
	 * (has to be protected because of Drupal integration)
	 *
	 * @param $latestVersion
	 * @throws Gpf_Api_IncompatibleVersionException
	 */
	protected function checkApiVersion(Gpf_Rpc_Form $form) {
		if ($form->getFieldValue('correspondsApi') === Gpf::NO) {
		    $exception = new Gpf_Api_IncompatibleVersionException($this->url);
		    trigger_error($exception->getMessage(), E_USER_NOTICE);
		}
	}

	/**
	 * @return String
	 */
	public static function getAPIVersion($fileName = __FILE__) {
		$fileHandler = fopen($fileName, 'r');
		fseek($fileHandler, -6 -32, SEEK_END);
		$hash = fgets($fileHandler);
		return substr($hash, 0, -1);
	}
}
?>
