<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
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
class Pap_Common_StatsColumnsContext {

    /**
     * @var array
     */
    private $statsColumns;

    public function __construct(array $statsColumns) {
        $this->statsColumns = $statsColumns;
    }

    /**
     * @return array
     */
    public function getStatsColumns() {
        return $this->statsColumns;
    }

    public function setStatsColumns(array $statsColumns) {
        $this->statsColumns = $statsColumns;
    }
}
?>
