<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Tree.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Data_Tree extends Gpf_Object {
    /**
     *
     * @var Gpf_Data_TreeNode
     */
    private $tree;
    
    public function __construct() {
        $this->tree = new Gpf_Data_TreeNode(); 
    }
    
    /**
     *
     * @param array $treePath Full path to added item - includes item id
     * @param unknown_type $item
     */
    public function add(array $treePath, $item) {
        $node = $this->tree;
        foreach ($treePath as $treeKey) {
            $node = $node->addItem($treeKey);                        
        }
        
        $node->setValue($item); 
    }
    
    /**
     * @return Gpf_Data_TreeNode
     */
    public function getRoot() {
        return $this->tree;
    }
}
?>
