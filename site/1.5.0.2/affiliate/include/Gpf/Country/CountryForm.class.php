<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
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
class Gpf_Country_CountryForm extends Gpf_View_FormService {

    /**
     * @return Gpf_Db_Country
     */
    protected function createDbRowObject() {
        return new Gpf_Db_Country();
    }

    /**
     * @service country write
     *
     * @param $id
     * @return Gpf_Rpc_Action
     */
    public function setDefaultCountry(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_("Error changing default country"));
        $action->setInfoMessage($this->_("Default country changed"));

        try {
            $countryCode = $action->getParam('id');
            Gpf_Settings::set(Gpf_Settings_Gpf::DEFAULT_COUNTRY, $countryCode);
            $country = new Gpf_Db_Country();
            $country->setCountryCode($countryCode);
            $country->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
            $country->loadFromData(array(Gpf_Db_Table_Countries::COUNTRY_CODE, Gpf_Db_Table_Countries::ACCOUNTID));
            if ($country->getStatus() != Gpf_Db_Country::STATUS_ENABLED) {
                $country->setStatus(Gpf_Db_Country::STATUS_ENABLED);
                $country->save();
            }
            $action->addOk();
        } catch (Exception $e) {
            $action->addError();
        }

        return $action;
    }

    /**
     * @service country read
     * @anonym
     * @return Gpf_Rpc_Data
     */
    public function loadDefaultCountry(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $context = new Gpf_Plugins_ValueContext(Gpf_Settings::get(Gpf_Settings_Gpf::DEFAULT_COUNTRY));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Countries.getDefaultCountry', $context);

        $data->setValue('default', $context->get());
        return $data;
    }

    /**
     * @service country write
     *
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
    	return parent::saveFields($params);
    }

    /**
     * @service country read
     * @anonym
     * @return Gpf_Data_RecordSet
     */
    public function loadCountries() {
    	$select = new Gpf_SqlBuilder_SelectBuilder();
    	$select->select->add(Gpf_Db_Table_Countries::COUNTRY_CODE, 'id');
        $select->select->add(Gpf_Db_Table_Countries::COUNTRY, 'name');
    	$select->from->add(Gpf_Db_Table_Countries::getName());
    	$select->where->add(Gpf_Db_Table_Countries::ACCOUNTID, '=', Gpf_Application::getInstance()->getAccountId());
        $select->where->add(Gpf_Db_Table_Countries::STATUS, '=', Gpf_Db_Country::STATUS_ENABLED);
        $select->orderBy->add(Gpf_Db_Table_Countries::COUNTRY);

        return $select->getAllRows();
    }
}
?>
