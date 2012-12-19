<?php
/**
 *   @copyright Copyright (c) 2010 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Juraj Simon
 *   @since Version 1.0.0
 *   $Id:
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

class Gpf_Tree_Base extends Gpf_Object {
    private $type = null;
    private $records = null;
    private $JSONString = null;
    private $convertor;
    private $withRoot;
    private $states;

    public function __construct($type, $withRoot,$onlyWithStates = null) {
        $this->type = $type;
        $this->withRoot = $withRoot;
        $this->states = $onlyWithStates;
        $this->convertor = new Gpf_Tree_Convertor();
    }

    public function laod() {
        $this->records = $this->loadAsRecordset();
        $this->convertor->setMPTTArray($this->records);
        $this->JSONString = $this->convertor->getJSONString($this->withRoot, $this->states);
    }
    
    public function getMaxCode() {
        //TODO make test
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('max('.Gpf_Db_Table_HierarchicalDataNodes::CODE.') as maxcode');
        $select->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName());
        $select->where->add(Gpf_Db_Table_HierarchicalDataNodes::TYPE,'=',$this->type);
        $row = $select->getOneRow();
        return $row->get('maxcode');
    }


    private function typeExists($type) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('count('.Gpf_Db_Table_HierarchicalDataNodes::ID.') as cnt');
        $select->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName());
        $select->where->add(Gpf_Db_Table_HierarchicalDataNodes::TYPE,'=',$type);
        $row = $select->getOneRow();
        if ($row->get('cnt') > 0) {
            return true;
        }
        return false;
    }

    private function checkIntegrity() {
        $newTypeExist = $this->typeExists($this->type . '_');
        if ($newTypeExist) {
            $this->deleteObsolateNodes($this->type);
            $this->activateNewNodes();
        }
    }

    /**
     * @return Gpf_SqlBuilder_SelectIterator
     */
    private function loadAsRecordset() {
        $this->checkIntegrity();
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_HierarchicalDataNodes::CODE, 'code', 'n');
        $select->select->add(Gpf_Db_Table_HierarchicalDataNodes::NAME, Gpf_Db_Table_HierarchicalDataNodes::NAME,'n');
        $select->select->add(Gpf_Db_Table_HierarchicalDataNodes::STATE, Gpf_Db_Table_HierarchicalDataNodes::STATE,'n');
        $select->select->add('(COUNT(p.'.Gpf_Db_Table_HierarchicalDataNodes::NAME.') - 1)', 'depth');
        $select->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName(),'n');
        $select->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName(),'p');
        $select->where->add('n.' . Gpf_Db_Table_HierarchicalDataNodes::LFT, 'BETWEEN', 'p.'.Gpf_Db_Table_HierarchicalDataNodes::LFT.' AND p.'.Gpf_Db_Table_HierarchicalDataNodes::RGT, 'AND', false);
        $select->where->add('n.'.Gpf_Db_Table_HierarchicalDataNodes::TYPE,'=',$this->type);
        $select->where->add('p.'.Gpf_Db_Table_HierarchicalDataNodes::TYPE,'=',$this->type);
        $select->groupBy->add('n.' . Gpf_Db_Table_HierarchicalDataNodes::NAME);
        $select->groupBy->add('n.' . Gpf_Db_Table_HierarchicalDataNodes::LFT);
        $select->orderBy->add('n.'.Gpf_Db_Table_HierarchicalDataNodes::LFT);
        return $select->getAllRows();
    }



    public function toJSON() {
        if ($this->JSONString == null) {
            throw new Gpf_Exception('Tree not loaded! Try to call ->load() first!');
        }
        return $this->JSONString;
    }

    private function deleteObsolateNodes($type) {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName());
        $delete->where->add(Gpf_Db_Table_HierarchicalDataNodes::TYPE, '=', $type);

        $delete->execute();
    }

    private function activateNewNodes() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName());
        $update->set->add(Gpf_Db_Table_HierarchicalDataNodes::TYPE, $this->type);
        $update->where->add(Gpf_Db_Table_HierarchicalDataNodes::TYPE, '=', $this->type.'_');
        $update->execute();
    }

    protected function insertNode($item) {
        $hItem = new Gpf_Db_HierarchicalDataNode($this->type);
        $hItem->setCode($item['code']);
        $hItem->setType($this->type . '_');
        $hItem->setLft($item['lft']);
        $hItem->setRgt($item['rgt']);
        $hItem->setState($item['state']);
        $hItem->setName($item['name']);
        $hItem->insert();
    }
    
    public function getPath($code) {
        //TODO make test
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('p.' . Gpf_Db_Table_HierarchicalDataNodes::NAME);
        $select->select->add('p.'.Gpf_Db_Table_HierarchicalDataNodes::CODE);
        $select->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName(), 'n');
        $select->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName(), 'p');
        $select->where->add('n.'.Gpf_Db_Table_HierarchicalDataNodes::LFT , 'BETWEEN', 'p.'.Gpf_Db_Table_HierarchicalDataNodes::LFT.' AND p.'.Gpf_Db_Table_HierarchicalDataNodes::RGT, 'AND', false);
        $select->where->add('n.'.Gpf_Db_Table_HierarchicalDataNodes::TYPE,'=',$this->type);
        $select->where->add('p.'.Gpf_Db_Table_HierarchicalDataNodes::TYPE,'=',$this->type);
        $select->where->add('n.'.Gpf_Db_Table_HierarchicalDataNodes::CODE,'=', $code);
        $select->orderBy->add('p.'.Gpf_Db_Table_HierarchicalDataNodes::LFT);
        return $select->getAllRows();
    }
    
    public function getBreadcrumb($code, $separator) {
        //TODO make test
        $path = $this->getPath($code);
        $output = '';
        foreach ($path as $node) {
            if (!$this->withRoot && $node->get('code')==0) {
                continue;
            }
            $output .= $node->get('name') . $separator;
        }
        return substr ($output,0,-(strlen($separator)));
    }

    public function save($JSONString) {
        $this->convertor->setJSONString($JSONString);
        $items = $this->convertor->getMPTTArray($this->withRoot);
        $activeIds = array();
        try {
            foreach ($items as $item) {
                 $activeIds[] = $item['code'];
                 $this->insertNode($item);
            }
        } catch (Gpf_Exception $e) {
            $this->deleteObsolateNodes($this->type.'_');
            return;
        }
        $this->deleteObsolateNodes($this->type);
        $this->activateNewNodes();
        $this->JSONString = $JSONString;
        return $activeIds;
    }
}
?>
