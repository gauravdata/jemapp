<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_PapGeoip_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'PapGeoip';
        $this->name = $this->_('Post Affiliate Pro GeoIp');
        $this->description = $this->_('Feature is active only if basic GeoIp and Google Maps plugins are activated. Feature will compute from IP addresses country codes and assign transactions to countries. You will be able to filter sales/clicks/actions by countries, compare statistic data by countries and blacklist some countries in fraud protection settings of sales/clicks/affiliate signups.');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addRequirement('PapCore', '4.2.7.0');
        $this->addRequirement('GeoIp', '1.0.0');
        $this->addRequirement('GoogleMaps', '1.0.0');

        $this->addImplementation('Tracker.request.getCountryCode', 'Pap_Features_PapGeoip_Main', 'getCountryCode');
        $this->addImplementation('Tracker.ImpressionProcessor.getAllImpressions', 'Pap_Features_PapGeoip_Main', 'initImpressionsSelect');
        $this->addImplementation('FraudProtectionSignupsForm.load', 'Pap_Features_PapGeoip_Main', 'loadSignupsFraudProtectionForm');
        $this->addImplementation('FraudProtectionSignupsForm.save', 'Pap_Features_PapGeoip_Main', 'saveSignupsFraudProtectionForm');

        $this->addImplementation('FraudProtectionClicksForm.load', 'Pap_Features_PapGeoip_Main', 'loadClicksFraudProtectionForm');
        $this->addImplementation('FraudProtectionClicksForm.save', 'Pap_Features_PapGeoip_Main', 'saveClicksFraudProtectionForm');

        $this->addImplementation('FraudProtectionSalesForm.load', 'Pap_Features_PapGeoip_Main', 'loadSalesFraudProtectionForm');
        $this->addImplementation('FraudProtectionSalesForm.save', 'Pap_Features_PapGeoip_Main', 'saveSalesFraudProtectionForm');

        $this->addImplementation('FraudProtection.Action.check', 'Pap_Features_PapGeoip_Main', 'checkActionFraudProtection');
        $this->addImplementation('FraudProtection.Click.check', 'Pap_Features_PapGeoip_Main', 'checkClickFraudProtection');
        $this->addImplementation('FraudProtection.Signup.check', 'Pap_Features_PapGeoip_Main', 'checkSignupFraudProtection');

        $this->addImplementation('PostAffiliate.merchant.menu', 'Pap_Features_PapGeoip_Main', 'addToMenu');
        $this->addImplementation('PostAffiliate.Transaction.beforeSave', 'Pap_Features_PapGeoip_Main', 'initCountryCodeTransaction');

        $this->addImplementation('PostAffiliate.Countries.getDefaultCountry', 'Pap_Features_PapGeoip_Main', 'getDefaultCountry');

        $this->addImplementation('PostAffiliate.UserMail.initTemplateVariables', 'Pap_Features_PapGeoip_UserMailExtension', 'initUserMailTemplateVariables');
        $this->addImplementation('PostAffiliate.UserMail.setVariableValues', 'Pap_Features_PapGeoip_UserMailExtension', 'setUserMailTemplateVariables');

        $this->addImplementation('GpfModuleBase.initCachedData', 'Pap_Features_PapGeoip_Main', 'initCachedData');
        $this->addImplementation('PostAffiliate.CommissionType.beforeSaveCheck', 'Pap_Features_PapGeoip_Main', 'commissionTypeBeforeSaveCheck');
        $this->addImplementation('PostAffiliate.PapTrackingVisitProcessor.processVisit', 'Pap_Features_PapGeoip_Main', 'processVisit');
    }

    public function onDeactivate() {
        $this->removeMapOverlayFromAffiliatePanel();
    }

    private function removeMapOverlayFromAffiliatePanel() {
        $affMenu = Gpf_Settings::get(Pap_Settings::AFFILIATE_MENU);
        $json = new Gpf_Rpc_Json();
        $affMenuDecoded = $json->decode($affMenu);
        $affMenuDecoded = $this->removeMenuItem($affMenuDecoded, $this->getMapOverlayScreenId());
        Gpf_Settings::set(Pap_Settings::AFFILIATE_MENU,$json->encode($affMenuDecoded));
    }

    protected function removeMenuItem($items, $code) {
        foreach ($items as $key => $item) {
            if ($item->data->code == $code) {
                unset($items[$key]);
                $items = array_values($items);
            }
            if (isset($item->items) && count($item->items) > 0) {
                $item->items = $this->removeMenuItem($item->items, $code);
            }
        }
        return $items;
    }

    private function getMapOverlayScreenId() {
        $affiliateScreen = new Pap_Db_AffiliateScreen();
        $affiliateScreen->setCode(Pap_Features_PapGeoip_Main::MAP_OVERLAY);
        $affiliateScreen->loadFromData(array(Pap_Db_Table_AffiliateScreens::CODE));
        return $affiliateScreen->getId();
    }
}
?>
