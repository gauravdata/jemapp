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
class Pap_Tracking_Common_TrackingBase extends Gpf_Object {

    private function debug($context, $message) {
        if ($context == null) {
            return;
        }
        $context->debug($message);
    }
    
	/**
	 * gets user by user id
	 * @param $userId
	 * @return Pap_Affiliates_User
	 */
	protected function getUserById($context, $id) {
		if($id == '') {
			return null;
		}
		
		try {
		    return Pap_Affiliates_User::loadFromId($id);
		} catch (Gpf_Exception $e) {
		    $this->debug($context, "User with RefId/UserId: $id doesn't exist");
		    return null;
		}
	}
        
    /**
     * retrieves default currency
     *
     * @return Gpf_Db_Currency
     */
    protected function getDefaultCurrency() {
    	try {
    		$obj = new Gpf_Db_Currency();
    		return $obj->getDefaultCurrency();
    	} catch (Gpf_DbEngine_NoRowException $e) {
    		throw new Pap_Tracking_Exception("    Critical error - No default currency is defined");
    	}
    } 

}

?>
