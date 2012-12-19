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
class Pap_Features_BannerRotator_RotatorFormService extends Gpf_View_FormService {
    const SUCCESS = 'success';
    private $form;
    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Db_BannerInRotator();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Banner");
    }

    public function setForm($form){
        $this->form = $form;
    }
    /**
     * @service banner write
     * @param Gpf_Rpc_Params $params
     */
    public function save(Gpf_Rpc_Params $params) {
        $this->form = new Gpf_Rpc_Form($params);
        
        try {
            $this->validateData(true);
        } catch (Gpf_Exception $e) {
            $this->form->setField(self::SUCCESS, Gpf::NO);
            $this->form->setErrorMessage($e->getMessage());
            return $this->form;
        }
        $banner = $this->getBannerInRotatorObject();
        $banner->setId($this->form->getFieldValue("Id"));
        try{
            $banner->load();
        } catch (Gpf_DbEngine_NoRowException $e){
            $this->form->setSuccessful(Gpf::NO);
            $this->form->setErrorMessage($this->_("Banner does not exists"));
            return $this->form;
        }
        $banner->setValidFrom($this->form->getFieldValue("valid_from"));
        $banner->setValidUntil($this->form->getFieldValue("valid_until"));
        $banner->setRank($this->form->getFieldValue("rank"));
        try{
            $banner->save();
        } catch (Gpf_Exception $e){            
        }
        
        try {
            $this->removeRotatorFromCache($this->getBannerIdFromRotatedBannerId($this->form->getFieldValue("Id")));
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception("Warning: Problem with regeneration banners cache.");
        }
        
        $this->form->setSuccessful(Gpf::YES);
        $this->form->setInfoMessage($this->_("Banner in rotator saved"));
        return $this->form;
    }
    
    /**
     * @service banner write
     * @param Gpf_Rpc_Params $params
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $banner = new Pap_Db_BannerInRotator();
        $banner->setId($form->getFieldValue("Id"));
        $banner->load();
        $form->setField("valid_from",$banner->getValidFrom());
        $form->setField("valid_until",$banner->getValidUntil());
        $form->setField("rank",$banner->getRank());
        $form->setField("rotatedbannerid",$banner->getRotatedBannerId());
        return $form;
    }
    
    /**
     * @service banner write
     * @param Gpf_Rpc_Params $params
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        $result = parent::saveFields($params);

        $action = new Gpf_Rpc_Action($params);
        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($action->getParam("fields"));
        $field = $fields->get(0);


        $bannerInRotator = new Pap_Db_BannerInRotator();
        $bannerInRotator->setId($field->get('id'));
        $bannerInRotator->load();
        
        try {
            $this->removeRotatorFromCache($bannerInRotator->getParentBannerId());
        } catch (Gpf_Exception $e) {}
        return $result;
    }

    /**
     * @service banner add
     * @param Gpf_Rpc_Params $params
     */
    public function add(Gpf_Rpc_Params $params) {
        $this->form = new Gpf_Rpc_Form($params);
        try {
            $this->validateData();
        } catch (Gpf_Exception $e) {
            $this->form->setField(self::SUCCESS, Gpf::NO);
            $this->form->setErrorMessage($e->getMessage());
            return $this->form;
        }
        $this->form = parent::add($params);
        try {
            $this->removeRotatorFromCache($this->getBannerIdFromParams('parentbannerid', $params));
        } catch (Gpf_Exception $e) {}
        return $this->form;
    }

    protected function getBannerInRotatorObject(){
        return new Pap_Db_BannerInRotator();
    }
    
    /**
     * return Pap_Db_BannerInRotator
     */
     
    protected function getBannerIdFromRotatedBannerId($rotatedId){
        $rot = new Pap_Db_BannerInRotator();
        $rot->setId($rotatedId);
        $rot->load();
        return $rot->getParentBannerId();
        
    }
    
    protected function validateData($selfControl = false){
        
        $valid_from = $this->form->getFieldValue(Pap_Db_Table_BannersInRotators::VALID_FROM);
        $valid_until = $this->form->getFieldValue(Pap_Db_Table_BannersInRotators::VALID_UNTIL);
        if($valid_from > $valid_until && $valid_until != null && $valid_from != null) {
            throw(new Gpf_Exception($this->_("Publish banner on date must be lower then Expire banner on")));
        }
        
        try{
            $bannerInRotatorCollection = $this->getBannerInRotatorCollection($this->form->getFieldValue('rotatedbannerid'));
            foreach($bannerInRotatorCollection as $banner){
                if($selfControl){
                    if($banner->getId() ==  $this->form->getFieldValue("Id")){
                        continue;
                    }
                }
                $this->checkDates($banner);
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
    }
    
    protected function getBannerInRotatorCollection($id){
        $bannerInRotator = new Pap_Db_BannerInRotator();
        $bannerInRotator->setRotatedBannerId($id);
        $bannerInRotator->setParentBannerId($this->form->getFieldValue(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID));
        return $bannerInRotator->loadCollection(array(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID,Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID));
    }

    protected function checkDates(Pap_Db_BannerInRotator $banner){
        $from = $this->form->getFieldValue(Pap_Db_Table_BannersInRotators::VALID_FROM);
        $to = $this->form->getFieldValue(Pap_Db_Table_BannersInRotators::VALID_UNTIL);
        
        
        if ($from == null && $to == null) {
            throw(new Gpf_Exception($this->_("Rotator banner can not contain same banner twice")));
        }
                
        $fromDb = $banner->getValidFrom();
        $toDb = $banner->getValidUntil();
        
        if ($fromDb == null && $toDb == null) {
            throw(new Gpf_Exception($this->_("Rotator banner can not contain same banner twice")));
        }
        
        $dateFrom = new Gpf_DateTime($fromDb);
        $dateFrom->getClientTime()->toDateTime();
        
        $dateUntil = new Gpf_DateTime($toDb);
        $dateUntil->getClientTime()->toDateTime();
        
        $errorMsg = "Date is coliding with other date: ".$dateFrom->getClientTime()->toDateTime()." -> ".$dateUntil->getClientTime()->toDateTime();
        
        if ($from == null && $to != null){
            if ($to < $fromDb) {
                return;
            }
            throw(new Gpf_Exception($this->_($errorMsg)));
        }
        
        if ($from != null && $to == null){
            if ($from > $toDb) {
                return;
            }
            throw(new Gpf_Exception($this->_($errorMsg)));
        }
        
        if ($fromDb == null && $toDb != null){
            if ($from > $toDb) {
                return;
            }
            throw(new Gpf_Exception($this->_($errorMsg)));
        }
        
        if ($fromDb != null && $toDb == null){
            if ($to < $fromDb) {
                return;
            }
            throw(new Gpf_Exception($this->_($errorMsg)));
        }
        
        if (($from < $fromDb && $to < $fromDb) || ($from > $toDb && $to > $toDb)) {
            return;
        }
        throw(new Gpf_Exception($this->_($errorMsg)));
    }

    /**
     * @service banner delete
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        $form = parent::deleteRows($params);
        try {
            $this->removeRotatorFromCache($this->getBannerIfFromAction('bannerrotatorid', $params));
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception("Warning: Problem with regeneration banners cache.");
        }
        return $form;
    }

    private function getBannerIdFromParams($filed, Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        return $form->getFieldValue($filed);
    }

    private function getBannerIfFromAction($param, Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        return $action->getParam($param);
    }

    protected function removeRotatorFromCache($bannerId) {
        $select = new Gpf_SqlBuilder_DeleteBuilder();
        $select->from->add(Pap_Db_Table_CachedBanners::getName());
        $select->where->add(Pap_Db_Table_CachedBanners::BANNERID, '=', $bannerId);
        $select->delete();
    }
}

?>
