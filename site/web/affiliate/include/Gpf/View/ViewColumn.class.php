<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ViewColumn.class.php 25837 2009-10-27 10:27:14Z vzeman $
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
class Gpf_View_ViewColumn extends Gpf_View_Column {

    const TYPE_STRING = 'string';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_COUNTRYCODE = 'countrycode';
    const TYPE_NUMBER = 'number';
    const TYPE_CURRENCY = 'currency';
    const TYPE_IP = 'ip';
    const TYPE_PERCENTAGE = 'percentage';

    protected $sortable;
    protected $visible;
    protected $width;
    protected $sorted;
    protected $type;

    function __construct($id, $name, $sortable=false, $width="", $sorted="N", $visible=false, $type = Gpf_View_ViewColumn::TYPE_STRING) {
        parent::__construct($id, $name);
        $this->sortable = $sortable;
        $this->width = $width;
        $this->sorted = $sorted;
        $this->type = $type;
    }

    public function getSortable() {
        return $this->sortable;
    }

    public function getResultArray() {
        return array($this->id, $this->name, ($this->visible) ? Gpf::YES : Gpf::NO, ($this->sortable) ? Gpf::YES : Gpf::NO, $this->width, $this->sorted, $this->type);
    }

    public static function getMetaResultArray() {
        return array('id', 'name', 'visible', 'sortable', 'width', 'sorted', 'type');
    }
}
?>
