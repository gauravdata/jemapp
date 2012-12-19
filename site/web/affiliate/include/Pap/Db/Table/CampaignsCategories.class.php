<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActiveViews.class.php 18002 2008-05-13 18:31:39Z aharsani $
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
class Pap_Db_Table_CampaignsCategories extends Gpf_DbEngine_Table {

    const CATEGORYID = "categoryid";
    const DESCRIPTION = "description";
    const IMAGE = 'image';
    
    private static $instance;
        
    /**
     * @return Pap_Db_Table_CampaignsCategories
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_campaignscategories');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::CATEGORYID, self::INT, 0, true);
        $this->createColumn(self::DESCRIPTION, self::CHAR, 255);
        $this->createColumn(self::IMAGE, self::CHAR, 255);
    }
}

?>
