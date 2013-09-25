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
 * @package PostAffiliatePro
 */

class Pap_Install_RecommendedSettings extends Gpf_Install_RecommendedSettings {
    private $settings = array();

    public function __construct() {
        $this->addPhpIniCheck($this->_('Safe Mode'), 0, 'safe_mode');
        $this->addPhpIniCheck($this->_('Display Errors'), 0, 'display_errors');
        $this->addPhpIniCheck($this->_('File Uploads'), 1, 'file_uploads');
        $this->addPhpIniCheck($this->_('Register Globals'), 0, 'register_globals');
        $this->addPhpIniCheck($this->_('Session Auto Start'), 0, 'session.auto_start');
    }
}
?>
