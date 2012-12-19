<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
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
interface Gpf_Rpc_TableData {
	
    const SEARCH = 'search';
    
    /**
     * @service
     * @return Gpf_Data_RecordSet
     */
    public function getRow(Gpf_Rpc_Params $params);
    
    /**
     * @service
     * @return Gpf_Data_Table
     */
    public function getRows(Gpf_Rpc_Params $params);
    
}
?>
