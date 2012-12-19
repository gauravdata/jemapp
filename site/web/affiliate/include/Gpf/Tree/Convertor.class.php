<?php
/**
 *   @copyright Copyright (c) 2010 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Juraj Simon
 *   @since Version 1.0.0
 *   $Id: ModuleBase.class.php 20018 2008-08-20 15:37:36Z aharsani $
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

class Gpf_Tree_Convertor extends Gpf_Object {
    
    //mptt - Modified Preorder Tree Traversal
    /**
     *
     * @var Gpf_Data_RecordSet
     */
    private $mpttArray = null;
    
    private $jsonString = null;
    
    public function setJSONString($jsonString) {
        $this->jsonString = $jsonString;
    }
    
    public function setMPTTArray(Gpf_Data_RecordSet $mpttArray) {
        $this->mpttArray = $mpttArray;
    }
    
    private function parseTree($tree, $lft) {
        foreach($tree as $branch) {
            if (count($branch->items) > 0) {
                $rgt = $this->parseTree($branch->items, $lft+1) + 1;
            } else {
                $rgt = $lft + 1;
            }
            $branchItem = array("lft" => $lft, "rgt" => $rgt);
            foreach ($branch->data as $key => $item) {
                $branchItem[$key] = $item;
            }
            $this->mpttArray[] = $branchItem;
            $lft = $rgt + 1;
        }
        return $rgt;
    }
    
    public function getMPTTArray() {
        if ($this->jsonString == null) {
            throw new Gpf_Exception('No JSON string was set! There are no data to create mptt tree structure');
        }
        $json = new Gpf_Rpc_Json();
        $tree = $json->decode($this->jsonString);
        if (count($tree) > 0) {
            $this->parseTree($tree, 2);
        }
        $this->mpttArray[] = array("lft" => 1, "rgt" => count($this->mpttArray)*2+2, 'name' => 'root', 'state'=>'', 'code'=>'0');
        
        return $this->mpttArray;
    }
    
    private function createObject(Gpf_Data_Row $row, $headers) {
        $object = new stdClass();
        $object->data = new stdClass();
        $object->data->code = $row->get('code');
        $object->items = array();
        foreach ($headers as $header) {
            $object->data->$header = $row->get($header);
        }
        return $object;
    }
    
    private function cloneArray($array) {
        $newArray = array();
        foreach ($array as $key => $item) {
            $newArray[$key] = $item;
        }
        return $newArray;
    }
    
    private function getHeaders(Gpf_Data_RecordHeader $header) {
        $headers = $header->toArray();
        $output = array();
        foreach ($headers as $item) {
            if ($item != 'code' && $item != 'depth') {
               $output[] = $item;
            }
        }
        return $output;
    }
    
    private function checkPrevioursStatesAreCorrect($oldStates, $states) {
        if (count($oldStates)==0 || $states==null) {
            return true;
        }
        $states[] = '';
        foreach ($oldStates as $state) {
            if (!in_array($state, $states)) {
                return false;
            }
        }
        return true;
    }
    
    public function getJSONString($withRoot = true, $states = null) {
        if ($this->mpttArray == null) {
            throw new Gpf_Exception('No tree Records were set! There are no data to create JSON string');
        }
        
        $levels = array();
        $stack = 0;
        $oldDepth = -1;
        
        $headers = $this->getHeaders($this->mpttArray->getHeader());
        $oldStates = array();
        foreach ($this->mpttArray as $row) {
            $depth = $row->get('depth');
            // code, depth   //name,visible
            if ($depth == $oldDepth) {
                //no level change
                if (count($oldStates)>0) {
                    array_pop($oldStates);
                }
                array_push($oldStates,$row->get('state'));
                if ($this->checkPrevioursStatesAreCorrect($oldStates, $states)) {
                    $levels[$depth][] = $this->createObject($row, $headers);
                }
            }
            if ($depth > $oldDepth) {
                //level raised
                array_push($oldStates,$row->get('state'));
                if ($this->checkPrevioursStatesAreCorrect($oldStates, $states)) {
                    $levels[$depth][] = $this->createObject($row, $headers);
                }
                $stack = $depth;
            }
            if ($depth < $oldDepth) {
                //level drop
                while ($stack > $depth) {
                    $stack--;
                    array_pop($oldStates);
                    if ($this->checkPrevioursStatesAreCorrect($oldStates, $states)) {
                        $array = $this->cloneArray($levels[$stack+1]);
                        $levels[$stack][count($levels[$stack])-1]->items = $array;
                        unset($levels[$stack+1]);
                    }
                }
                array_pop($oldStates);
                array_push($oldStates,$row->get('state'));
                if ($this->checkPrevioursStatesAreCorrect($oldStates, $states)) {
                    $levels[$depth][] = $this->createObject($row, $headers);
                }
            }
            $oldDepth = $depth;
        }
        while ($stack > 0) {
            $stack--;

            array_pop($oldStates);
            if ($this->checkPrevioursStatesAreCorrect($oldStates, $states)) {
                if (array_key_exists($stack+1, $levels)) {
                    $levels[$stack][count($levels[$stack])-1]->items = $this->cloneArray($levels[$stack+1]);
                    unset($levels[$stack+1]);
                }
            }
        }

        $json = new Gpf_Rpc_Json();
        if ($withRoot) {
            return $json->encode($levels[0]);
        }
        if (array_key_exists(0, $levels)) {
            return $json->encode($levels[0][0]->items);
        }
        return 'null';
    }
}
?>
