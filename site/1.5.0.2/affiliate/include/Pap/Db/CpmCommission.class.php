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
class Pap_Db_CpmCommission extends Gpf_DbEngine_Row {
    
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Pap_Db_Table_CpmCommissions::getInstance());
        parent::init();
    }
    
    public function setUserId($userId) {
        $this->set(Pap_Db_Table_CpmCommissions::USERID, $userId);
    }
    
    public function setBannerId($bannerId) {
        $this->set(Pap_Db_Table_CpmCommissions::BANNERID, $bannerId);
    }

    public function setCount($count) {
        $this->set(Pap_Db_Table_CpmCommissions::COUNT, $count);
    }

    public function getCount() {
        return $this->get(Pap_Db_Table_CpmCommissions::COUNT);
    }
}

?>
