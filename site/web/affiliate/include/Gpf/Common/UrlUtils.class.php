<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Matej Kendera
 *   @since Version 1.0.0
 *   $Id: Campaign.class.php 18128 2008-05-20 16:37:37Z mfric $
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
class Gpf_Common_UrlUtils {

    public static function urlExists($url) {
        if (!self::isUrl($url)) {
            return false;
        }
        $handle = curl_init($url);
        if (false === $handle) {
            return false;
        }
        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_FAILONERROR, true);
        curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") );
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
        $connectable = curl_exec($handle);
        curl_close($handle);
        return $connectable;
    }
    
    public static function isUrl($url) {
        if (substr($url,0,5) == 'http:') {
            return true;
        }
        if (substr($url,0,6) == 'https:') {
            return true;
        }
        if (substr($url,0,4) == 'ftp:') {
            return true;
        }
        return false;
    }
}
?>
