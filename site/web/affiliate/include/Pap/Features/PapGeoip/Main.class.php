<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class Pap_Features_PapGeoip_Main extends Gpf_Plugins_Handler {

    const COUNTRYCODE = 'countrycode';
    const IP = 'ip';
    const MAP_OVERLAY = 'Map-Overlay';
    
    private static $recognizedIPs = array();    

    /**
     * @return Pap_Features_PapGeoip_Main
     */
    public static function getHandlerInstance() {
        return new Pap_Features_PapGeoip_Main();
    }

    /**
     * Load Location of last 20 IP addresses of logins of selected affiliate
     *
     * @service online_user read
     * @return Gpf_Data_RecordSet
     */
    public function getAffiliateLogins(Gpf_Rpc_Params $params) {
        $sql = new Gpf_SqlBuilder_SelectBuilder();

        $sql->select->add('l.'.Gpf_Db_Table_LoginsHistory::IP);
        $sql->select->add('MAX(l.'.Gpf_Db_Table_LoginsHistory::LOGIN . ')', 'login');

        $sql->from->add(Gpf_Db_Table_LoginsHistory::getName(), 'l');
        $sql->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u', 'l.accountuserid=u.accountuserid');
        $sql->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'u.authid=au.authid');

        $sql->where->add(Gpf_Db_Table_AuthUsers::USERNAME, '=', $params->get('username'));
        $sql->where->add('l.'.Gpf_Db_Table_LoginsHistory::IP, '<>', '127.0.0.1');

        $sql->orderBy->add(Gpf_Db_Table_LoginsHistory::LOGIN, false);

        $sql->groupBy->add('l.'.Gpf_Db_Table_LoginsHistory::IP);

        $sql->limit->set(0, 20);

        $recordset = $sql->getAllRows();

        $recordset->addColumn('countryCode', '');
        $recordset->addColumn('countryName', '');
        $recordset->addColumn('city', '');
        $recordset->addColumn('latitude', '');
        $recordset->addColumn('longitude', '');
        $recordset->addColumn('postalCode', '');
        $recordset->addColumn('region', '');

        foreach ($recordset as $record) {
            $location = new GeoIp_Location();
            $location->setIpString($record->get('ip'));
            $location->load();

            $record->set('countryCode', $location->getCountryCode());
            $record->set('countryName', $location->getCountryName());
            $record->set('city', $location->getCity());
            $record->set('latitude', $location->getLatitude());
            $record->set('longitude', $location->getLongitude());
            $record->set('postalCode', $location->getPostalCode());
            $record->set('region', $location->getRegion());

            $record->set('login', Gpf_Common_DateUtils::getHumanRelativeTime(Gpf_Common_DateUtils::getClientTime(
            Gpf_Common_DateUtils::mysqlDateTime2Timestamp($record->get('login')))));
        }

        return $recordset;
    }

    public function initUserMailTemplateVariables(Gpf_Mail_Template $template) {
        /*
         * this method was removed in rev. 26588, it was added back only for cause of update
         * if user has active GeoIp plugin and try to update to higher version , installer thorws an error
         * Fatal error:  Call to undefined method GeoIp_Main::initUserMailTemplateVariables()
         */
    }

    public function getCountryCode(Gpf_Data_Record $context) {
        if ($context->get(Pap_Db_Table_RawImpressions::IP) == '127.0.0.1') {
            $context->set(Pap_Db_Table_Impressions::COUNTRYCODE, '');
            return;
        }
        if (in_array($context->get(Pap_Db_Table_RawImpressions::IP), self::$recognizedIPs)) {
            $context->set(Pap_Db_Table_Impressions::COUNTRYCODE, self::$recognizedIPs[array_search($context->get(Pap_Db_Table_RawImpressions::IP), self::$recognizedIPs)]);
            return;
        }
        try {
            $location = new GeoIp_Location();
            $location->setIpString($context->get(Pap_Db_Table_RawImpressions::IP));
            $location->load();
            $context->set(Pap_Db_Table_Impressions::COUNTRYCODE, $location->getCountryCode());
        } catch (Exception $e) {
        }
        self::$recognizedIPs[$context->get(Pap_Db_Table_RawImpressions::IP)] = $context->get(Pap_Db_Table_Impressions::COUNTRYCODE);
    }

    public function loadClicksFraudProtectionForm(Gpf_Rpc_Form $form) {
        $form->setField(Pap_Settings::GEOIP_CLICKS,
        Gpf_Settings::get(Pap_Settings::GEOIP_CLICKS));
        $form->setField(Pap_Settings::GEOIP_CLICKS_BLACKLIST,
        Gpf_Settings::get(Pap_Settings::GEOIP_CLICKS_BLACKLIST));
        $form->setField(Pap_Settings::GEOIP_CLICKS_BLACKLIST_ACTION,
        Gpf_Settings::get(Pap_Settings::GEOIP_CLICKS_BLACKLIST_ACTION));
    }

    public function loadSalesFraudProtectionForm(Gpf_Rpc_Form $form) {
        $form->setField(Pap_Settings::GEOIP_SALES,
        Gpf_Settings::get(Pap_Settings::GEOIP_SALES));
        $form->setField(Pap_Settings::GEOIP_SALES_BLACKLIST,
        Gpf_Settings::get(Pap_Settings::GEOIP_SALES_BLACKLIST));
        $form->setField(Pap_Settings::GEOIP_SALES_BLACKLIST_ACTION,
        Gpf_Settings::get(Pap_Settings::GEOIP_SALES_BLACKLIST_ACTION));
    }

    public function loadSignupsFraudProtectionForm(Gpf_Rpc_Form $form) {
        $form->setField(Pap_Settings::GEOIP_AFFILIATES,
        Gpf_Settings::get(Pap_Settings::GEOIP_AFFILIATES));
        $form->setField(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST,
        Gpf_Settings::get(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST));
        $form->setField(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST_ACTION,
        Gpf_Settings::get(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST_ACTION));
    }

    public function saveClicksFraudProtectionForm(Gpf_Rpc_Form $form) {
        Gpf_Settings::set(Pap_Settings::GEOIP_CLICKS,
        $form->getFieldValue(Pap_Settings::GEOIP_CLICKS));
        Gpf_Settings::set(Pap_Settings::GEOIP_CLICKS_BLACKLIST,
        $form->getFieldValue(Pap_Settings::GEOIP_CLICKS_BLACKLIST));
        Gpf_Settings::set(Pap_Settings::GEOIP_CLICKS_BLACKLIST_ACTION,
        $form->getFieldValue(Pap_Settings::GEOIP_CLICKS_BLACKLIST_ACTION));
    }

    public function saveSalesFraudProtectionForm(Gpf_Rpc_Form $form) {
        Gpf_Settings::set(Pap_Settings::GEOIP_SALES,
        $form->getFieldValue(Pap_Settings::GEOIP_SALES));
        Gpf_Settings::set(Pap_Settings::GEOIP_SALES_BLACKLIST,
        $form->getFieldValue(Pap_Settings::GEOIP_SALES_BLACKLIST));
        Gpf_Settings::set(Pap_Settings::GEOIP_SALES_BLACKLIST_ACTION,
        $form->getFieldValue(Pap_Settings::GEOIP_SALES_BLACKLIST_ACTION));
    }

    public function saveSignupsFraudProtectionForm(Gpf_Rpc_Form $form) {
        Gpf_Settings::set(Pap_Settings::GEOIP_AFFILIATES,
        $form->getFieldValue(Pap_Settings::GEOIP_AFFILIATES));
        Gpf_Settings::set(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST,
        $form->getFieldValue(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST));
        Gpf_Settings::set(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST_ACTION,
        $form->getFieldValue(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST_ACTION));
    }

    public function checkActionFraudProtection(Pap_Contexts_Action $context) {
        $checkIt = Gpf_Settings::get(Pap_Settings::GEOIP_SALES);
        if($checkIt != Gpf::YES) {
            $context->debug('    PapGeoip: Check for blacklisted countries is not turned on');
            return true;
        }

        $context->debug('    PapGeoip: Check if origin country of visitor related to action is not blacklisted started ');

        $blacklistedCountries = str_replace(' ', ',', trim(strtoupper(Gpf_Settings::get(Pap_Settings::GEOIP_SALES_BLACKLIST))));
        $checkAction = Gpf_Settings::get(Pap_Settings::GEOIP_SALES_BLACKLIST_ACTION);

        if($blacklistedCountries == '') {
            $context->debug("PapGeoip: No country is blacklisted.");
            return true;
        }
        if($checkAction != Pap_Tracking_Click_FraudProtection::ACTION_DECLINE && $checkAction != Pap_Tracking_Click_FraudProtection::ACTION_DONTSAVE) {
            $context->debug("PapGeoip: Action after check is not correct: '$checkAction'");
            return true;
        }

        $countryCode = strtoupper($context->getCountryCode());
        if (!strlen($countryCode)) {
            $context->debug("    PapGeoip: Origin country was not recognized.");
            return true;
        }

        $arrBlacklist = explode(',', $blacklistedCountries);

        if(in_array($countryCode, $arrBlacklist)) {
            if($checkAction == Pap_Tracking_Click_FraudProtection::ACTION_DONTSAVE) {
                $context->debug("    PapGeoip: STOPPING (setting setDoCommissionsSave(false), country $countryCode is blacklisted");
                $context->setDoCommissionsSave(false);
                $context->debug('      PapGeoip: Check if origin country of visitor related to action is not blacklisted endeded');
                return false;
            } else {
                $context->debug("  PapGeoip: DECLINING, country $countryCode is blacklisted");
                $this->declineAction($context, $this->_('PapGeoip: Country %s is blacklisted', $countryCode));
                $context->debug('      PapGeoip: Check if origin country of visitor related to action is not blacklisted endeded');
                return true;
            }
        } else {
            $context->debug("    Country is not blacklisted");
        }

        $context->debug('      PapGeoip: Check if origin country of visitor related to action is not blacklisted endeded');
        return true;

    }

    public function checkClickFraudProtection(Pap_Contexts_Click $context) {
        $checkIt = Gpf_Settings::get(Pap_Settings::GEOIP_CLICKS);
        if($checkIt != Gpf::YES) {
            $context->debug('    PapGeoip: Check country blacklist is not turned on for clicks');
            return;
        }

        $context->debug('    PapGeoip: Check country blacklist started');

        $blacklistedCountries = str_replace(' ', ',', trim(strtoupper(Gpf_Settings::get(Pap_Settings::GEOIP_CLICKS_BLACKLIST))));
        $checkAction = Gpf_Settings::get(Pap_Settings::GEOIP_CLICKS_BLACKLIST_ACTION);

        if($blacklistedCountries == '') {
            $context->debug("PapGeoip: No country is blacklisted.");
            return;
        }
        if($checkAction != Pap_Tracking_Click_FraudProtection::ACTION_DECLINE && $checkAction != Pap_Tracking_Click_FraudProtection::ACTION_DONTSAVE) {
            $context->debug("PapGeoip: Action after check is not correct: '$checkAction'");
            return;
        }

        $countryCode = strtoupper($context->getCountryCode());

        if (!strlen($countryCode)) {
            $context->debug("    PapGeoip: Origin country was not recognized for IP: " . $context->getVisit()->getIp());
            return;
        }

        $arrBlacklist = explode(',', $blacklistedCountries);

        if(in_array($countryCode, $arrBlacklist)) {
            if($checkAction == Pap_Tracking_Click_FraudProtection::ACTION_DONTSAVE) {
                $context->debug("    PapGeoip: STOPPING (setting setDoTrackerSave(false), country $countryCode is blacklisted");
                $context->setDoTrackerSave(false);
                $context->debug('      PapGeoip: Check country blacklist endeded');
                return;

            } else {
                $context->debug("  DECLINING, country $countryCode is blacklisted");

                $this->declineClick($context);

                $context->debug('      PapGeoip: Check country blacklist endeded');
                return;
            }
        } else {
            $context->debug("    Country $countryCode is not blacklisted");
        }

        $context->debug('      PapGeoip: Check country blacklist endeded');
    }


    public function checkSignupFraudProtection(Pap_Signup_SignupFormContext $context) {
        $checkIt = Gpf_Settings::get(Pap_Settings::GEOIP_AFFILIATES);
        if($checkIt != Gpf::YES) {
            return;
        }

        $blacklistedCountries = str_replace(' ', ',', trim(strtoupper(Gpf_Settings::get(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST))));
        $checkAction = Gpf_Settings::get(Pap_Settings::GEOIP_AFFILIATES_BLACKLIST_ACTION);

        if($blacklistedCountries == '') {
            return;
        }
        if($checkAction != Pap_Tracking_Click_FraudProtection::ACTION_DECLINE && $checkAction != Pap_Tracking_Click_FraudProtection::ACTION_DONTSAVE) {
            return;
        }

        $countryContext = new Gpf_Data_Record(
        array(Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_Impressions::COUNTRYCODE), array($context->getIp(), ''));
        $this->getCountryCode($countryContext);

        if (!strlen($countryContext->get(Pap_Db_Table_Impressions::COUNTRYCODE))) {
            return;
        }

        $arrBlacklist = explode(',', $blacklistedCountries);

        if(in_array(strtoupper($countryContext->get(Pap_Db_Table_Impressions::COUNTRYCODE)), $arrBlacklist)) {
            if($checkAction == Pap_Tracking_Click_FraudProtection::ACTION_DONTSAVE) {
                $context->getForm()->setErrorMessage($this->_("Not saved by geoip fraud protection - country code %s is blacklisted by merchant.", $countryContext->get(Pap_Db_Table_Impressions::COUNTRYCODE)));
                $context->setAllowSave(false);
            } else if ($checkAction == Pap_Tracking_Click_FraudProtection::ACTION_DECLINE) {
                $context->getRow()->setStatus(Gpf_Db_User::DECLINED);
            }
        }
    }

    public function addToMenu(Pap_Merchants_Menu $menu) {
        $menu->getItem('Reports')->addItem(self::MAP_OVERLAY, $this->_('Map Overlay'));
    }

    public function initImpressionsSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $select->groupBy->add(Pap_Db_Table_RawImpressions::IP);
    }

    public function initCountryCodeTransaction(Pap_Common_Transaction $transaction) {
        if (is_null($transaction->getCountryCode()) || $transaction->getCountryCode() === '') {
            $context = new Gpf_Data_Record(array(Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_Impressions::COUNTRYCODE), array($transaction->getIp(), ''));
            $this->getCountryCode($context);
            $transaction->setCountryCode($context->get(Pap_Db_Table_Impressions::COUNTRYCODE));
        }
    }

    public function getDefaultCountry(Gpf_Plugins_ValueContext $valueContext) {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!strlen($ip) || $ip == '127.0.0.1') {
            return;
        }

        try {
            $location = new GeoIp_Location();
            $location->setIpString($ip);
            $location->load();
            $valueContext->set($location->getCountryCode());
        } catch (Exception $e) {
        }
    }

    public function getCommissionType(Pap_Contexts_Action $context) {
        $context->debug("Begin recognizing country specific commission type");
        if(!strlen($context->getTransactionObject()->getCountryCode())) {
            $context->debug("STOPPING recognizing country specific commission type eneded: country code not recognized or empty");
            return;
        }
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_CommissionTypes::getInstance());
        $select->from->add(Pap_Db_Table_CommissionTypes::getName());
        $select->where->add(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, '=', $context->getCommissionTypeObject()->getId());
            
        $compoundContext = new Gpf_Data_Record(array(Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_Impressions::COUNTRYCODE), array($context->getVisit()->getIp(), ''));
        $this->getCountryCode($compoundContext);
        $countryCode = $compoundContext->get(Pap_Db_Table_Impressions::COUNTRYCODE);
        if (!strlen($countryCode)) {
            $context->debug("STOPPING recognizing country specific commission type eneded: country code not recognized or empty");
            return;
        }
            
        $select->where->add(Pap_Db_Table_CommissionTypes::COUNTRYCODES, 'LIKE', '%'.$countryCode.'%');
        try {
            $commType = new Pap_Db_CommissionType();
            $collection = $commType->loadCollectionFromRecordset($select->getAllRows());
            $context->setCommissionTypeObject($collection->get(0));
        } catch (Gpf_DbEngine_NoRowException $e) {
            $context->debug("Recognizing country specific commission type eneded - no country secpific commission defined");
            return;
        } catch (Gpf_DbEngine_TooManyRowsException $e) {
            $context->debug("STOPPING ecognizing country specific commission type eneded: more than one commision type is defined for country " . $context->getTransactionObject()->getCountryCode());
            return;
        } catch (Gpf_Exception $e) {
            $context->debug("STOPPING recognizing country specific commission type eneded: " . $e->getMessage());
        }
    }

    public function initCachedData(Gpf_ModuleBase $module) {
        Gpf_Rpc_CachedResponse::addById(Gpf_Country_Countries::getEncodedCountries(), 'countryData');
    }

    private function resolveCountryNames($countryArray) {
        $out = '';
        if (count($countryArray) == 0) {
            return $out;
        }
        foreach ($countryArray as $code) {
            $out .= $this->_localize(Gpf_Country_Countries::getCountryName($code)) . ', ';
        }
        return substr($out, 0, -2);
    }

    private function getAllUsedCountryListForType(Pap_Db_CommissionType $beforeSaveType) {
        $existingcountries = array();

        $type = new Pap_Db_CommissionType();
        $type->setParentCommissionTypeId($beforeSaveType->getParentCommissionTypeId());
        $types = $type->loadCollection(array(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID));

        foreach ($types as $type) {
            if ($type->getId() == $beforeSaveType->getId()) continue;
            $countryList = preg_split('/,/', $type->getCountryCodes());
            $existingcountries = array_merge($existingcountries, $countryList);
        }
        return $existingcountries;
    }

    public function commissionTypeBeforeSaveCheck(Pap_Db_CommissionType $beforeSaveType) {
        if ($beforeSaveType->getParentCommissionTypeId() == '' || $beforeSaveType->getParentCommissionTypeId() == null) {
            return;
        }
        $countries = preg_split('/,/', $beforeSaveType->getCountryCodes());

        $existingcountries = $this->getAllUsedCountryListForType($beforeSaveType);

        $intersection = array_intersect($existingcountries, $countries);
        if (count($intersection)>0) {
            throw new Gpf_Exception($this->_('One or more of countries ('.$this->resolveCountryNames($intersection).'), you selected, is already included in another country group in this commission type.'));
        }
    }

    public function processVisit(Pap_Db_Visit $visit) {
        $compoundContext = new Gpf_Data_Record(array(self::IP, self::COUNTRYCODE), array($visit->getIp(), ''));
        $this->getCountryCode($compoundContext);
        $countryCode = $compoundContext->get(self::COUNTRYCODE);
        if (!strlen($countryCode)) {
            Gpf_Log::debug("Preprocessing visit error: country code not recognized from IP " . $visit->getIp());
            return;
        }
        Gpf_Log::debug('Preprocessing visit: found country code ' . $countryCode . ', resolved from IP ' . $visit->getIp() . ', and setting it to visit');
        $visit->setCountryCode($countryCode);
    }
}
?>
