<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Versions.class.php 18552 2008-06-17 12:59:40Z aharsani $
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
class Gpf_Install_DoneScenario extends Gpf_Install_Scenario {
    
    public function __construct() {
        parent::__construct();
        $this->name = $this->_('Done');
    }
    
    protected function initSteps() {
        $this->addStep(new Gpf_Install_Finished());
    }

    public function silentDevelopmentInstall() {
    }
}
?>
