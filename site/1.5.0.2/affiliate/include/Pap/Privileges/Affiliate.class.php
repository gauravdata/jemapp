<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: Paths.class.php 20126 2008-08-25 13:20:39Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * This class can be generated using parsePrivileges.php script in applications scripts folder
 *
 * @package PostAffiliatePro
 */
class Pap_Privileges_Affiliate extends Pap_Privileges {
  protected function initDefaultPrivileges() {
        // Framework privileges
        $this->addPrivilege(Gpf_Privileges::AUTHENTICATION, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::CURRENCY, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::DB_FILE, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::EXPORT_FILE, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::FILTER, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::FORM_FIELD, Gpf_Privileges::P_READ);
        $this->addPrivilege(Gpf_Privileges::GADGET, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::GRID_VIEW, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::QUICKLAUNCH, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::ROLE, Gpf_Privileges::P_READ);
        $this->addPrivilege(Gpf_Privileges::SIDEBAR, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::TEMPLATE, Gpf_Privileges::P_READ);
        $this->addPrivilege(Gpf_Privileges::THEME, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::UPLOADED_FILE, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::WALLPAPER, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::WINDOW, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Gpf_Privileges::MYPROFILE, Gpf_Privileges::P_ALL);

        // Application privileges
        $this->addPrivilege(Pap_Privileges::AFFILIATE, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::AFFILIATE, Pap_Privileges::P_WRITE);
        $this->addPrivilege(Pap_Privileges::AFFILIATE_EMAIL_NOTIFICATION, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::AFFILIATE_EMAIL_NOTIFICATION, Pap_Privileges::P_WRITE);
        $this->addPrivilege(Pap_Privileges::MASS_EMAIL, Pap_Privileges::P_WRITE);
        $this->addPrivilege(Pap_Privileges::MAIL_TEMPLATE, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::AFFILIATE_INVOICE, Pap_Privileges::P_READ_OWN);
        $this->addPrivilege(Pap_Privileges::AFFILIATE_LOGIN_FORM, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::AFFILIATE_SIGNUP_FORM, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::AFFILIATE_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::AFFILIATE_TREE, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::BANNER, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::BANNER, Pap_Privileges::P_EXPORT);
        $this->addPrivilege(Pap_Privileges::BANNER_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::CAMPAIGN, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::CAMPAIGN, Pap_Privileges::P_EXPORT);
        $this->addPrivilege(Pap_Privileges::CHANNEL, Gpf_Privileges::P_ALL);
        $this->addPrivilege(Pap_Privileges::CLICK, Pap_Privileges::P_READ_OWN);
        $this->addPrivilege(Pap_Privileges::CLICK, Pap_Privileges::P_EXPORT_OWN);
        $this->addPrivilege(Pap_Privileges::COMMISSION, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::CONTACT_US, Pap_Privileges::P_WRITE);
        $this->addPrivilege(Pap_Privileges::DIRECT_LINK, Pap_Privileges::P_READ_OWN);
        $this->addPrivilege(Pap_Privileges::DIRECT_LINK, Pap_Privileges::P_ADD_OWN);
        $this->addPrivilege(Pap_Privileges::DIRECT_LINK, Pap_Privileges::P_DELETE);
        $this->addPrivilege(Pap_Privileges::FINANCIAL_OVERVIEW, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::GENERAL_LINK, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::LINK_CLOAKER, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::MENU, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::MERCHANT, Pap_Privileges::P_READ_OWN);
        $this->addPrivilege(Pap_Privileges::PAYOUT, Pap_Privileges::P_READ_OWN);
        $this->addPrivilege(Pap_Privileges::PAYOUT, Pap_Privileges::P_EXPORT_OWN);
        $this->addPrivilege(Pap_Privileges::PAYOUT_HISTORY, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::PAYOUT_HISTORY, Pap_Privileges::P_EXPORT);
        $this->addPrivilege(Pap_Privileges::PERIOD_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::QUICK_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::SUB_AFF_SALE, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::SUB_AFF_SALE, Pap_Privileges::P_EXPORT);
        $this->addPrivilege(Pap_Privileges::SUB_AFF_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::DAILY_REPORT, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::TRAFFIC_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::TRANSACTION, Pap_Privileges::P_READ_OWN);
        $this->addPrivilege(Pap_Privileges::TRANSACTION, Pap_Privileges::P_EXPORT_OWN);
        $this->addPrivilege(Pap_Privileges::TRANSACTION_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::TREND_STATS, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::USER_COMM_GROUP, Pap_Privileges::P_ADD);
        $this->addPrivilege(Pap_Privileges::COUPON, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::COUPON, Pap_Privileges::P_WRITE);
        $this->addPrivilege(Pap_Privileges::COUPON, Pap_Privileges::P_EXPORT);
        $this->addPrivilege(Pap_Privileges::REPORTS_OVERVIEW, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::SUBID_TRACKING, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::PROMOTION_OVERVIEW, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::ADVANCED_FUNCTIONALITY, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::MAPOVERLAY, Pap_Privileges::P_READ);
        $this->addPrivilege(Pap_Privileges::CAMPAIGNS_CATEGORIES, Pap_Privileges::P_READ);
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliatePrivileges.initDefault', $this);
    }
}
?>
