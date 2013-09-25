<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UniqueConstraint.class.php 18645 2008-06-19 12:45:06Z mbebjak $
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
class Pap_Common_Campaign_ZeroOrOneDefaultCampaignConstraint extends Gpf_DbEngine_Row_UniqueConstraint implements Gpf_DbEngine_Row_Constraint {

    protected function doNoRowLoaded(Gpf_DbEngine_Row $row) {
    }
    
    public function validate(Gpf_DbEngine_Row $row) {
        
        if($row->get(Pap_Db_Table_Campaigns::IS_DEFAULT)==Gpf::YES){
            parent::validate($row);
        }
    }
    
    protected function loadRow(Gpf_DbEngine_Row $row) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->from->add($row->getTable()->name());
        $selectBuilder->select->addAll($row->getTable());
        foreach ($this->uniqueColumnNames as $columnName=>$value) {
            if ($value === false) {
                $selectBuilder->where->add($columnName,'=',$row->get($columnName));
                continue;
            }
            $selectBuilder->where->add($columnName,'=',$value);
        }
        return $selectBuilder->getOneRow();
    }
    
}

?>
