<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ActiveView.class.php 32431 2011-05-10 10:44:07Z mkendera $
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
class Gpf_Db_ActiveView extends Gpf_DbEngine_Row {
     
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_ActiveViews::getInstance());
        parent::init();
    }

    public function getActiveViewId() {
        return $this->get(Gpf_Db_Table_ActiveViews::ACTIVEVIEWID);
    }

    public function setActiveViewId($viewId) {
        $this->set(Gpf_Db_Table_ActiveViews::ACTIVEVIEWID, $viewId);
    }

    public function getViewType() {
        return $this->get(Gpf_Db_Table_ActiveViews::VIEWTYPE);
    }

    public function setViewType($viewType) {
        $this->set(Gpf_Db_Table_ActiveViews::VIEWTYPE, $viewType);
    }

    public function setAccountUserId($accountUserId) {
        $this->set(Gpf_Db_Table_ActiveViews::ACCOUNTUSERID, $accountUserId);
    }
}

?>
