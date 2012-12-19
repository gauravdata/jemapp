<?php
/**
 *   @copyright Copyright (c) 2010 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Account.class.php 27046 2010-02-02 12:30:55Z mkendera $
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
class Gpf_Db_JobsRun extends Gpf_DbEngine_Row {

    function init() {
        $this->setTable(Gpf_Db_Table_JobsRuns::getInstance());
        parent::init();
    }
    
    public function getStartTime() {
    	return $this->get(Gpf_Db_Table_JobsRuns::STARTTIME); 
    }
    
    public function setStartTime($time) {
        return $this->set(Gpf_Db_Table_JobsRuns::STARTTIME, $time); 
    }
}
?>
