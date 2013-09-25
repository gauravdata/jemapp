<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Google.class.php 18112 2008-05-20 07:17:10Z mbebjak $
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
class Gpf_Gadget_Content extends Gpf_Gadget  {

    public function __construct() {
        parent::__construct();
        $this->setType('C');
    }

    protected function getTemplateName() {
        return "";
    }

    public function loadConfiguration($configurationContent) {
        if (strpos($this->getUrl(), Gpf_Gadget_Factory::CONTENT_PREFFIX) !== 0) {
            throw new Gpf_Exception("Not a Content gadget");
        }
        $this->setWidth(320);
        $this->setHeight(200);
    }
}
?>
