<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 24347 2009-05-08 12:36:40Z mjancovic $
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
class Pap_Db_BannerWrapper extends Gpf_DbEngine_Row { 

    protected function init() {
        $this->setTable(Pap_Db_Table_BannerWrappers::getInstance());
        parent::init();
    }

    public function getId() {
        return $this->get(Pap_Db_Table_BannerWrappers::ID);
    }

    public function setId($id) {
        $this->set(Pap_Db_Table_BannerWrappers::ID, $id);
    }

    public function getName() {
        return $this->get(Pap_Db_Table_BannerWrappers::NAME);
    }

    public function setName($name) {
        $this->set(Pap_Db_Table_BannerWrappers::NAME, $name);
    }

    public function getCode() {
        return $this->get(Pap_Db_Table_BannerWrappers::CODE);
    }

    public function setCode($name) {
        $this->set(Pap_Db_Table_BannerWrappers::CODE, $name);
    }
}

?>
