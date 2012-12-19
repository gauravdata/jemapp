<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 20399 2008-08-29 15:03:21Z mbebjak $
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
class Pap_Db_BannerInRotator extends Gpf_DbEngine_Row {
     
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Pap_Db_Table_BannersInRotators::getInstance());
        parent::init();
    }

    public function setParentBannerId($id) {
        $this->set(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID, $id);
    }
    
    public function setId($id) {
        $this->set(Pap_Db_Table_BannersInRotators::ID, $id);
    }
    
    public function getId() {
        return $this->get(Pap_Db_Table_BannersInRotators::ID);
    }

    public function getClicks() {
        return $this->get(Pap_Db_Table_BannersInRotators::CLICKS);
    }

    public function getBannerId() {
        return $this->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
    }

    public function getUniqeImps() {
        return $this->get(Pap_Db_Table_BannersInRotators::UNIQ_IMPS);
    }

    public function getAllImps() {
        return $this->get(Pap_Db_Table_BannersInRotators::ALL_IMPS);
    }

    public function getRank() {
        return $this->get(Pap_Db_Table_BannersInRotators::RANK);
    }
    
    public function getRotatedBannerId() {
        return $this->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
    }
    
    public function getParentBannerId() {
        return $this->get(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID);
    }
    
    public function setRotatedBannerId($bannerid) {
        $this->set(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID, $bannerid);
    }
    
    public function setValidFrom($value) {
        $this->set(Pap_Db_Table_CachedBanners::VALID_FROM, $value);
    }
    
    public function setRank($value) {
        $this->set(Pap_Db_Table_CachedBanners::RANK, $value);
    }
    
    public function setValidUntil($value) {
        $this->set(Pap_Db_Table_CachedBanners::VALID_UNTIL, $value);
    }
    
    public function getValidFrom() {
        return $this->get(Pap_Db_Table_CachedBanners::VALID_FROM);
    }
    
    public function getValidUntil() {
        return $this->get(Pap_Db_Table_CachedBanners::VALID_UNTIL);
    }
}

?>
