<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Banners.class.php 26541 2009-12-11 13:26:57Z mbebjak $
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
class Pap_Db_Table_Banners extends Gpf_DbEngine_Table {
    const ID = 'bannerid';
    const ACCOUNT_ID = 'accountid';
    const CAMPAIGN_ID = 'campaignid';
    const WRAPPER_ID = 'wrapperid';
    const TYPE = 'rtype';
    const STATUS = 'rstatus';
    const NAME = 'name';
    const DESTINATION_URL = 'destinationurl';
    const DATEINSERTED = 'dateinserted';
    const SIZE = 'size';
    const DATA1 = 'data1';
    const DATA2 = 'data2';
    const DATA3 = 'data3';
    const DATA4 = 'data4';
    const DATA5 = 'data5';
    const DATA = 'data';
    const ORDER = 'rorder';
    const DESCRIPTION = 'description';
    const SEOSTRING = 'seostring';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_banners');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::ACCOUNT_ID, 'char', 8);
        $this->createColumn(self::CAMPAIGN_ID, 'char', 8);
        $this->createColumn(self::WRAPPER_ID, 'char', 8);
        $this->createColumn(self::TYPE, 'char', 1);
        $this->createColumn(self::STATUS, 'char', 1);
        $this->createColumn(self::NAME, 'char', 100);
        $this->createColumn(self::DESTINATION_URL, 'char', 1000);
        $this->createColumn(self::DATEINSERTED, 'datetime', 0);
        $this->createColumn(self::SIZE, 'char', 50);
        $this->createColumn(self::DATA.'1', 'text');
        $this->createColumn(self::DATA.'2', 'text');
        $this->createColumn(self::DATA.'3', 'text');
        $this->createColumn(self::DATA.'4', 'text');
        $this->createColumn(self::DATA.'5', 'text');
        $this->createColumn(self::DATA.'6', 'text');
        $this->createColumn(self::DATA.'7', 'text');
        $this->createColumn(self::DATA.'8', 'text');
        $this->createColumn(self::DATA.'9', 'text');
        $this->createColumn(self::ORDER, 'int', 0);
        $this->createColumn(self::DESCRIPTION, 'text');
        $this->createColumn(self::SEOSTRING, 'text');
    }

    protected function initConstraints() {
		$this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::BANNER_ID, new Pap_Db_DirectLinkUrl());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CpmCommissions::BANNERID, new Pap_Db_CpmCommission());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID, new Pap_Db_BannerInRotator());
         
        $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_RawClicks::BANNERID, new Pap_Db_RawClick());
        $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Clicks::BANNERID, new Pap_Db_Click());
         
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Impressions::BANNERID, new Pap_Db_Impression());
         
        $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::BANNER_ID, new Pap_Db_DirectLinkUrl());
        $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Transactions::BANNER_ID, new Pap_Db_Transaction());
         
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID, new Pap_Db_BannerInRotator());
        
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CachedBanners::BANNERID, new Pap_Db_CachedBanner());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CachedBanners::PARENTBANNERID, new Pap_Db_CachedBanner());
    }


    /**
     * checks if banner name is unique
     *
     * @return unknown
     */
    public function checkUniqueName($name, $bannerId, $accountId) {
        $result = new Gpf_Data_RecordSet('id');

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID, 'bannerid');
        $selectBuilder->select->add('name', 'name');
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add('name', '=', $name);
        $selectBuilder->where->add('accountid', '=', $accountId);
        if($bannerId != '') {
            $selectBuilder->where->add('bannerid', '<>', $bannerId);
        }
         
        $result->load($selectBuilder);
        return $result;
    }
}
?>
