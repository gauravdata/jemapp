<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 20081 2008-08-22 10:21:35Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Gpf_Lang_LanguagesGridRow extends Gpf_View_FormService {

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_Db_Language();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Language");
    }

    /**
     * @service language read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @service language add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }

    /**
     * @service language write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $result = parent::save($params);
        Gpf_Lang_CsvLanguage::rebuildLanguageCache($form->getFieldValue('Id'));
        return $result;
    }

    /**
     * @service language write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @service language delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
}

?>
