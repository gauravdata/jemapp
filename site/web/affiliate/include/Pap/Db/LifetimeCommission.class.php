<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: Channel.class.php 18660 2008-06-19 15:30:59Z aharsani $
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
class Pap_Db_LifetimeCommission extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Pap_Db_Table_LifetimeCommissions::getInstance());
        parent::init();
    }
        
    public function getIdentifier() {
    	return $this->get(Pap_Db_Table_LifetimeCommissions::IDENTIFIER);
    }

    public function setIdentifier($value) {
    	$this->set(Pap_Db_Table_LifetimeCommissions::IDENTIFIER, $value);
    }
    
    public function getUserId() {
    	return $this->get(Pap_Db_Table_LifetimeCommissions::USER_ID);
    }

    public function setUserId($value) {
    	$this->set(Pap_Db_Table_LifetimeCommissions::USER_ID, $value);
    }
}
?>
