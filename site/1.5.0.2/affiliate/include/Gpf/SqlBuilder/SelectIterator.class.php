<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: SelectBuilder.class.php 22258 2008-11-11 09:21:11Z vzeman $
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
final class Gpf_SqlBuilder_SelectIterator extends Gpf_Object implements Iterator {
	/**
	 * @var string
	 */
	private $select;
	/**
	 * @var Gpf_DbEngine_Driver_Mysql_Statement
	 */
	private $sth;
	/**
	 * @var Gpf_Data_Record
	 */
	private $currentRecord;
	private $position;
	
	public function __construct($select) {
    	$this->select = $select;
	}
    
	public function rewind() {
        $this->position = 0;
		$this->sth = $this->createDatabase()->execute($this->select);
        if ($this->sth->rowCount() < 1) {
            $this->sth = null;
            return;
        }
        $this->next();
    }
	
    /**
     *
     * @return Gpf_Data_Record
     */
    public function current() {
        return $this->currentRecord;
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        $this->position++;
    	if(false === ($row = $this->sth->fetchArray())) {
        	$this->sth = null;
        	return;
        }
        $this->currentRecord = new Gpf_Data_Record(array_keys($row), array_values($row)); 
    }

    public function valid() {
        return $this->sth !== null;
    }
}

?>
