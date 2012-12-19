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
class Pap_Affiliates_Reports_TrendsReport extends Pap_Common_Reports_StatisticsBase {
	
   protected function setAffiliateId(Pap_Stats_Params $statsParameters, $userId) {
        $statsParameters->setAffiliateId(Gpf_Session::getAuthUser()->getPapUserId());
    }
}

?>
