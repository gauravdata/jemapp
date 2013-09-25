<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
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
class Pap_Features_PrivateCampaigns_MailContext extends Gpf_Object {

	private $user;
	private $campaign;
	private $userInCommissionGroup;
	
    /**
     * @return Pap_Common_User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param $user
     */
    public function setUser(Pap_Common_User $user) {
        $this->user = $user;
    }

    /**
     * @return Pap_Common_Campaign
     */
    public function getCampaign() {
        return $this->campaign;
    }

    /**
     * @param $campaign
     */
    public function setCampaign(Pap_Common_Campaign $campaign) {
        $this->campaign = $campaign;
    }
    
    /**
     * @return Pap_Db_UserInCommissionGroup
     */
    public function getUserInCommissionGroup() {
        return $this->userInCommissionGroup;
    }

    /**
     * @param Pap_Db_UserInCommissionGroup
     */
    public function setUserInCommissionGroup(Pap_Db_UserInCommissionGroup $userInCommissionGroup) {
        $this->userInCommissionGroup = $userInCommissionGroup;
    }
}
?>
