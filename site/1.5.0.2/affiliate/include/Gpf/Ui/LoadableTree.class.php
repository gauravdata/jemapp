<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
abstract class Gpf_Ui_LoadableTree extends Gpf_Object {
    
    /**
     * @param $itemId
     * @return Gpf_Data_RecordSet
     */
    public function loadTree(Gpf_Rpc_Params $params) {
        if (!$params->exists("itemId")) {
            throw new Gpf_Exception($this->_('Param itemId is mising'));
        }

        return $this->loadItems($params->get('itemId'));
    }

    /**
     * @param $itemId
     * @return Gpf_Data_RecordSet
     */
    protected abstract function loadItems($itemId);
}
?>
