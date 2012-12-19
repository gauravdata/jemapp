<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 25194 2009-08-14 09:51:35Z mjancovic $
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
class Pap_Db_CachedBanner extends Gpf_DbEngine_Row {
    
    public function __construct() {
        parent::__construct();
        $this->setRank(100);
    }
    
    protected function init() {
        $this->setTable(Pap_Db_Table_CachedBanners::getInstance());
        parent::init();
    }
    
    public function getUserId() {
        return $this->get(Pap_Db_Table_CachedBanners::USERID);
    }
    
    public function getBannerId() {
        return $this->get(Pap_Db_Table_CachedBanners::BANNERID);
    }
    
    public function getParentBannerId() {
        return $this->get(Pap_Db_Table_CachedBanners::PARENTBANNERID);
    }
    
    public function getChannel() {
        return $this->get(Pap_Db_Table_CachedBanners::CHANNEL);
    }
    
    public function getWrapper() {
        return $this->get(Pap_Db_Table_CachedBanners::WRAPPER);
    }
    
    public function getHeaders() {
        return $this->get(Pap_Db_Table_CachedBanners::HEADERS);
    }
    
    public function getCode() {
        return $this->get(Pap_Db_Table_CachedBanners::CODE);
    }
    
    public function setHeaders($value) {
        $this->set(Pap_Db_Table_CachedBanners::HEADERS, $value);
    }
    
    public function setCode($value) {
        $this->set(Pap_Db_Table_CachedBanners::CODE, $value);
    }
    
    public function setRank($value) {
        $this->set(Pap_Db_Table_CachedBanners::RANK, $value);
    }
    
    public function setValidFrom($value) {
        $this->set(Pap_Db_Table_CachedBanners::VALID_FROM, $value);
    }
    
    public function setValidUntil($value) {
        $this->set(Pap_Db_Table_CachedBanners::VALID_UNTIL, $value);
    }
    
    public function setUserid($value){
        $this->set(Pap_Db_Table_CachedBanners::USERID, $value);
    }
    
    public function setBannerId($value){
        $this->set(Pap_Db_Table_CachedBanners::BANNERID, $value);
    }
    
    public function setWrapper($value){
        $this->set(Pap_Db_Table_CachedBanners::WRAPPER, $value);
    }
    
    public function setParentBannerId($value){
        $this->set(Pap_Db_Table_CachedBanners::PARENTBANNERID, $value);
    }
    
    public function setDynamicLink($value) {
        $this->set(Pap_Db_Table_CachedBanners::DYNAMIC_LINK, $value);
    }
}

?>
