<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Menu.class.php 21654 2008-10-16 12:23:30Z mbebjak $
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
class Gpf_Data_MenuItem extends Gpf_Object implements Gpf_Rpc_Serializable {
    private $title;
    private $code;
    private $items = array();

    public function __construct($code, $title) {
        $this->title = $title;
        $this->code = $code;
    }
    
    /**
     * Get menu item title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    /**
     * Add new menu item to current item
     *
     * @param string $code
     * @param string $title
     * @return Gpf_Data_MenuItem
     */
    public function addItem($code, $title) {
        if(isset($this->items[$code])) {
            throw new Gpf_Exception($this->_('Menu entry with %s already exists', $code));
        }
        $menuEntry = new Gpf_Data_MenuItem($code, $title);
        $this->items[$code] = $menuEntry;
        return $menuEntry;
    }

    /**
     * Get menu item by code
     *
     * @param string $code
     * @return Gpf_Data_MenuItem
     */
    public function getItem($code) {
        if(!isset($this->items[$code])) {
            throw new Gpf_Exception($this->_('Menu entry with %s already exists', $code));
        }
        return $this->items[$code];
    }
    
    private function serializeToRecordset(Gpf_Data_RecordSet $response, $level) {
        foreach ($this->items as $menuItem) {
            $response->add(array($menuItem->getCode(), "" . $level, $menuItem->getTitle()));
            $menuItem->serializeToRecordset($response, $level+1);
        }
    }

    /**
     * @return Gpf_Data_MenuItem
     */
    public function getNoRpc() {
        return $this->menu;
        
    }
    
    public function toObject() {
        $response = new Gpf_Data_RecordSet();
        $response->setHeader(array('screenCode', 'menuLevel', 'caption'));
        $this->serializeToRecordset($response, 1);
        return $response->toObject();
    }

    public function toText() {
        throw new Gpf_Exception('Unsupported');    
    }
    
}

?>
