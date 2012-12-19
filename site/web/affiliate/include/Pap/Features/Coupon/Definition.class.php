<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_Coupon_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'Coupon';
        $this->name = $this->_('Coupons and Offline Sales');
        $this->description = $this->_('Coupons feature allows you to use coupons and offline sales form for offline marketing. %s', '<a href="'.Gpf_Application::getKnowledgeHelpUrl('features-and-functions/banners/coupons.html').'" target="_blank">More help in our Knowledge Base</a>');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.BannerFactory.getBannerObjectFromType', 'Pap_Features_Coupon_Main', 'getBanner');
        $this->addImplementation('PostAffiliate.merchant.menu', 'Pap_Features_Coupon_Main', 'addToMenu');
        $this->addImplementation('Tracker.action.recognizeParametersStarted', 'Pap_Features_Coupon_Main', 'recognizeParameters');
        $this->addImplementation('PostAffiliate.Banner.replaceBannerConstants', 'Pap_Features_Coupon_Constants', 'replaceCouponConstants');
        $this->addImplementation('PostAffiliate.RebrandPdfBanner_Variables.getAll', 'Pap_Features_Coupon_Constants', 'getCouponConstants');
        $this->addImplementation('PostAffiliate.RebrandPdfBanner_Variables.getValue', 'Pap_Features_Coupon_Constants', 'getCouponValue');
    }

    public function onDeactivate() {
        $this->deleteAllCouponBanners();
        $this->deleteAllCoupons();
        $this->deactivateInRebrand();
    }

    private function deleteAllCouponBanners() {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Pap_Db_Table_Banners::getName());
        $delete->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_Coupon_Coupon::TYPE_COUPON);
        $delete->execute();
    }

    private function deleteAllCoupons() {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Pap_Db_Table_Coupons::getName());
        $delete->execute();
    }

    private function deactivateInRebrand() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_Banners::getInstance());
        $select->from->add(Pap_Db_Table_Banners::getName());
        $select->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_RebrandPdfBanner_Config::TYPE);

        foreach ($select->getAllRowsIterator() as $rebrandData) {
            $this->removeCouponConstants($rebrandData);
        }
    }

    private function removeCouponConstants(Gpf_Data_Record $rebrandData) {
        $rebrand = new Pap_Features_RebrandPdfBanner_Banner();
        $rebrand->fillFromRecord($rebrandData);
        $variables = $rebrand->getVariables();
        foreach ($variables as $variable) {
            if (substr_count($variable, 'couponcode_') > 0) {
                $rebrand->removeVariable($variable);
            }
        }
        if (count($variables) > count($rebrand->getVariables())) {
            $rebrand->update();
        }
    }
}
?>
