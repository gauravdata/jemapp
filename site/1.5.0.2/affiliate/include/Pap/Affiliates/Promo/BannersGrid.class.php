<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
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
class Pap_Affiliates_Promo_BannersGrid extends Pap_Common_Banner_BannersGrid {
    /**
     * @var Pap_Affiliates_Promo_BannersGrid
     */
    private static $instance;

    /**
     * contains filter values for displaying stats
     * @var string
     */
    private $displayStats = '';
    private $displayCampaignDetails = '';
    private $statsDateFrom;
    private $statsDateTo;
    private $statsChannel = null;

    function __construct() {
        parent::__construct();
        $this->user->setId(Gpf_Session::getAuthUser()->getPapUserId());
        $this->user->load();
    }

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @service banner read
     */
    public function getBannerPreview(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $id = $form->getFieldValue('bannerId');
        $type = $form->getFieldValue('rtype');
        $actualSize = $form->getFieldValue('actualSize');
        try {
            $bannerObj = $this->bannerFactory->getBannerObject($id, $type);
        } catch (Pap_Common_Banner_NotFound $e) {
            $form->setField('bannerPreview', 'Unknown banner type');
            return $form;
        }
        $bannerObj->setViewInActualSize($actualSize);
        $form->setField('bannerPreview', $bannerObj->getPreview(new Pap_Common_User()));
        return $form;
    }

    protected function initViewColumns() {
        $this->addViewColumn('bannerwidget', $this->_("Banner"), false);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('bannerwidget', '', 'N');
    }

    protected function initRequiredColumns() {
        parent::initRequiredColumns();
        $this->addRequiredColumn('rorder');
        $this->addRequiredColumn('description');
        $this->addRequiredColumn('size');
        $this->addRequiredColumn('destinationurl');
    }

    protected function buildWhere() {
        $this->_selectBuilder->where->add('c.'.Pap_Db_Table_Campaigns::STATUS, 'IN',
        array(Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE, Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED));
        $this->_selectBuilder->where->add('b.'.Pap_Db_Table_Banners::STATUS, '=', Pap_Db_Banner::STATUS_ACTIVE);
        if ($this->getShowOnlyWithStatsFromFilter()) {
            $statsCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
            $statsCondition->add('im.raw', '>', 0, 'OR');
            $statsCondition->add('cl.raw', '>', 0, 'OR');
            $statsCondition->add('tr.count', '>', 0, 'OR');
            $this->_selectBuilder->where->addCondition($statsCondition);
        }
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannersGrid.buildWhere', $this->_selectBuilder);
    }

    protected function buildOrder() {
        $this->_selectBuilder->orderBy->add('rorder');
    }

    private function getStatsParametersFromFilter() {
        $this->statsChannel = $this->getChannel();

        $filters = $this->getParam("filters");
        if (!is_array($filters)) {
            return;
        }

        $arrParams = array();
        foreach ($filters as $filterArray) {
            $filter = new Gpf_SqlBuilder_Filter($filterArray);
            if($filter->getCode() == "statsdate") {
                $arrParams = $filter->addDateValueToArray($arrParams);
            }
        }

        if(count($arrParams) > 0) {
            $this->statsDateFrom = $arrParams['dateFrom'];
            $this->statsDateTo = $arrParams['dateTo'];
        } else {
            $this->statsDateFrom = date("Y-m-d H:i:s", Gpf_DateTime::MIN_TIMESTAMP);
            $this->statsDateTo = date("Y-m-d H:i:s", time());
        }

        return '';
    }

    /**
     * @return boolean
     */
    private function getDisplayStatsFromFilter() {
        $this->displayStats = $this->getValueFromFilter("displaystats");
        if($this->displayStats == Gpf::YES) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return boolean
     */
    private function getShowOnlyWithStatsFromFilter() {
        if($this->getValueFromFilter("show_with_stats_only") == Gpf::YES) {
            return true;
        } else {
            return false;
        }
    }

    private function getValueFromFilter($name) {
        $filters = $this->getParam("filters");
        if (!is_array($filters)) {
            return;
        }
        foreach ($filters as $filterArray) {
            $filter = new Gpf_SqlBuilder_Filter($filterArray);
            if ($filter->getCode() == $name) {
                return $filter->getValue();
            }
        }

        return;
    }

    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
        $inputResult = $this->addBannerCode($inputResult);
        $inputResult = $this->addCampaignDetails($inputResult);
        $inputResult = $this->addOther($inputResult);
        return $inputResult;
    }

    protected function doComputeStatistics() {
        return $this->getDisplayStatsFromFilter();
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $this->getStatsParametersFromFilter();

        $statsParameters = new Pap_Stats_Params();
        if($this->statsChannel != null) {
            $statsParameters->setChannel($this->statsChannel->getId());
        }
        $statsParameters->setAffiliateId(Gpf_Session::getAuthUser()->getPapUserId());
        $statsParameters->setDateRange($this->statsDateFrom, $this->statsDateTo);
        $statsParameters->setType(Pap_Common_Constants::TYPE_SALE);
        $statsParameters->setStatus(Pap_Common_Constants::STATUS_APPROVED);

        return $statsParameters;
    }

