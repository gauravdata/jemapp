<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: CurrencyForm.class.php 26315 2009-11-29 17:02:26Z vzeman $
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
class Pap_Merchants_Config_CurrencyForm extends Gpf_View_FormService {

 /**
     * @return Gpf_Db_Currency
     */
    protected function createDbRowObject() {
        return new Gpf_Db_Currency();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Currency");
    }

    /**
     * @param Gpf_DbEngine_Row $dbRow
     */
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_Row $dbRow) {
        $dbRow->set('isdefault', '0');
    }

    /**
     * @service currency write
     * @param $fields
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }

    /**
     * Load default currency
     *
     * @service currency read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $row = $this->createDbRowObject();
        $row->setIsDefault(Gpf_Db_Currency::DEFAULT_CURRENCY_VALUE);

        try {
            $row->loadFromData();
            $form->load($row);
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CurrencyForm.load', $form);
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Exception($this->getDbRowObjectName().$this->_(" does not exist"));
        }

        return $form;
    }

    /**
     * @service currency write
     *
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @service currency delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }

    /**
     * @service currency write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setIsDefault(Gpf_Db_Currency::DEFAULT_CURRENCY_VALUE);

        try {
        	$dbRow->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $dbRow->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
        }

        $oldDbRow = clone $dbRow;
        $form->fill($dbRow);

        foreach ($oldDbRow as $name => $oldValue) {
            if ($dbRow->get($name) != $oldValue) {
                $form->addField('changed', 'Y');
                break;
            }
        }

        try {
            $dbRow->save();
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CurrencyForm.save', $form);
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($dbRow);
        $form->setInfoMessage($this->getDbRowObjectName().$this->_(" saved"));
        return $form;
    }
}

?>
