<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package ShopMachine
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
abstract class Pap_Mail_CampaignMailBase extends Pap_Mail_UserMail {

    /**
     * @var Pap_Common_Campaign
     */
    protected $campaign;

    public function __construct() {
        parent::__construct();
    }

    public function setCampaign(Pap_Common_Campaign $campaign) {
        $this->campaign = $campaign;
    }

    protected function initTemplateVariables() {
        parent::initTemplateVariables();
        $this->addVariable('campaignid', $this->_('Campaign ID'));
        $this->addVariable('campaignname', $this->_('Campaign name'));
        $this->addVariable('campaignstatus', $this->_('Campaign status'));
        $this->addVariable('campaigntype', $this->_('Campaign type'));
        $this->addVariable('campaigndescription', $this->_('Campaign description'));
    }

    protected function setVariableValues() {
        parent::setVariableValues();
        $this->setVariable('campaignid', $this->campaign->getId());
        $this->setVariable('campaignname', $this->_localize($this->campaign->getName()));
        $this->setVariable('campaignstatus', Pap_Common_Constants::getStatusAsText($this->campaign->getCampaignStatus()));
        $this->setVariable('campaigntype', $this->getTypeAsText($this->campaign->getCampaignType()));
        $this->setVariable('campaigndescription', $this->campaign->getDescription());
    }

    private function getTypeAsText($type) {
        switch ($type) {
            case Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION: return Gpf_Lang::_('Private - Visible only for invited affiliates');
            case Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC: return Gpf_Lang::_('Public - visible to all');
            case Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL: return Gpf_Lang::_('Public - with manual approval');
        }
        return Gpf_Lang::_('unknown');
    }
}
