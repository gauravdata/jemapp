<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: TreeNode.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Data_TreeNode implements IteratorAggregate {
    private $childs = array();
    private $value;

    /**
     * @param string $treeKey
     * @return Gpf_Data_TreeNode
     */
    public function addItem($treeKey) {
        if (!isset($this->childs[$treeKey])) {
            $this->childs[$treeKey] = new Gpf_Data_TreeNode();
        }
        return $this->childs[$treeKey];
    }
    
    /**
     *
     * @param Gpf_Data_TreeNode $child
     * @return Gpf_Data_TreeNode
     */
    public function addChild($key, Gpf_Data_TreeNode $child) {
        $this->childs[$key] = $child;
        return $child;
    }

    public function getIterator() {
        return new ArrayIterator($this->childs);
    }
    
    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }
}
?>
