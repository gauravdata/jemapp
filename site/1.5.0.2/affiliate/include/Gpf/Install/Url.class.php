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

class Gpf_Install_Url extends Gpf_Object {
    private $url;
    
    public function __construct($url = '') {
        $this->url = $url;
        if(strlen($url) <= 0) {
            $this->url = Gpf_Paths::getInstance()->getFullBaseServerUrl(); 
        }    
        $this->stripProtocolAndWWW();
    }   
     
    public function toString() {
        return $this->url;    
    }
    
    private function stripProtocolAndWWW() {
        $this->url = rtrim($this->url, '/');
        $protocolPosition = strpos($this->url, '://');
        if ($protocolPosition !== false) {
            $this->url = substr($this->url, $protocolPosition + 3);
        }
        if (substr($this->url, 0, 4) == 'www.') {
            $this->url = substr($this->url, 4);
        }
    }
}
?>
