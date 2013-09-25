<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ActiveView.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Db_AffiliateScreen extends Gpf_DbEngine_Row {
  
    const HEADER_SHOW = Gpf::YES;
    const HEADER_HIDE = Gpf::NO;
    
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Pap_Db_Table_AffiliateScreens::getInstance());
        parent::init();
    }
    
    public function setId($value) {
        $this->set(Pap_Db_Table_AffiliateScreens::ID, $value);
    }

    public function getId() {
        return $this->get(Pap_Db_Table_AffiliateScreens::ID);
    }

    public function setAccountId($value) {
        $this->set(Pap_Db_Table_AffiliateScreens::ACCOUNTID, $value);
    }

    public function setCode($value) {
        $this->set(Pap_Db_Table_AffiliateScreens::CODE, $value);
    }

    public function setTitle($value) {
        $this->set(Pap_Db_Table_AffiliateScreens::TITLE, $value);
    }

    public function setParams($value) {
        $this->set(Pap_Db_Table_AffiliateScreens::PARAMS, $value);
    }
    
    public function setShowHeader($value) {
        $this->set(Pap_Db_Table_AffiliateScreens::SHOWHEADER, $value);
    }
}

?>
