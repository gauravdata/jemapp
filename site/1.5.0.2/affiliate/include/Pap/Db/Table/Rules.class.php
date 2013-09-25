<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Campaigns.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_Table_Rules extends Gpf_DbEngine_Table {
	const ID = 'ruleid';
	const CAMPAIGN_ID = 'campaignid';
	const WHAT = 'what';
	const STATUS = 'status';
	const DATE = 'date';
	const SINCE = 'since';
	const EQUATION = 'equation';
	const EQUATION_VALUE_1 = 'equationvalue1';
	const EQUATION_VALUE_2 = 'equationvalue2';
	const ACTION = 'action';
	const COMMISSION_GROUP_ID = 'commissiongroupid';
	const BONUS_TYPE = 'bonustype';
	const BONUS_VALUE = 'bonusvalue';

	private static $instance;

	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	protected function initName() {
		$this->setName('pap_rules');
	}

	public static function getName() {
		return self::getInstance()->name();
	}

	protected function initColumns() {
		$this->createPrimaryColumn(self::ID, 'int');
		$this->createColumn(self::CAMPAIGN_ID, 'char', 8);
		$this->createColumn(self::WHAT, 'char', 1);
		$this->createColumn(self::STATUS, 'char', 1);
		$this->createColumn(self::DATE, 'char', 3);
		$this->createColumn(self::SINCE, 'int');
		$this->createColumn(self::EQUATION, 'char', 1);
		$this->createColumn(self::EQUATION_VALUE_1, 'float');
		$this->createColumn(self::EQUATION_VALUE_2, 'float');
		$this->createColumn(self::ACTION, 'char', 3);
		$this->createColumn(self::COMMISSION_GROUP_ID, 'char', 8);
		$this->createColumn(self::BONUS_TYPE, 'char', 1);
		$this->createColumn(self::BONUS_VALUE, 'float');
	}
}
?>
