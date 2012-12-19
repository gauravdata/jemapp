<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GadgetProperty.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_GadgetProperty extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Gpf_Db_Table_GadgetProperties::getInstance());
        parent::init();
    }
    
    public function setGadgetId($gadgetId) {
        $this->set(Gpf_Db_Table_GadgetProperties::GADGETID, $gadgetId);
    }
    
    public function getName() {
        return $this->get(Gpf_Db_Table_GadgetProperties::NAME);
    }
    
    public function setName($name) {
        $this->set(Gpf_Db_Table_GadgetProperties::NAME, $name);
    }
    
    public function getValue() {
        return $this->get(Gpf_Db_Table_GadgetProperties::VALUE);
    }
    
    public function setValue($value) {
        $this->set(Gpf_Db_Table_GadgetProperties::VALUE, $value);
    }
}

?>
