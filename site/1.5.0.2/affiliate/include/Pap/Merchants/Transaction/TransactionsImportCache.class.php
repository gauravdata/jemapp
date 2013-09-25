<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Ivan Ivanco
 *   @since Version 1.0.0
 *   $Id: TransactionsForm.class.php 27999 2010-05-04 11:39:31Z mgalik $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Transaction_TransactionsImportCache extends Gpf_Object {

    private $cacheArray = array();

    public function __construct(){
        $this->cacheArray = Gpf_Session::getInstance()->getVar("cacheArray");
    }

    public function addToCache($value, $columnName){
        $this->cacheArray[$columnName][$value] = true;
        Gpf_Session::getInstance()->setVar('cacheArray',$this->cacheArray);
    }

    public function isInCache($value, $columnName){
        if(isset($this->cacheArray[$columnName][$value])){
            return true;
        }
        return false;
    }

    public function clearCache(){
        $this->cacheArray = array();
        Gpf_Session::getInstance()->setVar('cacheArray',$this->cacheArray);
    }
}

?>
