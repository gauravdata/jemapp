<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: View.class.php 21752 2008-10-20 14:22:33Z mbebjak $
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
class Gpf_Db_View extends Gpf_DbEngine_Row {
     
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Views::getInstance());
        parent::init();
    }
    
    public function getName() {
        return $this->get(Gpf_Db_Table_Views::NAME);
    }
    
    public function setId($id) {
        $this->set(Gpf_Db_Table_Views::ID, $id);
    }
    
    public function getId() {
        return $this->get(Gpf_Db_Table_Views::ID);
    }
    
    public function getRowsPerPage() {
        return $this->get(Gpf_Db_Table_Views::ROWSPERPAGE);
    }
}

?>
