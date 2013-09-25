<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Word.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Country extends Gpf_DbEngine_Row {

    const STATUS_ENABLED = 'E';
    const STATUS_DISABLED = 'D';

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Countries::getInstance());
        parent::init();
    }

    public function setAccountId($id) {
        $this->set(Gpf_Db_Table_Countries::ACCOUNTID, $id);
    }
    
    public function setStatusId($id) {
        $this->set(Gpf_Db_Table_Countries::ID, $id);
    }

    public function setCountryCode($countryCode) {
        $this->set(Gpf_Db_Table_Countries::COUNTRY_CODE, $countryCode);
    }

    public function setCountry($country) {
        $this->set(Gpf_Db_Table_Countries::COUNTRY, $country);
    }

    public function setStatus($status) {
        $this->set(Gpf_Db_Table_Countries::STATUS, $status);
    }

    public function getCountry() {
        return $this->get(Gpf_Db_Table_Countries::COUNTRY);
    }

    public function getStatus() {
        return $this->get(Gpf_Db_Table_Countries::STATUS);
    }
}
