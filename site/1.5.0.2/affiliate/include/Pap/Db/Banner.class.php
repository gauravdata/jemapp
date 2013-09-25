<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 28912 2010-07-27 07:53:07Z mbebjak $
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
class Pap_Db_Banner extends Gpf_DbEngine_Row {
    const SIZE_NOTAPPLICABLE = 'U';
    const SIZE_OWN = 'O';
    const SIZE_PREDEFINED = 'P';

    const STATUS_ACTIVE = 'A';
    const STATUS_HIDDEN = 'H';

    protected $width;
    protected $height;

    protected function init() {
        $this->setTable(Pap_Db_Table_Banners::getInstance());
        parent::init();
    }

    public function setDateInserted($dateInserted) {
        $this->set(Pap_Db_Table_Banners::DATEINSERTED, $dateInserted);
    }
    
    public function setAccountId($value) {
        $this->set(Pap_Db_Table_Banners::ACCOUNT_ID, $value);
    }
    
    public function getAccountId() {
        return $this->get(Pap_Db_Table_Banners::ACCOUNT_ID);
    }
    
    public function getDestinationUrl() {
        return $this->get(Pap_Db_Table_Banners::DESTINATION_URL);
    }

    public function getId() {
        return $this->get(Pap_Db_Table_Banners::ID);
    }

    public function setId($id) {
        $this->set(Pap_Db_Table_Banners::ID, $id);
    }

    public function getCampaignId() {
        return $this->get(Pap_Db_Table_Banners::CAMPAIGN_ID);
    }

    public function setName($name) {
        $this->set(Pap_Db_Table_Banners::NAME, $name);
    }

    public function getName() {
        return $this->get(Pap_Db_Table_Banners::NAME);
    }

    public function getBannerType() {
        return $this->get(Pap_Db_Table_Banners::TYPE);
    }

    public function getSeoString() {
        return $this->get(Pap_Db_Table_Banners::SEOSTRING);
    }
    
    public function setBannerType($type) {
        $this->set(Pap_Db_Table_Banners::TYPE, $type);
    }

    public function setCampaignId($value) {
        $this->set(Pap_Db_Table_Banners::CAMPAIGN_ID, $value);
    }

    public function setStatus($value) {
        $this->set(Pap_Db_Table_Banners::STATUS, $value);
    }

    public function setDestinationUrl($value) {
        $this->set(Pap_Db_Table_Banners::DESTINATION_URL, $value);
    }

    public function setSize($value) {
        $this->set(Pap_Db_Table_Banners::SIZE, $value);
    }

    public function setData1($value) {
        $this->set(Pap_Db_Table_Banners::DATA1, $value);
    }

    public function setData2($value) {
        $this->set(Pap_Db_Table_Banners::DATA2, $value);
    }

    public function setData3($value) {
        $this->set(Pap_Db_Table_Banners::DATA3, $value);
    }

    public function setData4($value) {
        $this->set(Pap_Db_Table_Banners::DATA4, $value);
    }

    public function setData5($value) {
        $this->set(Pap_Db_Table_Banners::DATA5, $value);
    }

    public function setData($num, $value) {
        $this->set(Pap_Db_Table_Banners::DATA.$num, $value);
    }

    public function getStatus() {
        return $this->get(Pap_Db_Table_Banners::STATUS);
    }

    public function getData1() {
        return $this->get(Pap_Db_Table_Banners::DATA1);
    }

    public function getData2() {
        return $this->get(Pap_Db_Table_Banners::DATA2);
    }

    public function getData3() {
        return $this->get(Pap_Db_Table_Banners::DATA3);
    }

    public function getData4() {
        return $this->get(Pap_Db_Table_Banners::DATA4);
    }

    public function getData5() {
        return $this->get(Pap_Db_Table_Banners::DATA5);
    }

    public function getData($num){
        return $this->get(Pap_Db_Table_Banners::DATA.$num);
    }

    public function setWrapperId($id){
        $this->set(Pap_Db_Table_Banners::WRAPPER_ID, $id);
    }
    
    public function getWrapperId(){
        return $this->get(Pap_Db_Table_Banners::WRAPPER_ID);
    }

    /**
     * @param Pap_Common_User $user
     * @return string
     */
    protected function getDescription(Pap_Common_User $user) {
        $description = $this->get(Pap_Db_Table_Banners::DATA2);

        $userFields = Pap_Common_UserFields::getInstance();
        $userFields->setUser($user);
        $description = $userFields->replaceUserConstantsInText($description);

        return $description;
    }

    public function getSizeType($sizeColumnName) {
        $sizeField = $this->get($sizeColumnName);
        if ($sizeField == '') {
            return self::SIZE_NOTAPPLICABLE;
        } else {
            return substr($sizeField, 0, 1);
        }
    }

    public function getWidth() {
        $this->decodeWidthAndHeight();
        return $this->width;
    }

    public function getHeight() {
        $this->decodeWidthAndHeight();
        return $this->height;
    }

    private function decodeWidthAndHeight() {
        if($this->width !== null){
            return;
        }
        if($this->isSizeDefined()) {
            $sizeField = $this->get(Pap_Db_Table_Banners::SIZE);
            $sizeArray = explode('x', substr($sizeField, 1));
            if(count($sizeArray) == 2) {
                $this->width = $sizeArray[0];
                $this->height = $sizeArray[1];
            }
        } else {
            $this->setUndefinedSize();
        }
    }

    public function isSizeDefined(){
        return $this->getSizeType(Pap_Db_Table_Banners::SIZE) !== self::SIZE_NOTAPPLICABLE;
    }

    protected function setUndefinedSize(){
        $this->width = '';
        $this->height = '';
    }

    public function delete() {
        if (Gpf_Application::isDemo() && Gpf_Application::isDemoEntryId($this->getId())) {
            throw new Gpf_Exception("Demo banner can not be deleted");
        }
        return parent::delete();
    }
    
    public function update($updateColumns = array()) {
        parent::update($updateColumns);
        Pap_Db_Table_CachedBanners::deleteCachedBannersForBanner($this->getId());
    }
    
    protected function beforeSaveAction() {
        parent::beforeSaveAction();
        if ($this->getCampaignId() != '') {
            $this->setAccountId($this->resolveAccountId($this->getCampaignId()));
        }
    }

    /**
     * @throws Gpf_Exception
     */
    protected function resolveAccountId($campaignId) {
        $campaign = new Pap_Db_Campaign();
        $campaign->setId($campaignId);
        try {
            $campaign->load();
            return $campaign->getAccountId();
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Gpf_Exception("Can not resolve accountId for campaign '$campaignId' in Pap_Db_Banner::resolveAccountId()");
        }
    }
}

?>
