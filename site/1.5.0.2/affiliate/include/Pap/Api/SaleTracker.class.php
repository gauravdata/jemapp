<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 * @deprecated backward compatibility class. use Pap_Api_Tracker instead
 */
class Pap_Api_SaleTracker extends Pap_Api_Tracker {

    /**
     * @param string $saleScriptUrl Url to sale.php script
     */
    public function __construct($saleScriptUrl, $debug = false) {
        $session = new Gpf_Api_Session(str_replace('sale.php', 'server.php', $saleScriptUrl));
        if ($debug) {
            $session->setDebug(true);
        }
        parent::__construct($session);
    }

    /**
     * sets value of the cookie to be used
     *
     * @param string $value
     */
    public function setCookieValue($value) {
        $this->setVisitorId($value);
    }

    /**
     * Registers all created sales
     */
    public function register() {
        $this->track();
    }
}
?>
