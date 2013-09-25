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

class Pap_Features_MailToFriend_Definition extends Gpf_Plugins_Definition  {
    
    public function __construct() {
        
        $this->codeName = 'MailToFriend';
        $this->name = $this->_('Mail to friend');
        $this->description = $this->_('This feature allows affiliates to send the promo emails directly from their panel to friends. %s', '<a href="'.Gpf_Application::getKnowledgeHelpUrl('features-and-functions/mail-to-friend.html').'" target="_blank">'.$this->_('More details read in our Knowledge Base').'</a>');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addImplementation('PostAffiliate.AffiliateGeneralSettingsForm.loadGeneral','Pap_Features_MailToFriend_Main','loadSetting');
        $this->addImplementation('PostAffiliate.AffiliateGeneralSettingsForm.saveGeneral','Pap_Features_MailToFriend_Main','saveSetting');
        $this->addImplementation('Gpf_Db_Mail.scheduleNow','Pap_Features_MailToFriend_Main','raiseScheduledTime');
        
    }
}
?>
