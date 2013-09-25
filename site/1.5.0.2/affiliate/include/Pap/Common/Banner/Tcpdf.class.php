<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Tcpdf.class.php 19083 2008-07-10 16:32:14Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

require_once 'tcpdf.php';

/**
 * @package GwtPhpFramework
 */
class Pap_Common_Banner_Tcpdf extends TCPDF {

	    /**
         * @param String $orientation page orientation. P (Portrait) or L (Landscape); (default = P)
         * @param String $unit User measure unit. pt: point, mm: millimeter, cm: centimeter, in: inch; (default = mm)
         * @param mixed $format The format used for pages. A1, A2, A3, A4, A5, A6, A7, A8...; see tcpdf manual (default = A4)
         * @param boolean $unicode TRUE means that the input text is unicode; (default = true)
         * @param String $encoding charset encoding; (default UTF-8)
         */
    function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = "UTF-8") {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding);
    }    
}
?>
