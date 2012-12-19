<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Table.class.php 20488 2008-09-02 12:52:19Z mbebjak $
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
class Gpf_DbEngine_RestrictDeleteException extends Gpf_Exception {
    public function __construct(Gpf_DbEngine_Row $dbRow) {
        parent::__construct("Row " . get_class($dbRow) . " can not be deleted because it is referenced by other rows");             
    }
}
?>
