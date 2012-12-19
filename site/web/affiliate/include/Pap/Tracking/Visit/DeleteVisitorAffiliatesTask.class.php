<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Pap_Tracking_Visit_DeleteVisitorAffiliatesTask extends Gpf_Tasks_LongTask {
    
    private $deleteLimit = 100;
     

    public function getName() {
        return $this->_('Delete visitor affiliates');
    }

    protected function execute() {
        while(!$this->isTimeToInterrupt()){
            if(!$this->deleteVisitorAffiliates()){
                return;
            }
            $this->setProgress("DeleteVisitorAffiliates");
        }
        $this->interrupt();
    }
    
    private function deleteVisitorAffiliates() {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Pap_Db_Table_VisitorAffiliates::getName());
        $delete->where->add(Pap_Db_Table_VisitorAffiliates::VALIDTO,'<',Gpf_Common_DateUtils::now());
        $delete->where->add(Pap_Db_Table_VisitorAffiliates::VALIDTO,'!=',null);
        $delete->limit->set('',$this->deleteLimit);
        $statement = $delete->delete();
        return $statement->affectedRows()>0;
    }
}
