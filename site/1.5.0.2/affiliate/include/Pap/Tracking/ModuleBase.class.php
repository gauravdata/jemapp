<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
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

class Pap_Tracking_ModuleBase extends Gpf_ModuleBase {
    
    public function __construct() {
        parent::__construct('', 'install', 'T');
    }
    
    protected function getTitle() {
        return "";
    }
    
    protected function initCachedData() {
    }
    
    protected function initStyleSheets() {
    }
}
?>
