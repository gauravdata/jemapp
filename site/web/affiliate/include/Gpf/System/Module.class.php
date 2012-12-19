<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani, Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: ModuleBase.class.php 20018 2008-08-20 15:37:36Z aharsani $
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

class Gpf_System_Module extends Gpf_ModuleBase {

    public function __construct($panelName = 'install') {
        parent::__construct('', $panelName);
    }
    
    protected function initSession() {
        Gpf_System_Session::create($this, $sessionId);
    }
}
?>
