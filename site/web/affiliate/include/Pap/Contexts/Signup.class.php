<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
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
 * @package PostAffiliate
 */
class Pap_Contexts_Signup extends Gpf_Plugins_Context {

    /**
     * @var Gpf_Log_Logger
     */
    private $_logger = null;

    /**
     * @var instance
     */
    static protected $instance = null;

    /**
     * constructs context instance
     * It creates debug logger if there are parameters for it
     *
     */
    protected function __construct() {
    	Gpf_Session::getAuthUser()->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);

//    	$this->initDebugLogger();

        $cookieObj = new Pap_Tracking_Cookie();
    	$cookieObj->setLogger($this->getLogger());
    }

    /**
     * override this function and return the correct
     * Pap_Common_Constants::TYPE_XXXX type of your transaction to enable logging
     */
    protected function getActionTypeConstant() {
    	return Pap_Common_Constants::TYPE_SIGNUP;
    }

    protected function initDebugLogger() {
    	$logger = Pap_Logger::create($this->getActionTypeConstant());
    	if($logger != null) {
    		$this->setLogger($logger);
    	}
    	if($logger == null || !$logger->checkLoggerTypeExists(Gpf_Log_LoggerDisplay::TYPE)) {
    		Pap_Tracking_ClickTracker::sendJavaScriptHeaders();
    	}
    }

    /**
     * @return Pap_Contexts_Signup
     */
	public function getContextInstance() {
        if (self::$instance == null) {
            self::$instance = new Pap_Contexts_Signup();
        }
        return self::$instance;
 	}

	/**
	 * gets affiliate form object (instance of Gpf_Rpc_Form)
	 * @return Gpf_Rpc_Form
	 */
    public function getFormObject() {
		return $this->get("formObject");
	}

	/**
	 * sets affiliate form object (instance of Gpf_Rpc_Form)
	 */
	public function setFormObject(Gpf_Rpc_Form $value) {
		$this->set("formObject", $value);
	}

	/**
	 * gets user object (instance of Pap_Common_User)
	 * @return Pap_Common_User
	 */
    public function getUserObject() {
		return $this->get("userObject");
	}

	/**
	 * sets user object (instance of Pap_Common_User)
	 */
	public function setUserObject(Pap_Common_User $value) {
		$this->set("userObject", $value);
	}

	/**
	 * gets form parameters object (instance of Gpf_Rpc_Params)
	 * @return Gpf_Rpc_Params
	 */
	public function getParametersObject() {
		return $this->get("paramObject");
	}

	/**
	 * sets form parameters object (instance of Gpf_Rpc_Params)
	 */
	public function setParametersObject(Gpf_Rpc_Params $value) {
		$this->set("paramObject", $value);
	}
}
?>
