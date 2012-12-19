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
class Pap3Compatibility_PreSale extends Gpf_Object {

    public function setCookiesToBeDeleted() {
    	$deleteCookies = Gpf_Settings::get(Pap_Settings::DELETE_COOKIE);
    	if($deleteCookies == Gpf::YES) {
             echo "setCookieToBeDeleted(".$_REQUEST['salerIndex'].", '".Pap_Tracking_Cookie::SALE_COOKIE_NAME."');\n";      		
        }
    }

 	public function finishSale() {
 		echo "finishSale(".$_REQUEST['salerIndex'].");";
 	} 	
}
?>
