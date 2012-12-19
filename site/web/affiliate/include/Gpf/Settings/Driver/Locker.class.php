<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera, Juraj Simon
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Settings.class.php 18002 2008-05-13 18:31:39Z aharsani $
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
interface Gpf_Settings_Driver_Locker {
    
    /**
     * @return Gpf_Io_File
     */
    public function lock($fileName, $operation = LOCK_EX);
    
    /**
     * @param Gpf_Io_File
     */
    public function unlock(Gpf_Io_File $file);
}
?>
