<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.6
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
class Gpf_Newsletter_NewsletterForm extends Gpf_View_FormService {

	/**
	 * @return Gpf_Db_Newsletter
	 */
	protected function createDbRowObject() {
		return new Gpf_Db_Newsletter();
	}

	/**
	 * @return string
	 */
	protected function getDbRowObjectName() {
		return $this->_('Newsletter');
	}

	/**
	 * @service newsletter read
	 *
	 * @param $fields
	 * @return Gpf_Rpc_Serializable
	 */
	public function load(Gpf_Rpc_Params $params) {
		return parent::load($params);
	}

	/**
	 * @service newsletter write
	 * @param $fields
	 */
	public function save(Gpf_Rpc_Params $params) {
	    return parent::save($params);
	}

    /**
     * @service newsletter add
     * @param $fields
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }


    /**
     * @service newsletter write
     *
     * @param $fields
     */
    public function saveFields(Gpf_Rpc_Params $params) {
    	return parent::saveFields($params);
    }

    /**
     *
     * @service newsletter delete
     * @param $ids
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
    	return parent::deleteRows($params);
    }

}
?>
