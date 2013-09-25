<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GridService.class.php 24534 2009-06-02 07:27:44Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *   @package GwtPhpFramework
 */

interface Gpf_View_Grid_HasRowFilter {
    /**
     * @param $row
     * @return DataRow or null
     */
    public function filterRow(Gpf_Data_Row $row); 
}

?>
