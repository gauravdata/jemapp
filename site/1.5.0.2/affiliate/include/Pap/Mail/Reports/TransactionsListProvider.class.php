<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Pap_Mail_Reports_TransactionsListProvider implements Pap_Mail_Reports_HasHtml {

	/**
	 * @var Pap_Stats_Params
	 */
	private $statsParams;
	private $timeOffset;

	public function __construct(Pap_Stats_Params $statsParams, $timeOffset) {
		$this->statsParams = $statsParams;
		$this->timeOffset = $timeOffset;
	}

	public function getHtml($limit = 9999) {
	    $transactionsList = new Pap_Mail_Reports_TransactionsList($this->statsParams, $this->timeOffset);
	    return $transactionsList->getHtml($limit);
	}
}
