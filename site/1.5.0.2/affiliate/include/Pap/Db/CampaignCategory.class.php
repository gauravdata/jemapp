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
class Pap_Db_CampaignCategory extends Gpf_DbEngine_Row {

    private $name;
    private $state;

    function init() {
        $this->setTable(Pap_Db_Table_CampaignsCategories::getInstance());
        parent::init();
    }

    public function setId($value) {
        $this->set(Pap_Db_Table_CampaignsCategories::CATEGORYID, $value);
    }

    public function setDescription($value) {
        $this->set(Pap_Db_Table_CampaignsCategories::DESCRIPTION, $value);
    }

    public function setImage($value) {
        $this->set(Pap_Db_Table_CampaignsCategories::IMAGE, $value);
    }
    
    public function getName($value) {
        return $this->name;
    }
}

?>
