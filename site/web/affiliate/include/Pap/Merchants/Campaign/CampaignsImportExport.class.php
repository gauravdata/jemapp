<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CampaignsExport.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Merchants_Campaign_CampaignsImportExport extends Gpf_Csv_ObjectImportExport {

    const CAMPAIGNS = 'Campaigns';
    const COMMISSIONS_TYPES = 'Commissions types';
    const COMMISSIONS_GROUPS = 'Commissions group';
    const COMMISSIONS = 'Commissions';

    public function __construct() {
    	parent::__construct();
        $this->setName(Gpf_Lang::_runtime('Campaigns'));
        $this->setDescription(Gpf_Lang::_runtime("CampaignsImportExportDescription"));
    }

    protected function execute() {
        $this->checkData();

        if ($this->delete &&
        $this->isBlockPending('delete')) {
            $this->deleteData();
            $this->setBlockDone();
        }

        $this->readData();
    }

    protected function writeData() {
        $this->writeDataHeader(self::CAMPAIGNS);
        $this->writeSelectBuilder($this->getCampaigns());
        $this->writeDataDelimiter();

        $this->writeDataHeader(self::COMMISSIONS_TYPES);
        $this->writeSelectBuilder($this->getCommissionTypes());
        $this->writeDataDelimiter();

        $this->writeDataHeader(self::COMMISSIONS_GROUPS);
        $this->writeSelectBuilder($this->getCommissionGroups());
        $this->writeDataDelimiter();

        $this->writeDataHeader(self::COMMISSIONS);
        $this->writeSelectBuilder($this->getCommission());
    }

    protected function checkData() {
        if ($this->isBlockPending('checkCampaigns')) {
        	$this->logger->debug('Check ' . self::CAMPAIGNS);
            $this->setDataHeader(self::CAMPAIGNS);
            $this->setRequiredColumns(array('!CAMPAIGNID'));
            $this->checkFile($this->getArrayHeaderColumns($this->getCampaigns()));
            $this->rewindFile();
            $this->setBlockDone();
        }

        if ($this->isBlockPending('checkCommissionTypes')) {
        	$this->logger->debug('Check ' . self::COMMISSIONS_TYPES);
            $this->setDataHeader(self::COMMISSIONS_TYPES);
            $this->setRequiredColumns(array('!COMMTYPEID'));
            $this->checkFile($this->getArrayHeaderColumns($this->getCommissionTypes()));
            $this->rewindFile();
            $this->setBlockDone();
        }

        if ($this->isBlockPending('checkCommissionGroups')) {
        	$this->logger->debug('Check ' . self::COMMISSIONS_GROUPS);
            $this->setDataHeader(self::COMMISSIONS_GROUPS);
            $this->setRequiredColumns(array('!COMMISSIONGROUPID'));
            $this->checkFile($this->getArrayHeaderColumns($this->getCommissionGroups()));
            $this->rewindFile();
            $this->setBlockDone();
        }

        if ($this->isBlockPending('checkCommissions')) {
        	$this->logger->debug('Check ' . self::COMMISSIONS);
            $this->setDataHeader(self::COMMISSIONS);
            $this->setRequiredColumns(array('!COMMISSIONID'));
            $this->checkFile($this->getArrayHeaderColumns($this->getCommission()));
            $this->rewindFile();
            $this->setBlockDone();
        }
    }

    protected function deleteData() {
    	$this->logger->debug('Delete ' . self::CAMPAIGNS);
        $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add(Pap_Db_Table_Campaigns::getName());
        $deleteBuilder->execute();

        $this->logger->debug('Delete ' . self::COMMISSIONS_TYPES);
        $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add(Pap_Db_Table_CommissionTypes::getName());
        $deleteBuilder->execute();

        $this->logger->debug('Delete ' . self::COMMISSIONS_GROUPS);
        $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add(Pap_Db_Table_CommissionGroups::getName());
        $deleteBuilder->execute();

        $this->logger->debug('Delete ' . self::COMMISSIONS);
        $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add(Pap_Db_Table_Commissions::getName());
        $deleteBuilder->execute();
    }

    protected function insert(Gpf_DbEngine_Row $dbRow) {
        if ($dbRow instanceof Pap_Db_Campaign) {
            $dbRow->insert(false);
            return;
        }
        parent::insert($dbRow);
    }

    protected function readData() {
        if ($this->isBlockPending('importCampaigns')) {
            $this->partName = self::CAMPAIGNS;
            $this->logger->debug($this->_('Import %s', $this->partName));
            $this->setDataHeader(self::CAMPAIGNS);
            $this->readDbRow('Pap_Db_Campaign', $this->getArrayHeaderColumns($this->getCampaigns()));
            $this->rewindFile();
            $this->setBlockDone();
        }

        if ($this->isBlockPending('importCommissionTypes')) {
            $this->partName = self::COMMISSIONS_TYPES;
            $this->logger->debug($this->_('Import %s', $this->partName));
            $this->setDataHeader(self::COMMISSIONS_TYPES);
            $this->readDbRow('Pap_Db_CommissionType', $this->getArrayHeaderColumns($this->getCommissionTypes()));
            $this->rewindFile();
            $this->setBlockDone();
        }

        if ($this->isBlockPending('importCommissionGroups')) {
            $this->partName = self::COMMISSIONS_GROUPS;
            $this->logger->debug($this->_('Import %s', $this->partName));
            $this->setDataHeader(self::COMMISSIONS_GROUPS);
            $this->readDbRow('Pap_Db_CommissionGroup', $this->getArrayHeaderColumns($this->getCommissionGroups()));
            $this->rewindFile();
            $this->setBlockDone();
        }

        if ($this->isBlockPending('importCommissions')) {
            $this->partName = self::COMMISSIONS;
            $this->logger->debug($this->_('Import %s', $this->partName));
            $this->setDataHeader(self::COMMISSIONS);
            $this->readDbRow('Pap_Db_Commission', $this->getArrayHeaderColumns($this->getCommission()));
            $this->setBlockDone();
        }
    }

    private function getCampaigns() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::ID, Pap_Db_Table_Campaigns::ID);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::TYPE, Pap_Db_Table_Campaigns::TYPE);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::STATUS, Pap_Db_Table_Campaigns::STATUS);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::NAME, Pap_Db_Table_Campaigns::NAME);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::DESCRIPTION, Pap_Db_Table_Campaigns::DESCRIPTION);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::DATEINSERTED, Pap_Db_Table_Campaigns::DATEINSERTED);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::ORDER, Pap_Db_Table_Campaigns::ORDER);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::NETWORK_STATUS, Pap_Db_Table_Campaigns::NETWORK_STATUS);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::LOGO_URL, Pap_Db_Table_Campaigns::LOGO_URL);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::PRODUCT_ID, Pap_Db_Table_Campaigns::PRODUCT_ID);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::DISCONTINUE_URL, Pap_Db_Table_Campaigns::DISCONTINUE_URL);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::VALID_FROM, Pap_Db_Table_Campaigns::VALID_FROM);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::VALID_TO, Pap_Db_Table_Campaigns::VALID_TO);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::VALID_NUMBER, Pap_Db_Table_Campaigns::VALID_NUMBER);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::VALID_TYPE, Pap_Db_Table_Campaigns::VALID_TYPE);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::COUNTRIES, Pap_Db_Table_Campaigns::COUNTRIES);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::ACCOUNTID, Pap_Db_Table_Campaigns::ACCOUNTID);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::COOKIELIFETIME, Pap_Db_Table_Campaigns::COOKIELIFETIME);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::OVERWRITECOOKIE, Pap_Db_Table_Campaigns::OVERWRITECOOKIE);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::GEO_CAMPAIGN_DISPLAY, Pap_Db_Table_Campaigns::GEO_CAMPAIGN_DISPLAY);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::GEO_BANNER_SHOW, Pap_Db_Table_Campaigns::GEO_BANNER_SHOW);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::GEO_TRANS_REGISTER, Pap_Db_Table_Campaigns::GEO_TRANS_REGISTER);
        $selectBuilder->from->add(Pap_Db_Table_Campaigns::getName());

        return $selectBuilder;
    }

    private function getCommissionTypes() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::ID, Pap_Db_Table_CommissionTypes::ID);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::TYPE, Pap_Db_Table_CommissionTypes::TYPE);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::STATUS, Pap_Db_Table_CommissionTypes::STATUS);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::NAME, Pap_Db_Table_CommissionTypes::NAME);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::APPROVAL, Pap_Db_Table_CommissionTypes::APPROVAL);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::CODE, Pap_Db_Table_CommissionTypes::CODE);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::RECURRENCEPRESETID, Pap_Db_Table_CommissionTypes::RECURRENCEPRESETID);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION, Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::SAVEZEROCOMMISSION, Pap_Db_Table_CommissionTypes::SAVEZEROCOMMISSION);
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::CAMPAIGNID, Pap_Db_Table_CommissionTypes::CAMPAIGNID);
        $selectBuilder->from->add(Pap_Db_Table_CommissionTypes::getName());

        return $selectBuilder;
    }

    private function getCommissionGroups() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_CommissionGroups::ID, Pap_Db_Table_CommissionGroups::ID);
        $selectBuilder->select->add(Pap_Db_Table_CommissionGroups::IS_DEFAULT, Pap_Db_Table_CommissionGroups::IS_DEFAULT);
        $selectBuilder->select->add(Pap_Db_Table_CommissionGroups::NAME, Pap_Db_Table_CommissionGroups::NAME);
        $selectBuilder->select->add(Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
        $selectBuilder->from->add(Pap_Db_Table_CommissionGroups::getName());

        return $selectBuilder;
    }

    private function getCommission() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Commissions::ID, Pap_Db_Table_Commissions::ID);
        $selectBuilder->select->add(Pap_Db_Table_Commissions::TIER, Pap_Db_Table_Commissions::TIER);
        $selectBuilder->select->add(Pap_Db_Table_Commissions::SUBTYPE, Pap_Db_Table_Commissions::SUBTYPE);
        $selectBuilder->select->add(Pap_Db_Table_Commissions::TYPE, Pap_Db_Table_Commissions::TYPE);
        $selectBuilder->select->add(Pap_Db_Table_Commissions::VALUE, Pap_Db_Table_Commissions::VALUE);
        $selectBuilder->select->add(Pap_Db_Table_Commissions::TYPE_ID, Pap_Db_Table_Commissions::TYPE_ID);
        $selectBuilder->select->add(Pap_Db_Table_Commissions::GROUP_ID, Pap_Db_Table_Commissions::GROUP_ID);
        $selectBuilder->from->add(Pap_Db_Table_Commissions::getName());

        return $selectBuilder;
    }
}
?>
