<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Campaigns.class.php 33739 2011-07-12 06:58:23Z iivanco $
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
class Pap_Db_Table_Campaigns extends Gpf_DbEngine_Table {
    const ID = 'campaignid';
    const TYPE = 'rtype';
    const STATUS = 'rstatus';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const LONG_DESCRIPTION = 'longdescription';
    const DATEINSERTED = 'dateinserted';
    const ORDER = 'rorder';
    const NETWORK_STATUS = 'networkstatus';
    const IS_DEFAULT = 'isdefault';
    const LOGO_URL = 'logourl';
    const PRODUCT_ID = 'productid';
    const DISCONTINUE_URL = 'discontinueurl';
    const VALID_FROM = 'validfrom';
    const VALID_TO = 'validto';
    const VALID_NUMBER = 'validnumber';
    const VALID_TYPE = 'validtype';
    const COUNTRIES = 'countries';
    const ACCOUNTID = 'accountid';
    const COOKIELIFETIME = 'cookielifetime';
    const OVERWRITECOOKIE = 'overwritecookie';
    const GEO_CAMPAIGN_DISPLAY = 'geocampaigndisplay';
    const GEO_BANNER_SHOW = 'geobannersshow';
    const GEO_TRANS_REGISTER = 'geotransregister';
    private static $instance;

    /**
     * @return Pap_Db_Table_Campaigns
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_campaigns');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::TYPE, 'char', 1);
        $this->createColumn(self::STATUS, 'char', 1);
        $this->createColumn(self::NAME, 'char', 100);
        $this->createColumn(self::DESCRIPTION, 'char');
        $this->createColumn(self::LONG_DESCRIPTION, 'char');
        $this->createColumn(self::DATEINSERTED, 'datetime', 0);
        $this->createColumn(self::ORDER, 'int', 0);
        $this->createColumn(self::NETWORK_STATUS, 'char', 1);
        $this->createColumn(self::IS_DEFAULT, 'char', 1);
        $this->createColumn(self::LOGO_URL, 'char', 255);
        $this->createColumn(self::PRODUCT_ID, 'char');
        $this->createColumn(self::DISCONTINUE_URL, 'char', 255);
        $this->createColumn(self::VALID_FROM, 'datetime', 0);
        $this->createColumn(self::VALID_TO, 'datetime', 0);
        $this->createColumn(self::VALID_NUMBER, 'int', 0);
        $this->createColumn(self::VALID_TYPE, 'char', 1);
        $this->createColumn(self::ACCOUNTID, 'char', 8);
        $this->createColumn(self::COOKIELIFETIME, 'int', 0);
        $this->createColumn(self::OVERWRITECOOKIE, 'char', 1);
        $this->createColumn(self::COUNTRIES, 'char', 1000);
        $this->createColumn(self::GEO_CAMPAIGN_DISPLAY, 'char', 1);
        $this->createColumn(self::GEO_BANNER_SHOW, 'char', 1);
        $this->createColumn(self::GEO_TRANS_REGISTER, 'char', 1);
    }

    protected function initConstraints() {
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Banners::CAMPAIGN_ID, new Pap_Db_Banner());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, new Pap_Db_CommissionGroup());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CommissionTypes::CAMPAIGNID, new Pap_Db_CommissionType());

        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_RawClicks::CAMPAIGNID, new Pap_Db_RawClick());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Clicks::CAMPAIGNID, new Pap_Db_Click());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Impressions::CAMPAIGNID, new Pap_Db_Impression());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Transactions::CAMPAIGN_ID, new Pap_Db_Transaction());
         
        $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::CAMPAIGN_ID, new Pap_Db_DirectLinkUrl());
         
        $this->addConstraint(new Pap_Common_Campaign_ZeroOrOneDefaultCampaignConstraint(array(self::ACCOUNTID => false, self::IS_DEFAULT=>Gpf::YES), $this->_('There must be exactly one default campaign')));
    }

    /**
     *
     * @return Pap_Db_CommissionGroup
     */
    public function getDefaultCommissionGroup($campaignId) {
        $commissionGroup = new Pap_Db_CommissionGroup();
        $commissionGroup->setCampaignId($campaignId);
        $commissionGroup->setDefault(GPF::YES);
        $commissionGroup->loadFromData();
        return $commissionGroup;
    }

