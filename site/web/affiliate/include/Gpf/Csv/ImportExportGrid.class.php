<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ExportGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Csv_ImportExportGrid extends Gpf_View_GridService implements Gpf_View_Grid_HasRowFilter {
    
    protected function initViewColumns() {
        $this->addViewColumn("name", $this->_("Name"), true);
        $this->addViewColumn("description", $this->_("Export data"), true);
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn("code");
        $this->addDataColumn("name");
        $this->addDataColumn("description");
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn("name", '', 'A');
        $this->addDefaultViewColumn("description", '', 'N');
    }
    
    protected function buildFrom(){
    	$this->_selectBuilder->from->add(Gpf_Db_Table_ImportExports::getName());
    }
    
    /**
     * @service import_export read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    public function filterRow(Gpf_Data_Row $row) {
        if ($row->get('name') != null) {
            $row->set('name', $this->_localize($row->get('name')));
        }
        if ($row->get('description') != null) {
            $row->set('description', $this->_localize($row->get('description')));
        }
        return $row;
    }
    
    /**
     * @service import_export export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }  
}
?>
