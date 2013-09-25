<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
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

class Pap_Api_RecurringCommissionsGrid extends Gpf_Rpc_GridRequest {
    
    private $dataValues = null;
    
    public function __construct(Gpf_Api_Session $session) {
        if($session->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            throw new Exception("This class can be used only by merchant!");
        } else {
            parent::__construct("Pap_Features_RecurringCommissions_RecurringCommissionsGrid", "getRows", $session);
        }
    }
}
?>
