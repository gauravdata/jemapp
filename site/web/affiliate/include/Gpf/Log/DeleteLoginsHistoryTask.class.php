<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package PostAffiliatePro
 *   @since Version 4.5.30
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
class Gpf_Log_DeleteLoginsHistoryTask extends Gpf_Log_DeleteTaskBase {

    public function getName() {
        return $this->_('Delete Logins History');
    }

    protected function execute() {
        if ($this->isPending('deleteLoginsHistory', $this->_('Delete Logins History'))) {
            $this->deleteLoginshistory();
            $this->setDone();
        }
    }

    private function deleteLoginshistory() {
        if (Gpf_Settings::get(Gpf_Settings_Gpf::AUTO_DELETE_LOGINSHISTORY) <= 0) {
            return;
        }
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Gpf_Db_Table_LoginsHistory::getName());
        $delete->where->add(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, '<', $this->getLastDate(Gpf_Settings::get(Gpf_Settings_Gpf::AUTO_DELETE_LOGINSHISTORY))->toDateTime());
        $delete->delete();
    }
}
