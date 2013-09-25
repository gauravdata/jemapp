<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
class Gpf_Db_Gadget extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Gpf_Db_Table_Gadgets::getInstance());
        parent::init();
    }
    
    public function delete() {
        parent::delete();
        Gpf_Db_Table_GadgetProperties::deleteAll($this->getId());
    }
    
    public function getId() {
        return $this->get(Gpf_Db_Table_Gadgets::ID);
    }
    
    public function getType() {
        return $this->get(Gpf_Db_Table_Gadgets::TYPE);
    }
    
    public function setType($type) {
        $this->set(Gpf_Db_Table_Gadgets::TYPE, $type);
    }
    
    public function getName() {
        return $this->get(Gpf_Db_Table_Gadgets::NAME);
    }
    
    public function setName($name) {
        $this->set(Gpf_Db_Table_Gadgets::NAME, $name);
    }
    
    public function getUrl() {
        return $this->get(Gpf_Db_Table_Gadgets::URL);
    }
    
    public function setUrl($url) {
        $this->set(Gpf_Db_Table_Gadgets::URL, $url);
    }
    
    public function getHeight() {
        return $this->get(Gpf_Db_Table_Gadgets::HEIGHT);
    }
    
    public function getWidth() {
        return $this->get(Gpf_Db_Table_Gadgets::WIDTH);
    }
    
    public function setAccountUserId($accountUserId) {
        $this->set(Gpf_Db_Table_Gadgets::ACCOUNTUSERID, $accountUserId);
    }
    
    public function setHeight($height) {
        $this->set(Gpf_Db_Table_Gadgets::HEIGHT, $height);
    }
    
    public function setWidth($width) {
        $this->set(Gpf_Db_Table_Gadgets::WIDTH, $width);
    }
    
    public function setPositionType($positionType) {
        $this->set(Gpf_Db_Table_Gadgets::POSITION_TYPE, $positionType);
    }
    
    public function setPositionLeft($positionLeft) {
        $this->set(Gpf_Db_Table_Gadgets::POSITION_LEFT, $positionLeft);
    }
    
    public function setPositionTop($positionTop) {
        $this->set(Gpf_Db_Table_Gadgets::POSITION_TOP, $positionTop);
    }
    
    public function getAutorefreshTime() {
        return $this->get(Gpf_Db_Table_Gadgets::AUTOREFRESH_TIME);
    }
    
    public function setAutorefreshTime($miliseconds) {
        $this->set(Gpf_Db_Table_Gadgets::AUTOREFRESH_TIME, $miliseconds);
    }
}

?>
