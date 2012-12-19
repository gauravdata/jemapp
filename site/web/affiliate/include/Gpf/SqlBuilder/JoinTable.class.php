<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: JoinTable.class.php 29120 2010-08-24 07:30:36Z mbebjak $
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
class Gpf_SqlBuilder_JoinTable extends Gpf_Object implements Gpf_SqlBuilder_FromClauseTable {
    private $type;
    private $name;
    private $alias;
    private $onCondition;

    function __construct($type, $name, $alias, $onCondition) {
        $this->type = $type;
        $this->name = $name;
        $this->alias = $alias;
        $this->onCondition = $onCondition;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getName() {
        return $this->name;
    }

    public function toString() {
        $out = " ".$this->type." JOIN ".$this->name;
        if(!empty($this->alias)) {
            $out .= ' ' . $this->alias;
        }
        $out .= " ON ".$this->onCondition;
        return $out;
    }

    public function isJoin() {
        return true;
    }
    
    public function getRequiredPreffixes() {
        $requiredPreffixes = array();
        $matches = array();
        if (preg_match_all("/([a-zA-Z]+)\./", $this->onCondition, $matches) > 0) {
            foreach ($matches[1] as $preffix) {
                $requiredPreffixes[$preffix] = $preffix;
            }
        }
        return $requiredPreffixes;
    }
}

?>
