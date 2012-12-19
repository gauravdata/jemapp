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
abstract class Pap_Mail_SendTaskBase extends Gpf_Tasks_LongTask {

	protected $time;

	protected function execute() {
		$this->time = Gpf_Common_DateUtils::getClientTime(time());		
	}

	/**
	 * @return boolean
	 */
	protected function shouldSendMonthly($monthlyDay) {
		return $this->isMonthlySendDay($monthlyDay) || ($this->isLastDay() && !$this->hasSentDay($monthlyDay));
	}

	/**
	 * @return boolean
	 */
	private function isMonthlySendDay($monthlyDay) {
		return date('j', $this->time) == $monthlyDay;
	}

	/**
	 * @return boolean
	 */
	protected function isLastDay() {
		return date('j', $this->time) == $this->getLastMonthDay();		
	}

	/**
	 * @return boolean
	 */
	protected function hasSentDay($monthlyDay) {		
		return $monthlyDay <= $this->getLastMonthDay();		
	}
	
	/**
	 * @return String
	 */
	private function getLastMonthDay() {
		return date('j', mktime(0, 0, 0,date("m",$this->time) + 1, 0, date("Y",$this->time)));
	}
}
