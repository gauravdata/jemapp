<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class Pap_Stats_Computer_TransactionsStatsBuilderContext {

    /**
     * @var Pap_Stats_Computer_TransactionsStatsBuilder
     */
    private $transactionsStatsBuilder;

    private $groupColumn;

    public function __construct(Pap_Stats_Computer_TransactionsStatsBuilder $transactionsStatsBuilder, $groupColumn) {
        $this->transactionsStatsBuilder = $transactionsStatsBuilder;
        $this->groupColumn = $groupColumn;
    }

    /**
     * @return Pap_Stats_Computer_TransactionsStatsBuilder
     */
    public function getTransactionsStatsBuilder() {
        return $this->transactionsStatsBuilder;
    }

    public function getGroupColumn() {
        return $this->groupColumn;
    }
}
?>
