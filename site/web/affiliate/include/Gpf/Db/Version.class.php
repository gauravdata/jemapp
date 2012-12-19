<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
class Gpf_Db_Version extends Gpf_DbEngine_Row {

    public function init() {
        $this->setTable(Gpf_Db_Table_Versions::getInstance());
        parent::init();
    }
    
    public function setVersion($version) {
        $this->set(Gpf_Db_Table_Versions::NAME, $version);
    }

    public function setApplication($application) {
        $this->set(Gpf_Db_Table_Versions::APPLICATION, $application);
    }
    
    public function setDone() {
        $this->set(Gpf_Db_Table_Versions::DONE_DATE, Gpf_Common_DateUtils::now());
    }
}

?>
