<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class DatabaseStatus_Main extends Gpf_Plugins_Handler {

    /**
     * @return DatabaseStatus_Main
     */
    public static function getHandlerInstance() {
        return new DatabaseStatus_Main();
    }

    public function addToMenu(Gpf_Menu $menu) {
        $menu->getItem('Tools')->addItem('Database-Status', $this->_('Database status'));
    }
}
?>
