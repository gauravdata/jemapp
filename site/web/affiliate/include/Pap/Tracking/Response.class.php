<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Response.class.php 24148 2009-04-22 06:37:53Z mbebjak $
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
class Pap_Tracking_Response extends Gpf_Object {
    
    public function finishTracking() { 
        echo "_tracker.trackNext();";
    }
    
    public function outputEmptyImage() {
		Gpf_Http::setHeader('Content-Type', 'image/gif');
		$pixFile = new Gpf_Io_File('pix.gif');
		$pixFile->output();
    }
    
    public function redirectTo($url) {
        Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::LOCATION, $url, 301);
        //echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$url\">";
    }
}
?>
