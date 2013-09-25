<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: LanguageAndDate.class.php 18081 2008-05-16 12:17:32Z mfric $
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
abstract class Pap_Features_SiteReplication_Driver_Base  {
    
    public abstract function shouldBeProcessed();

    public abstract function passthru();

    /**
     * @return Gpf_Net_Http_Response
     */
    public abstract function getContent();
    
    public abstract function getReplicatedSiteRealUrl();
    
    private function encodeSubArray(array $array, $keyName) {
        $string = "";
        foreach ($array as $key => $value) {
            $string .= $keyName . '[' . $key . ']='.urlencode($value).'&';
        }
        return rtrim($string, "&");
    }
    
    protected function encodeArray(array $array) {
        $string = "";
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $string .= $this->encodeSubArray($value, $key) . '&';
            } else {
                $string .= $key. '=' . urlencode($value) . '&';
            }
        }
        return rtrim($string, "&");
    }
}

?>
