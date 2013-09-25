<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Db_RawImpression extends Gpf_DbEngine_Row {

    private $index;

    public function __construct($index){
        $this->index = $index;
        parent::__construct();
    }

    protected function init() {
        $this->setTable(Pap_Db_Table_RawImpressions::getInstance($this->index));
        parent::init();
    }
    
    public function getDate() {
        return $this->get(Pap_Db_Table_RawImpressions::DATE);
    }
    
    public function setUserId($id) {
        $this->set(Pap_Db_Table_RawImpressions::USERID, $id);
    }
    
    public function getUserId() {
        return $this->get(Pap_Db_Table_RawImpressions::USERID);
    }
    
    public function setBannerId($id) {
        $this->set(Pap_Db_Table_RawImpressions::BANNERID, $id);
    }
    
    public function getBannerId() {
        return $this->get(Pap_Db_Table_RawImpressions::BANNERID);
    }
    
    public function getParentBannerId() {
        return $this->get(Pap_Db_Table_RawImpressions::PARENTBANNERID);
    }
    
    public function setChannel($id) {
        $this->set(Pap_Db_Table_RawImpressions::CHANNEL, $id);
    }
    
    public function getChannel() {
        return $this->get(Pap_Db_Table_RawImpressions::CHANNEL);
    }
    
    public function getIp() {
        return $this->get(Pap_Db_Table_RawImpressions::IP);
    }
    
    public function getData1() {
        return $this->get(Pap_Db_Table_RawImpressions::DATA1);
    }
    
    public function getData2() {
        return $this->get(Pap_Db_Table_RawImpressions::DATA2);
    }
    
    public function isUnique() {
        return $this->get(Pap_Db_Table_RawImpressions::RTYPE) == 'U';
    }
}


?>
