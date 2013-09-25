<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class Pap_Db_CampaignInCategory extends Gpf_DbEngine_Row {

    function init() {
        $this->setTable(Pap_Db_Table_CampaignsInCategory::getInstance());
        parent::init();
    }

    public function setId($value) {
        $this->set(Pap_Db_Table_CampaignsInCategory::ID, $value);
    }
    
    public function setCategoryId($value) {
        $this->set(Pap_Db_Table_CampaignsInCategory::CATEGORYID, $value);
    }
    
    public function setCampaignId($value) {
        $this->set(Pap_Db_Table_CampaignsInCategory::CAMPAIGNID, $value);
    }
}

?>
