<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package PostAffiliate
 *   @since Version 1.0.0
 *   $Id: Account.class.php 20153 2008-08-26 09:41:14Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliate
 */
class Pap_Lang extends Gpf_Lang_CsvHandler {
    
    public function getModule(Gpf_Io_File $file) {
        
        $fileName = str_replace(strtolower(Gpf_Paths::getInstance()->getTopPath()), '', 
        strtolower($file->getFileName()));
        
        if (strpos($fileName, 'merchant') !== false) {
            return 'merchant';
        }
        if (strpos($fileName, 'affiliate') !== false) {
            return 'affiliate';
        }
        
        return '';
    }
}
?>
