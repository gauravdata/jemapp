<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: OrderByClause.class.php 23478 2009-02-12 14:48:17Z aharsani $
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
class Gpf_SqlBuilder_UnionBuilder extends Gpf_Object {

	private $selectBuilders = array();

	public function addSelect(Gpf_SqlBuilder_SelectBuilder $select) {
		$this->selectBuilders[] = $select;
	}

	/**
	 * @throws Gpf_Exception
	 * @return Gpf_SqlBuilder_SelectIterator
	 */
	public function getAllRowsIterator() {
		return new Gpf_SqlBuilder_SelectIterator($this->toString());
	}

	/**
	 * @throws Gpf_Exception
	 * @return String
	 */
	public function toString() {
		if ($this->isEmpty()) {
			throw new Gpf_Exception('Union is empty!');
		}
		$sql = $this->getSql($this->selectBuilders[0]);
		for ($i = 1; $i < count($this->selectBuilders); $i++) {
			$sql .= $this->getSql($this->selectBuilders[$i], ' UNION ');			
		}
		return $sql;
	}

	/**
	 * @return String
	 */
	protected function getSql(Gpf_SqlBuilder_SelectBuilder $select, $prefix = '') {
		return $prefix . $select->toString();
	}
	
	private function isEmpty() {
		return count($this->selectBuilders) === 0;
	}
}

?>
