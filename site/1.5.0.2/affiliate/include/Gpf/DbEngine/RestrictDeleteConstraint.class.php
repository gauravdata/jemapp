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
class Gpf_DbEngine_RestrictDeleteConstraint extends Gpf_DbEngine_DeleteConstraint {
     
    public function execute(Gpf_DbEngine_Row $dbRow) {
        for ($i=0; $i<count($this->selfColumns); $i++) {
            $this->foreignDbRow->set($this->foreignColumns[$i],
                                     $dbRow->get($this->selfColumns[$i]));
        }
        $rowCollection = $this->foreignDbRow->loadCollection($this->foreignColumns);
        if ($rowCollection->getSize() > 0) {
            throw new Gpf_DbEngine_RestrictDeleteException($dbRow);
        }
    }

}
?>