    private function addBannerCode(Gpf_Data_RecordSet $inputResult) {
        $inputResult->addColumn('bannercode', '');
        $inputResult->addColumn('bannerpreview', '');
        $inputResult->addColumn('bannerclickurl', '');
        $inputResult->addColumn('bannerdirectlinkcode', '');
        $isDirectLinkEnabled = $this->getIsDirectLinkEnabled();

        $this->statsChannel = $this->getChannel();

        foreach ($inputResult as $record) {
            try {
                $bannerObj = $this->bannerFactory->getBannerFromRecord($record);
            } catch (Pap_Common_Banner_NotFound $e) {
                $record->set('bannercode', $this->_('Unknown banner'));
                $record->set('bannerpreview', $this->_('Unknown banner'));
                $record->set('bannerclickurl', $this->_('Unknown banner'));
                $record->set('bannerdirectlinkcode', $this->_('Unknown banner'));
                continue;
            }
            if($this->statsChannel != null) {
                $bannerObj->setChannel($this->statsChannel);
            }

            $record->set('bannercode', $bannerObj->getCode($this->user));
            $record->set('bannerpreview', $bannerObj->getPreview($this->user));
            $record->set('bannerclickurl', $bannerObj->getClickUrl($this->user));
            $record->set('destinationurl', $bannerObj->getDestinationUrl($this->user));

            if($isDirectLinkEnabled <> Gpf::YES) {
                $record->set('bannerdirectlinkcode', '');
            } else {
                $record->set('bannerdirectlinkcode', $bannerObj->getDirectLinkCode($this->user));
            }
        }
        return $inputResult;
    }

    private function addOther(Gpf_Data_RecordSet $inputResult) {
        $inputResult->addColumn('displaystats', $this->getDisplayStatsFromFilter() ? Gpf::YES : Gpf::NO);
        $inputResult->addColumn('userid', $this->user->getId());
        if ($this->statsChannel != null) {
            $inputResult->addColumn('channel', $this->statsChannel->getName());
            $inputResult->addColumn('channelcode', $this->statsChannel->getValue());
        } else {
            $inputResult->addColumn('channel', '');
            $inputResult->addColumn('channelcode', '');
        }
        return $inputResult;
    }

    private function addCampaignDetails(Gpf_Data_RecordSet $inputResult) {
        $inputResult->addColumn('campaigndetails');

        $commissionsTable = Pap_Db_Table_Commissions::getInstance();
        $allCommissions = $commissionsTable->getAllCommissionsInCampaign();
        $campaignCommissions = array();

        foreach ($inputResult as $record) {
            $campaignId = $record->get('campaignid');
            if (!array_key_exists($campaignId, $campaignCommissions)) {
                $campaign = new Pap_Db_Campaign();
                $campaign->setId($campaignId);
                $commissionGroupId = $campaign->checkUserIsInCampaign(Gpf_Session::getAuthUser()->getPapUserId());
                if ($commissionGroupId == false) {
                    $commissionGroupId = null;
                }
                $campaignCommissions[$campaignId] =  $commissionsTable->getCommissionsDescription($campaignId,$allCommissions,$commissionGroupId);
            }
            $record->set('campaigndetails', $campaignCommissions[$campaignId]);
        }
        return $inputResult;
    }

    private function getChannel() {
        $filters = $this->getParam("filters");

        if($filters != null && $filters != false) {
            if (is_array($filters)) {
                foreach ($filters as $filterArray) {
                    $filter = new Gpf_SqlBuilder_Filter($filterArray);
                    $filterCode = $filter->getCode();
                    if($filterCode == 'channel') {
                        return $this->loadChannelFromId($filter->getValue());
                    }
                }
            }
        }

        return null;
    }

    private function loadChannelFromId($channelId) {
        $channel = new Pap_Db_Channel();
        $channel->setPrimaryKeyValue($channelId);
        $channel->set(Pap_Db_Table_Channels::USER_ID, Gpf_Session::getAuthUser()->getPapUserId());
        try {
            $channel->loadFromData(array('channelid', 'userid'));
            return $channel;
        } catch(Gpf_Exception $e) {
        }

        return null;
    }

    private function getIsDirectLinkEnabled() {
        // check if it is supported in general
        $support = Gpf_Settings::get(Pap_Settings::SUPPORT_DIRECT_LINKING);
        if($support <> Gpf::YES) {
            return Gpf::NO;
        }

        // check if this user has at least one direct link approved
        $directLinks = new Pap_Db_DirectLinkUrl();
        if($directLinks->checkUserApprovedDirectLinks($this->user->getId())) {
            return Gpf::YES;
        } else {
            return Gpf::NO;
        }
    }

    /**
     * @service banner read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service banner export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }

    /**
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addStringField(Pap_Db_Table_Banners::ID, $this->_("Banner Id"));
        $filterFields->addStringField(Pap_Db_Table_Banners::NAME, $this->_("Name"));
        $filterFields->addStringField(Pap_Db_Table_Banners::DESTINATION_URL, $this->_("Target Url"));
        $filterFields->addStringField(Pap_Db_Table_Banners::CAMPAIGN_ID, $this->_("Campaign Id"));
        $filterFields->addStringField(Pap_Db_Table_Banners::DESCRIPTION, $this->_("Description"));
        $filterFields->addStringField(Pap_Db_Table_Banners::DATA1, $this->_("Data1"));
        $filterFields->addStringField(Pap_Db_Table_Banners::DATA2, $this->_("Data2"));
        $filterFields->addStringField(Pap_Db_Table_Banners::DATA3, $this->_("Data3"));
        $filterFields->addStringField(Pap_Db_Table_Banners::DATA4, $this->_("Data4"));
        $filterFields->addStringField(Pap_Db_Table_Banners::DATA5, $this->_("Data5"));
        return $filterFields->getRecordSet();
    }
}
?>
