<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: PhpDbFile.class.php 19841 2008-08-14 13:28:36Z aharsani $
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
class Gpf_Install_PhpDbFile extends Gpf_Install_DbFile {

    public function __construct($fileName, $version, $application) {
        parent::__construct($fileName, $version, $application);
    }

    private function executePhp($className) {
        include_once($this->file->getFileName());

        if(!class_exists($className, false)) {
            throw new Gpf_Exception($className . " " . $this->_("doesnt exist's"));
        }
        $dbFile = Gpf::newObj($className);
        $dbFile->execute();
    }
    
    protected function executeFile() {
        $this->executePhp($this->application . '_update_'
            . str_replace('.', '_', $this->version));
    }
}
