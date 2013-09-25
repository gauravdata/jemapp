<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Sale.class.php 20226 2008-08-27 09:18:01Z mfric $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 *
 * all public properties in cookie are encoded
 */
class Pap_Tracking_Cookie_Base extends Gpf_Rpc_JsonObject {
    public function decode($string) {
        try {
            parent::decode($string);
        } catch (Gpf_Exception $e) {
            throw new Pap_Tracking_Exception("Invalid cookie format (".get_class($this)."). Cookie value: $string");
        }
    }
}
?>
