<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani, Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: ModuleBase.class.php 21346 2008-09-30 14:56:46Z mbebjak $
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

class Gpf_ApiModule extends Gpf_ModuleBase {
    public function __construct() {
        parent::__construct('', '');
    }
    
    public function getDefaultTheme() {
        return '';
    }
}
