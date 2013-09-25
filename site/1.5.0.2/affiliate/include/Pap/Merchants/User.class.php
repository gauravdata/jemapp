<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Andrej Harsani
*   @since Version 1.0.0
*   $Id: User.class.php 17743 2008-05-06 08:25:49Z mfric $
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
class Pap_Merchants_User extends Pap_Common_User  {
    
    public function __construct() {
        parent::__construct();
        $this->setType(Pap_Application::ROLETYPE_MERCHANT);
        $this->setRoleId(Pap_Application::DEFAULT_ROLE_MERCHANT);
    }
    
    protected function setupNewUser() {
        $this->setupAttributes();
        $this->addDefaultGadgets();
    }
    
    protected function initRefid($refid) {
        $this->setRefId($refid);
    }
    
    protected function setDefaultLanguage() {
        Gpf_Db_Table_UserAttributes::setSetting(Gpf_Auth_User::LANGUAGE_ATTRIBUTE_NAME,
            Pap_Branding::DEFAULT_LANGUAGE_CODE, $this->getAccountUserId());
    }
    
    private function setupAttributes() {
        $this->setDefaultLanguage();
        $this->setDefaultTheme(Gpf_Settings::get(Pap_Settings::DEFAULT_MERCHANT_PANEL_THEME));
        $this->setQuickLaunchSetting('showDesktop');
    }
    
    private function addDefaultGadgets() {
        $this->addGadget('C', Gpf_Lang::_runtime('Quick start actions'), 'content://QuickStartActionsGadget',
            Gpf_Db_Table_Gadgets::POSITION_TYPE_SIDEBAR, 63, 1000, 333, 59);

        $this->addGadget('C', Gpf_Lang::_runtime('Pending tasks'), 'content://PendingTasksGadget',
            Gpf_Db_Table_Gadgets::POSITION_TYPE_SIDEBAR, 122, 1000, 333, 170);
            
        $this->addGadget('C', Gpf_Lang::_runtime('Traffic stats'), 'content://trafficStatsGadget',
            Gpf_Db_Table_Gadgets::POSITION_TYPE_SIDEBAR, 292, 1000, 333, 160);

        $this->addGadget('C', Gpf_Lang::_runtime('Search'), 'content://SearchGadget',
            Gpf_Db_Table_Gadgets::POSITION_TYPE_SIDEBAR, 452, 1000, 100, 88);
                
        $this->addGadget('C', Gpf_Lang::_runtime('Online users'), 'content://OnlineUsersGadget',
            Gpf_Db_Table_Gadgets::POSITION_TYPE_SIDEBAR, 540, 1000, 333, 124);
    }
}

?>
