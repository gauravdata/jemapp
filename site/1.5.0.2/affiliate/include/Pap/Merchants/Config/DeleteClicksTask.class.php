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
 * @package PostAffiliatePro
 */
class Pap_Merchants_Config_DeleteClicksTask extends Gpf_Log_DeleteTaskBase {

    public function getName() {
        return $this->_('Delete clicks');
    }

    protected function execute() {
        if ($this->isPending('deleteRawClicks', $this->_('Delete raw clicks'))) {
            $this->deleteRawClicks();
            $this->setDone();
        }
    }

    private function deleteRawClicks() {
        if (Gpf_Settings::get(Pap_Settings::AUTO_DELETE_RAWCLICKS) <= 0) {
            return;
        }
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Pap_Db_Table_RawClicks::getName());
        $delete->where->add(Pap_Db_Table_RawClicks::RSTATUS, '=', 'P');
        $delete->where->add(Pap_Db_Table_RawClicks::DATETIME, '<', $this->getLastDate(Gpf_Settings::get(Pap_Settings::AUTO_DELETE_RAWCLICKS))->toDateTime());
        $delete->delete();
    }
}
