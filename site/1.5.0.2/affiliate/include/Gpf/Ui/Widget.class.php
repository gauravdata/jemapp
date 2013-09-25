<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Widget.class.php 21890 2008-10-27 11:07:08Z vzeman $
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
abstract class Gpf_Ui_Widget extends Gpf_Object {
    /**
     *
     * @var Gpf_Ui_Page
     */
    protected $page;

    protected $code = '';

    public function __construct(Gpf_Ui_Page $page = null) {
        $this->page = $page;
        $this->code = get_class($this);
    }
    
    abstract public function render();

    public function getCode() {
        return $this->code;
    }

}

?>
