<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Exception.class.php 22920 2008-12-21 14:05:22Z rdohan $
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
class Gpf_Api_IncompatibleVersionException extends Exception {

    private $apiLink;

    public function __construct($url) {
        $this->apiLink = $url. '?C=Gpf_Api_DownloadAPI&M=download&FormRequest=Y&FormResponse=Y';
        parent::__construct('Version of API not corresponds to the Application version. Please <a href="' . $this->apiLink . '">download latest version of API</a>.', 0);
    }
    
    public function getApiDownloadLink() {
        return $this->apiLink;
    }

}
?>
