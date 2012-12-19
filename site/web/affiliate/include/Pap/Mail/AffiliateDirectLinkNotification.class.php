<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Pap_Mail_AffiliateDirectLinkNotification extends Pap_Mail_UserMail {
    
    private $approvedDirectLinks = array();
    private $declinedDirectLinks = array();
    private $pendingDirectLinks = array();
    private $deletedDirectLinks = array();
    
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'affiliate_direct_link_notification.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Affiliate - DirectLink Notification');
        $this->subject = Gpf_Lang::_runtime('DirectLink notification');
    }
    
    protected function initTemplateVariables() {
        $this->addVariable('directlinks_approved', $this->_("DirectLinks Approved"));
        $this->addVariable('directlinks_declined', $this->_("DirectLinks Declined"));
        $this->addVariable('directlinks_pending', $this->_("DirectLinks Pending"));
        $this->addVariable('directlinks_deleted', $this->_("DirectLinks Deleted"));
        parent::initTemplateVariables();    
    }

    protected function setVariableValues() {
        parent::setVariableValues();
        
        $this->setVariable('directlinks_approved', $this->approvedDirectLinks);
        $this->setVariable('directlinks_declined', $this->declinedDirectLinks);
        $this->setVariable('directlinks_pending', $this->pendingDirectLinks);
        $this->setVariable('directlinks_deleted', $this->deletedDirectLinks);
    }
    
    public function addApprovedDirectLink(Pap_Db_DirectLinkUrl $directLink) {
        $this->approvedDirectLinks[] = $directLink->getUrl();
    }
    
    public function addDeclinedDirectLink(Pap_Db_DirectLinkUrl $directLink) {
        $this->declinedDirectLinks[] = $directLink->getUrl();
    }
    
    public function addPendingDirectLink(Pap_Db_DirectLinkUrl $directLink) {
        $this->pendingDirectLinks[] = $directLink->getUrl();
    }
    
    public function addDeletedDirectLink(Pap_Db_DirectLinkUrl $directLink) {
        $this->deletedDirectLinks[] = $directLink->getUrl();
    }
    


}
