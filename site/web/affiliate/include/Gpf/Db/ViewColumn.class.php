<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ViewColumn.class.php 21752 2008-10-20 14:22:33Z mbebjak $
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
class Gpf_Db_ViewColumn extends Gpf_DbEngine_Row {
     
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_ViewColumns::getInstance());
        parent::init();
    }
    
    public function setViewId($viewId) {
        $this->set(Gpf_Db_Table_ViewColumns::VIEW_ID, $viewId);
    }
    
    public function getSorted() {
        return $this->get(Gpf_Db_Table_ViewColumns::SORTED);
    }
    
    public function getName() {
        return $this->get(Gpf_Db_Table_ViewColumns::NAME);
    }
}

?>
