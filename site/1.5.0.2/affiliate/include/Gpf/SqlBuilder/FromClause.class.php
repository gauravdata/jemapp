<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FromClause.class.php 29120 2010-08-24 07:30:36Z mbebjak $
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
class Gpf_SqlBuilder_FromClause extends Gpf_Object {
    protected $clause = array();
    const LEFT = "LEFT";
    const RIGHT = "RIGHT";
    const INNER = "INNER";

    public function add($tableName, $tableAlias = '') {
        $this->checkAlias($tableAlias);

        $this->clause[] = new Gpf_SqlBuilder_FromTable($tableName, $tableAlias);
    }

    private function addJoin($type, $tableName, $tableAlias, $onCondition) {
        $this->checkAlias($tableAlias);

        $this->clause[] = new Gpf_SqlBuilder_JoinTable($type, $tableName, $tableAlias, $onCondition);
    }

    public function addLeftJoin($tableName, $tableAlias, $onCondition) {
        $this->addJoin(self::LEFT, $tableName, $tableAlias, $onCondition);
    }

    public function addRightJoin($tableName, $tableAlias, $onCondition) {
        $this->addJoin(self::RIGHT, $tableName, $tableAlias, $onCondition);
    }

    public function addSubselect(Gpf_SqlBuilder_SelectBuilder $query, $tableAlias) {
        $this->checkAlias($tableAlias);
        $this->clause[] = new Gpf_SqlBuilder_SubSelectTable($query, $tableAlias);
    }

    public function replacePrimarySource(Gpf_SqlBuilder_SelectBuilder $query, $tableAlias) {
        $this->clause[0] = new Gpf_SqlBuilder_SubSelectTable($query, $tableAlias);
    }

    public function addClause(Gpf_SqlBuilder_FromClauseTable $clause) {
        $this->clause[] = $clause;
    }

    /**
     * @return array<Gpf_SqlBuilder_FromClauseTable>
     */
    public function getAllFroms() {
        return $this->clause;
    }

    /**
     * Removes unnecessary tables from the from clause
     *
     * @param $requiredPreffixes
     */
    public function prune(array $requestedPreffixes) {
        $requiredPreffixes = array();
        foreach ($requestedPreffixes as $preffix) {
            $requiredPreffixes[$preffix] = $preffix;
        }
        
        do {
            $requiredPreffixesCount = count($requiredPreffixes);
            $requiredPreffixes = array_merge($requiredPreffixes, $this->getJoinTableDependencies($requiredPreffixes));
        } while ($requiredPreffixesCount < count($requiredPreffixes));
        
        foreach ($this->clause as $i => $table) {
            if (!in_array($table->getAlias(), $requiredPreffixes)) {
                unset($this->clause[$i]);
            }
        }
    }
    
    private function getJoinTableDependencies($requiredPreffixes) {
        $joinTableDependencies = array();
        foreach ($this->clause as $table) {
            if (in_array($table->getAlias(), $requiredPreffixes) && $table instanceof Gpf_SqlBuilder_JoinTable) {
                $joinTableDependencies = array_merge($joinTableDependencies, $table->getRequiredPreffixes());
            }
        }
        return $joinTableDependencies;
    }

    /**
     *
     * @param $tableAlias
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getSubSelect($tableAlias) {
        if(empty($tableAlias)) {
            throw new Gpf_Exception('Could not return empty alias table');
        }
        $builderTable = $this->getFromClauseTable($tableAlias);
        if(!($builderTable instanceof Gpf_SqlBuilder_SubSelectTable)) {
            throw new Gpf_Exception('SubSelect table does not exist.');
        }
        return $builderTable->getSubSelect();
    }

    private function getFromClauseTable($tableAlias) {
        foreach ($this->clause as $key => $fromTableObj) {
            if($tableAlias == $fromTableObj->getAlias()) {
                return $fromTableObj;
            }
        }
        throw new Gpf_Exception("Table alias $tableAlias does not exist.");
    }

    public function addInnerJoin($tableName, $tableAlias, $onCondition) {
        $this->addJoin(self::INNER, $tableName, $tableAlias, $onCondition);
    }

    public function isEmpty() {
        return count($this->clause) <= 0;
    }

    public function containsAlias($alias) {
        if(empty($alias)) {
            return false;
        }
        foreach ($this->clause as $key => $fromTableObj) {
            if($alias == $fromTableObj->getAlias()) {
                return true;
            }
        }
        return false;
    }

    private function checkAlias($tableAlias) {
        if($this->containsAlias($tableAlias)) {
            throw new Gpf_Exception('Table alias already exists');
        }
    }

    public function toString() {
        $out = '';
        foreach ($this->clause as $key => $fromTableObj) {
            $out .= ($out && !$fromTableObj->isJoin()) ? ',' : '';
            $out .= $fromTableObj->toString();
        }
        return $out . " ";
    }

    public function equals(Gpf_SqlBuilder_FromClause  $from) {
        return $from->toString() == $this->toString();
    }
}

?>
