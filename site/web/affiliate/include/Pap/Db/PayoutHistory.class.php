<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_PayoutHistory extends Gpf_DbEngine_Row {
        
    function __construct(){
        parent::__construct();
    }
    
    function init() {
        $this->setTable(Pap_Db_Table_PayoutsHistory::getInstance());
        parent::init();
    }
    
    public function getId() {
        return $this->get(Pap_Db_Table_PayoutsHistory::ID);
    }
    
    public function setId($id) {
        $this->set(Pap_Db_Table_PayoutsHistory::ID, $id);
    }
    
    public function setAccountId($value) {
        $this->set(Pap_Db_Table_PayoutsHistory::ACCOUNTID, $value);
    }
    
    public function setAffiliateNote($note) {
        $this->set(Pap_Db_Table_PayoutsHistory::AFFILIATE_NOTE, $note);
    }
    
    public function getAffiliateNote() {
        return $this->get(Pap_Db_Table_PayoutsHistory::AFFILIATE_NOTE);
    }
    
    public function setMerchantNote($note) {
        $this->set(Pap_Db_Table_PayoutsHistory::MERCHANT_NOTE, $note);
    }
    
    public function getMerchantNote() {
        return $this->get(Pap_Db_Table_PayoutsHistory::MERCHANT_NOTE);
    }
    
    public function getDateInserted() {
        return $this->get(Pap_Db_Table_PayoutsHistory::DATEINSERTED);
    }
    
    public function setDateInserted($date) {
        $this->set(Pap_Db_Table_PayoutsHistory::DATEINSERTED, $date);
    }
    
}

?>
