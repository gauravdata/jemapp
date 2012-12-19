<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Array.class.php 23542 2009-02-19 06:49:38Z rdohan $
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
class Gpf_Rpc_Array extends Gpf_Object implements Gpf_Rpc_Serializable, IteratorAggregate {

	private $array;

	function __construct(array $array = null){
		if($array === null){
			$this->array = array();
		}else{
			$this->array = $array;
		}
	}

	public function add($response) {
		if(is_scalar($response) || $response instanceof Gpf_Rpc_Serializable) {
			$this->array[] = $response;
			return;
		}
		throw new Gpf_Exception("Value of type " . gettype($response) . " is not scalar or Gpf_Rpc_Serializable");
	}

	public function toObject() {
		$array = array();
		foreach ($this->array as $response) {
			if($response instanceof Gpf_Rpc_Serializable) {
				$array[] = $response->toObject();
			} else {
				$array[] = $response;
			}
		}
		return $array;
	}

	public function toText() {
		return var_dump($this->array);
	}

	public function getCount() {
		return count($this->array);
	}

	public function get($index) {
		return $this->array[$index];
	}

	/**
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->array);
	}
}
?>
