<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: DailyClick.class.php 25452 2009-09-23 09:17:15Z mbebjak $
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
class Pap_Db_Click extends Pap_Db_ClickImpression {

    protected function init() {
        $this->setTable(Pap_Db_Table_Clicks::getInstance());
        parent::init();
    }

    public function addDeclined() {
        $this->addDeclinedCount(1);
    }

    public function addDeclinedCount($count) {
        $this->setDeclined($this->getDeclined()+$count);
    }

    public function mergeWith(Pap_Db_Click $clickImpression) {
        parent::mergeWith($clickImpression);
        $this->addDeclinedCount($clickImpression->getDeclined());
    }

    public function setDeclined($value) {
        $this->set(Pap_Db_Table_Clicks::DECLINED, $value);
    }

    public function getDeclined() {
        return $this->get(Pap_Db_Table_Clicks::DECLINED);
    }
}

?>
