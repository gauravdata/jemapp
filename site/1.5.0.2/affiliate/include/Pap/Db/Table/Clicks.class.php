<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: DailyClicks.class.php 23891 2009-03-23 13:11:09Z mbebjak $
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
class Pap_Db_Table_Clicks extends Pap_Db_Table_ClicksImpressions {
	const ID = "clickid";
	const DECLINED = "declined";
	
    private static $instance;
        
    /**
     * @return Pap_Db_Table_Clicks
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_clicks');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
	protected function initColumns() {
	    $this->createPrimaryColumn(self::ID, self::INT);
		parent::initColumns();
		$this->createColumn(self::DECLINED, self::INT);
	}
	
    protected function initStatsSelect(Gpf_SqlBuilder_SelectClause  $select) {
        parent::initStatsSelect($select);
        $select->add('sum('.self::DECLINED.')', self::DECLINED);
    }
}

?>
