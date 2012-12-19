<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Merchant.class.php 18071 2008-05-16 08:02:18Z aharsani $
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

class Gpf_Install_LicenseInfo extends Gpf_Object {
    private $license = '';
    private $productVariationName = '';
    private $applicationCode;
    
    public function setLicense($license) {
        $this->license = $license;
    }
    
    public function getLicense() {
        return $this->license;
    }
    
    public function setProductVariationName($productVariationName) {
        $this->productVariationName = $productVariationName;
    }

    public function getProductVariationName() {
        return $this->productVariationName;
    }
    
    public function setApplicationCode($applicationCode) {
        $this->applicationCode = trim($applicationCode);
    }
    
    public function isApplicationCodeValid() {
        if(strlen($this->applicationCode) <= 0) {
            return true;
        }
        return $this->applicationCode == Gpf_Application::getInstance()->getCode();      
    }
}
?>
