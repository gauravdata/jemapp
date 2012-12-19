<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CommissionsExport.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Merchants_Banner_BannersImportExport extends Gpf_Csv_ObjectImportExport {
    
    public function __construct() {
    	parent::__construct();
        $this->setName(Gpf_Lang::_runtime('Banners'));
        $this->setDescription(Gpf_Lang::_runtime("BannersImportExportDescription"));
    }    
    
    protected function writeData() {
        $this->writeSelectBuilder($this->getBanners());
    }

    protected function deleteData() {
    	$deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
    	$deleteBuilder->from->add(Pap_Db_Table_Banners::getName());
    	$deleteBuilder->execute();
    }
    
    protected function readData() {
    	$this->readDbRow('Pap_Db_Banner', $this->getArrayHeaderColumns($this->getBanners()));
    }
    
    protected function checkData() {
    	$this->setRequiredColumns(array('!BANNERID'));
        $this->checkFile($this->getArrayHeaderColumns($this->getBanners()));
        $this->rewindFile();
    }
    
    private function getBanners() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Banners::ID, Pap_Db_Table_Banners::ID);
        $selectBuilder->select->add(Pap_Db_Table_Banners::ACCOUNT_ID, Pap_Db_Table_Banners::ACCOUNT_ID);
        $selectBuilder->select->add(Pap_Db_Table_Banners::CAMPAIGN_ID, Pap_Db_Table_Banners::CAMPAIGN_ID);
        $selectBuilder->select->add(Pap_Db_Table_Banners::TYPE, Pap_Db_Table_Banners::TYPE);
        $selectBuilder->select->add(Pap_Db_Table_Banners::STATUS, Pap_Db_Table_Banners::STATUS);
        $selectBuilder->select->add(Pap_Db_Table_Banners::NAME, Pap_Db_Table_Banners::NAME);
        $selectBuilder->select->add(Pap_Db_Table_Banners::DESTINATION_URL, Pap_Db_Table_Banners::DESTINATION_URL);
        $selectBuilder->select->add(Pap_Db_Table_Banners::DATEINSERTED, Pap_Db_Table_Banners::DATEINSERTED);
        $selectBuilder->select->add(Pap_Db_Table_Banners::SIZE, Pap_Db_Table_Banners::SIZE);
        $selectBuilder->select->add(Pap_Db_Table_Banners::DATA1, Pap_Db_Table_Banners::DATA1);
        $selectBuilder->select->add(Pap_Db_Table_Banners::DATA2, Pap_Db_Table_Banners::DATA2);
        $selectBuilder->select->add(Pap_Db_Table_Banners::DATA3, Pap_Db_Table_Banners::DATA3);
        $selectBuilder->select->add(Pap_Db_Table_Banners::DATA4, Pap_Db_Table_Banners::DATA4);
        $selectBuilder->select->add(Pap_Db_Table_Banners::ORDER, Pap_Db_Table_Banners::ORDER);
        $selectBuilder->select->add(Pap_Db_Table_Banners::DESCRIPTION, Pap_Db_Table_Banners::DESCRIPTION);
        $selectBuilder->select->add(Pap_Db_Table_Banners::SEOSTRING, Pap_Db_Table_Banners::SEOSTRING);
        $selectBuilder->select->add(Pap_Db_Table_Banners::WRAPPER_ID, Pap_Db_Table_Banners::WRAPPER_ID);
        $selectBuilder->from->add(Pap_Db_Table_Banners::getName());
        
        return $selectBuilder;
    }
}
?>
