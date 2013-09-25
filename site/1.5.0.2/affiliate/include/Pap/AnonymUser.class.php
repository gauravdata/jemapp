<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: AuthUser.class.php 18081 2008-05-16 12:17:32Z mfric $
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
class Pap_AnonymUser extends Gpf_Auth_Anonym {

    function __construct() {
        $this->accountid = $this->resolveAccountId();
    }

    public function isLogged() {
        return false;
    }

    private function resolveAccountId() {
        return Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
    }

    public function getPapUserId() {
        throw new Gpf_Exception("No userId defined for Anonymous user");
    }
    
    public function isMerchant() {
        return false;
    }

    public function isAffiliate() {
        return false;
    }

    public function isMasterMerchant() {
        return false;
    }
    
    public function isDefaultAccount() {
        return $this->getAccountId() === Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
    }
    
    public function isNetworkMerchant() {
        return false;
    }
}
?>
