<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework 
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ExistingExportsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Gpf_Csv_DownloadServerExportFilesGrid extends Gpf_Csv_ServerExportFilesGrid {
    
    function __construct() {
        parent::__construct();
    }
    
    protected function initViewColumns() {
    	parent::initViewColumns();
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), true);
    }
    
    protected function initDefaultView() {
        parent::initDefaultView();
        $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
    }
    
    /**
     * @service export_file read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service export_file export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }  
}
?>
