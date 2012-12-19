<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Galik
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
class Pap_Tracking_ActionObject extends Pap_Tracking_Action_RequestActionObject {	
	public function __construct($actionCode = '') {
	    $this->setActionCode($actionCode);
	}

	/**
	 * Backward compatibility with API
	 */
	public function setCoupon($couponCode) {
	    $this->setCouponCode($couponCode);
	}

	/**
	 * Backward compatibility with API
	 */	
    public function getCoupon() {
        return $this->getCouponCode();
    }
}
?>