    private function getCampaignsSelect() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID, 'id');
        $selectBuilder->select->add(self::NAME, 'name');
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add(self::ACCOUNTID, '=', Gpf_Session::getAuthUser()->getAccountId());
        Gpf_Plugins_Engine::extensionPoint('Pap_Db_Table_Campaigns.getCampaignsSelect', $selectBuilder);
        return $selectBuilder;
    }

    public static function getDefaultCampaignId($accountId = null) {
        if ($accountId == null) {
            $accountId = Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
        }
        $campaign = new Pap_Db_Campaign();
        $campaign->setAccountId($accountId);
        $campaign->setIsDefault();
        try {
            $campaign->loadFromData(array(self::ACCOUNTID, self::IS_DEFAULT));
            return $campaign->getId();
        } catch (Gpf_Exception $e) {
        }
        return null;
    }

    /**
     * @service campaign read
     *
     * @param Gpf_Rpc_Params $params
     */
    public function getPrivateAndManualCampaigns(Gpf_Rpc_Params $params) {
        $selectBuilder = $this->getCampaignsSelect();
        $selectBuilder->select->add(self::TYPE, 'type');
        $selectBuilder->where->add(self::TYPE, 'IN', array(Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION, Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL));
        $selectBuilder->orderBy->add(self::NAME);
        $campaigns = $selectBuilder->getAllRows();
         
        $cTable = Pap_Db_Table_Commissions::getInstance();
        $rsCommissions = $cTable->getAllCommissionsInCampaign();
        $campaigns->addColumn('commissions', '');

        foreach ($campaigns as $campaign) {
            $campaign->set('type', Pap_Common_Constants::getCampaignTypeAsText($campaign->get('type')));
            if ($cTable->findCampaignInCommExistsRecords($campaign->get('id'), $rsCommissions)) {
                $campaign->set('commissions', $cTable->getCommissionsDescription($campaign->get('id'), $rsCommissions));
            }
        }

        return $campaigns;
    }

    /**
     * @return Pap_Common_Campaign
     */
    public static function createDefaultCampaign($accountId, $campaignName, $campaignId = null, $type = Pap_Common_Campaign::CAMPAIGN_TYPE_PUBLIC) {
        $campaign = new Pap_Common_Campaign();
        if ($campaignId != null) {
            $campaign->setId($campaignId);
        }
        $campaign->setName($campaignName);
        $campaign->setDateInserted(Gpf_Common_DateUtils::now());
        $campaign->setCampaignStatus(Pap_Common_Campaign::CAMPAIGN_STATUS_ACTIVE);
        $campaign->setCampaignType($type);
        $campaign->setCookieLifetime(0);
        $campaign->resetOverwriteCookieToDefault();
        $campaign->setAccountId($accountId);
        $campaign->setIsDefault();
        $campaign->save();

        self::createDefaultCommissionSettings($campaign);

        return $campaign;
    }

    private function createDefaultCommissionSettings(Pap_Common_Campaign $campaign) {
        $commissionGroupId = $campaign->getDefaultCommissionGroup();

        $clickCommTypeId = $campaign->insertCommissionType(Pap_Common_Constants::TYPE_CLICK);
        self::createCommission($commissionGroupId, $clickCommTypeId, 1, '$', 0.5);

        $saleCommTypeId = $campaign->insertCommissionType(Pap_Common_Constants::TYPE_SALE);
        self::createCommission($commissionGroupId, $saleCommTypeId, 1, '%', 30);
        self::createCommission($commissionGroupId, $saleCommTypeId, 2, '%', 10);
    }

    private function createCommission($commissionGroupId, $commissionTypeId, $tier, $type, $value) {
        $c = new Pap_Db_Commission();

        $c->set("tier", $tier);
        $c->set("subtype", 'N');
        $c->set("commissiontype", $type);
        $c->set("commissionvalue", $value);
        $c->set("commtypeid", $commissionTypeId);
        $c->set("commissiongroupid", $commissionGroupId);

        $c->insert();
        return $c->get("commissionid");
    }
}

?>
