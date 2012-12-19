<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 27482 2010-03-08 14:19:16Z mkendera $
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
class Pap_Common_CustomInvalidLicenseData extends Gpf_Object {
	
	/**
	 * @service
	 * @anonym
	 * @return Gpf_Rpc_Data
	 */
	public function load(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);
		$data->setValue('affManagerContact', Pap_Merchants_User::getMerchantEmail());
		return $data;
	}
}
?>
