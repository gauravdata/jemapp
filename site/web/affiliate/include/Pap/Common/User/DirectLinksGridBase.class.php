<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Common_User_DirectLinksGridBase extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_DirectLinkUrls::ID, $this->_("ID"), true);
        $this->addViewColumn(Pap_Db_Table_DirectLinkUrls::USER_ID, $this->_("Affiliate"), true);
        $this->addViewColumn(Pap_Db_Table_DirectLinkUrls::URL, $this->_("Url"), true);
        $this->addViewColumn(Pap_Db_Table_DirectLinkUrls::STATUS, $this->_("Status"), true);
        $this->addViewColumn(Pap_Db_Table_DirectLinkUrls::NOTE, $this->_("Note"), true);
        $this->addViewColumn(Pap_Db_Table_DirectLinkUrls::CHANNEL_ID, $this->_("Channel ID"), true);
        $this->addViewColumn("tracking", $this->_("Tracking"), false);
        $this->addViewColumn("channel", $this->_("Channel"), false);
        $this->addViewColumn("banner", $this->_("Banner"), false);
        $this->addViewColumn("campaign", $this->_("Campaign"), false);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_DirectLinkUrls::ID);
        $this->addDataColumn(Pap_Db_Table_DirectLinkUrls::USER_ID, "l.".Pap_Db_Table_DirectLinkUrls::USER_ID);
        $this->addDataColumn(Pap_Db_Table_DirectLinkUrls::URL,  "l.".Pap_Db_Table_DirectLinkUrls::URL);
        $this->addDataColumn(Pap_Db_Table_DirectLinkUrls::STATUS,  "l.".Pap_Db_Table_DirectLinkUrls::STATUS);
        $this->addDataColumn(Pap_Db_Table_DirectLinkUrls::CHANNEL_ID, "l.".Pap_Db_Table_DirectLinkUrls::CHANNEL_ID);
        $this->addDataColumn(Pap_Db_Table_DirectLinkUrls::NOTE,  "l.".Pap_Db_Table_DirectLinkUrls::NOTE);
        $this->addDataColumn('username', "au.username");
        $this->addDataColumn('firstname', "au.firstname");
        $this->addDataColumn('lastname', "au.lastname");
        $this->addDataColumn('channel', "ch.name");
        $this->addDataColumn('banner', "b.name");        
        $this->addDataColumn('campaign', "c.name");
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::URL, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::STATUS, '', 'N');
        $this->addDefaultViewColumn('channel', '', 'N');
        $this->addDefaultViewColumn('banner', '', 'N');
        $this->addDefaultViewColumn('campaign', '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::NOTE, '', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_DirectLinkUrls::getName(), "l");
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "l.userid = pu.userid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");        
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Channels::getName(), "ch", "ch.channelid = l.channelid");
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Banners::getName(), "b", "b.bannerid = l.bannerid");
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Campaigns::getName(), "c", "c.campaignid = l.campaignid");
    }
}
?>
