<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
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
 * @package PostAffiliatePro
 */
class Pap_Tracking_TrackerBase extends Gpf_Object {
	const LOGGER_TRACKING = 'tracking';
	   
	/**
     * @var Gpf_Log_Logger
     */
	protected $logger;
	/**
     * @var Pap_Tracking_Request
     */
	protected $request;
	/**
	 * @var Pap_Tracking_Response
	 */
	protected $response;
	/**
	 * @var Pap_Tracking_Cookie
	 */
	protected $cookie;
	/**
	 * @var Pap_Common_User
	 */
	protected $user = null;
	/**
	 * @var Pap_Common_Campaign
	 */
	protected $campaign = null;
	/**
	 * @var string
	 */
	protected $data1;
	/**
	 * @var string
	 */
	protected $data2;
	/**
	 * @var string
	 */
	protected $data3;
	/**
	 * @var string
	 */
	protected $data4;
		/**
	 * @var string
	 */
	protected $data5;
	/**
	 * @var string
	 */
	protected $countryCode = '';
	/**
	 * @var string
	 */
	protected $ip;
	/**
	 * @var string
	 */
	protected $browser;
	/**
	 * @var string
	 */
	protected $referrer;
    
    public function track(){
		throw new Pap_Tracking_Exception("You cannot call track() from the base class");
    }
    
    protected function debug($msg) {
    	if($this->logger != null) {
    		$this->logger->debug($msg);
    	}
    }

    public function info($msg) {
    	if($this->logger != null) {
    		$this->logger->info(msg);
    	}
    }
    
    public function error($msg) {
    	if($this->logger != null) {
    		$this->logger->error(msg);
    	}
    }
    
    public function getScriptUrl($scriptName) {
        return Gpf_Paths::getInstance()->getFullScriptsUrl() . $scriptName;
    }
    
    private function checkActionTypeInDebugTypes($actionType) {
        $debugTypes = Gpf_Settings::get(Pap_Settings::DEBUG_TYPES);
    	if($debugTypes == '') {
    		return false;
    	}
    	
    	$arr = explode(",", $debugTypes);
    	if(in_array($actionType, $arr)) {
    		return true;
    	}
    	return false;
    }    
}

?>
