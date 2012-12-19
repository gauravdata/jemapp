<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ActiveViews.class.php 20866 2008-09-12 11:39:19Z mbebjak $
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
class Gpf_Db_Table_ActiveViews extends Gpf_DbEngine_Table {
    
    const ACCOUNTUSERID = 'accountuserid';
    const VIEWTYPE = 'viewtype';
    const ACTIVEVIEWID = 'activeviewid';

    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_activeviews');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ACCOUNTUSERID, 'char', 8);
        $this->createPrimaryColumn(self::VIEWTYPE, 'char', 100);
        $this->createColumn(self::ACTIVEVIEWID, 'char', 8);
    }

    public function saveActiveView($gridcode, $viewid) {
        $activeView = new Gpf_Db_ActiveView();
        $activeView->set(self::ACCOUNTUSERID, Gpf_Session::getAuthUser()->getAccountUserId());
        $activeView->set(self::VIEWTYPE, $gridcode);
         
        try {
            $activeView->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
        }

        $activeView->set(self::ACTIVEVIEWID, $viewid);
        $activeView->save();
    }
}

?>
