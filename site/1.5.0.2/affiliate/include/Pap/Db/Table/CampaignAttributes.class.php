<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: Users.class.php 23675 2009-03-05 09:22:48Z mbebjak $
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
class Pap_Db_Table_CampaignAttributes extends Gpf_DbEngine_Table {   
	
    const ID = 'attributeid';
    const CAMPAIGN_ID = 'campaignid';
    const NAME = 'name';
    const VALUE = 'value';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_campaignattributes');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::CAMPAIGN_ID, self::CHAR, 8);
        $this->createColumn(self::NAME, self::CHAR, 40);
        $this->createColumn(self::VALUE, self::CHAR);
    }
}
?>
