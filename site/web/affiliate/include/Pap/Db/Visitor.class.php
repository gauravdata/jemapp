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
 * @deprecated
 */
class Pap_Db_Visitor extends Gpf_DbEngine_Row {
    
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Pap_Db_Table_Visitors::getInstance());
        parent::init();
    }
    
    public function getId() {
        return $this->get(Pap_Db_Table_Visitors::ID);
    }
    
    public function setId($id) {
        $this->set(Pap_Db_Table_Visitors::ID, $id);
    }
    
    public function getName() {
        return $this->get(Pap_Db_Table_Visitors::NAME);
    }
    
    public function setName($name) {
        $this->set(Pap_Db_Table_Visitors::NAME, $name);
    }
    
    public function getEmail() {
        return $this->get(Pap_Db_Table_Visitors::EMAIL);
    }
    
    public function setEmail($email) {
        $this->set(Pap_Db_Table_Visitors::EMAIL, $email);
    }
}

?>
