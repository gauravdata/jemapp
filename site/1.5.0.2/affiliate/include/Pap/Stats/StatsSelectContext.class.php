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
class Pap_Stats_StatsSelectContext {

    /**
     * @var Gpf_SqlBuilder_UnionBuilder
     */
    private $unionBuilder;

    /**
     * @var Gpf_SqlBuilder_SelectBuilder
     */
    private $selectBuilder;

    private $groupColumn;

    private $groupColumnAlias;

    public function __construct(Gpf_SqlBuilder_UnionBuilder $unionBuilder, Gpf_SqlBuilder_SelectBuilder $selectBuilder, $groupColumn, $groupColumnAlias) {
        $this->unionBuilder = $unionBuilder;
        $this->selectBuilder = $selectBuilder;
        $this->groupColumn = $groupColumn;
        $this->groupColumnAlias = $groupColumnAlias;
    }

    /**
     * @return Gpf_SqlBuilder_UnionBuilder
     */
    public function getUnionBuilder() {
        return $this->unionBuilder;
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getSelectBuilder() {
        return $this->selectBuilder;
    }

    public function getGroupColumn() {
        return $this->groupColumn;
    }

    public function getGroupColumnAlias() {
        return $this->groupColumnAlias;
    }
}
?>
