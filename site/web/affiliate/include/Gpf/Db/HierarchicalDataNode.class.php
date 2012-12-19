<?php
/**
 *   @copyright Copyright (c) 2010 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class Gpf_Db_HierarchicalDataNode extends Gpf_DbEngine_Row {
	public function __construct($type) {
		parent::__construct();
		$date = new Gpf_DateTime();
		$this->setDateInserted($date->toDateTime());
		$this->setType($type);
	}

	function init() {
		$this->setTable(Gpf_Db_Table_HierarchicalDataNodes::getInstance());
		parent::init();
	}

	public function setId($value) {
		$this->set(Gpf_Db_Table_HierarchicalDataNodes::ID, $value);
	}
	
    public function setCode($value) {
        $this->set(Gpf_Db_Table_HierarchicalDataNodes::CODE, $value);
    }
	
	public function setLft($value) {
	    $this->set(Gpf_Db_Table_HierarchicalDataNodes::LFT, $value);
	}
	
    public function setName($value) {
        $this->set(Gpf_Db_Table_HierarchicalDataNodes::NAME, $value);
    }
	
    public function setRgt($value) {
        $this->set(Gpf_Db_Table_HierarchicalDataNodes::RGT, $value);
    }
	
	public function setDateInserted($dateInserted) {
		$this->set(Gpf_Db_Table_HierarchicalDataNodes::DATE_INSERTED, $dateInserted);
	}

	public function setType($type) {
		$this->set(Gpf_Db_Table_HierarchicalDataNodes::TYPE, $type);
	}
	
    public function setState($state) {
        $this->set(Gpf_Db_Table_HierarchicalDataNodes::STATE, $state);
    }
}

?>
