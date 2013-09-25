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

/**
 * @package PostAffiliatePro
 */
class Pap_Features_Coupon_Constants extends Gpf_Plugins_Handler {

    public static function getHandlerInstance() {
        return new Pap_Features_Coupon_Constants();
    }

    /**
     * @service coupon read
     * @param $params
     * @return Gpf_Data_RecordSet
     */
    public function loadCouponConstants(Gpf_Rpc_Params $params) {
        $couponConstants = new Gpf_Data_RecordSet();
        $couponConstants->setHeader(array('id', 'name'));
        foreach ($this->getCouponBannersSelect()->getAllRowsIterator() as $couponBannerData) {
            if ($params->get('bannertype') == Pap_Common_Banner_Factory::BannerTypeHtml || $params->get('bannertype') == Pap_Common_Banner_Factory::BannerTypePromoEmail) {
                $couponConstant = $couponConstants->createRecord();
                $couponConstant->set('id', 'coupon_' . $couponBannerData->get('id'));
                $couponConstant->set('name', $this->_($couponBannerData->get('name')));
                $couponConstants->add($couponConstant);
            }
            $couponConstant = $couponConstants->createRecord();
            $couponConstant->set('id', 'couponcode_' . $couponBannerData->get('id'));
            $couponConstant->set('name', $this->_($couponBannerData->get('name')) . ' ' . $this->_('(only coupon code)'));
            $couponConstants->add($couponConstant);
        }
        return $couponConstants;
    }

    public function replaceCouponConstants(Gpf_Plugins_ValueContext $valueContext) {
        $valueArray = $valueContext->getArray();
        if ($valueArray['bannerType'] == Pap_Common_Banner_Factory::BannerTypeHtml || $valueArray['bannerType'] == Pap_Common_Banner_Factory::BannerTypePromoEmail) {
            $valueContext->set($this->replaceConstants($valueContext->get(), '{$coupon_', $valueArray['user']->getId()));
        }
        $valueContext->set($this->replaceConstants($valueContext->get(), '{$couponcode_', $valueArray['user']->getId()));
    }

    public function getCouponConstants(Gpf_Plugins_ValueContext $valueContext) {
        $constants = $valueContext->get();
        foreach ($this->getCouponBannersSelect()->getAllRowsIterator() as $couponBannerData) {
            $constants['couponcode_' . $couponBannerData->get('id')] = $this->_($couponBannerData->get('name')) . ' ' . $this->_('(only coupon code)');
        }
        $valueContext->set($constants);
    }

    public function getCouponValue(Gpf_Plugins_ValueContext $valueContext) {
        $valueArray = $valueContext->getArray();
        if (substr_count($valueContext->get(), 'couponcode_')) {
            $valueContext->set($this->replaceConstants($valueContext->get() . '}', 'couponcode_', $valueArray['user']->getId()));
        }
    }

    /**
     * @param $bannerID
     * @return Iterator
     */
    protected function getCoupons($bannerID, $userID) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_Coupons::getInstance());
        $selectBuilder->from->add(Pap_Db_Table_Coupons::getName());
        $selectBuilder->where->add(Pap_Db_Table_Coupons::BANNERID, '=', $bannerID);
        $selectBuilder->where->add(Pap_Db_Table_Coupons::USERID, '=', $userID);
        $selectBuilder->limit->set(0, 1);
        return $selectBuilder->getAllRowsIterator();
    }

    public function replaceConstants($text, $constantPrefix = '{$coupon_', $userID) {
        $constantPrefixLenght = strlen($constantPrefix);
        $constantLenght = $constantPrefixLenght + 9;
        $offset = 0;
        while (($start = strpos($text, $constantPrefix, $offset)) !== false && strlen(substr($text, $start)) >= $constantLenght) {
            $offset = $start + $constantLenght;
            $couponConstant = substr($text, $start, $constantLenght);
            $bannerID = substr($couponConstant, $constantPrefixLenght, -1);

            try {
                $couponBanner = $this->getCouponBanner($bannerID);
            } catch (Gpf_Exception $e) {
                continue;
            }
            
            foreach ($this->getCoupons($bannerID, $userID) as $couponData) {
                $coupon = new Pap_Db_Coupon();
                $coupon->fillFromRecord($couponData);
                if ($constantPrefix == '{$coupon_') {
                    $couponText = $couponBanner->getCouponText($coupon);
                    $text = str_replace($couponConstant, $couponText, $text);
                    $offset = $start + strlen($couponText);
                    continue;
                }
                $text = str_replace($couponConstant, $coupon->getCode(), $text);
                $offset = $start + strlen($coupon->getCode());
            }
        }
        return $text;
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @param $bannerID
     * @return Pap_Features_Coupon_Coupon
     */
    protected function getCouponBanner($bannerID) {
        $couponBanner = new Pap_Features_Coupon_Coupon();
        $couponBanner->setId($bannerID);
        $couponBanner->load();
        return $couponBanner;
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    private function getCouponBannersSelect() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Pap_Db_Table_Banners::ID, "id");
        $select->select->add(Pap_Db_Table_Banners::NAME, "name");
        $select->from->add(Pap_Db_Table_Banners::getName());
        $select->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_Coupon_Coupon::TYPE_COUPON);
        $select->orderBy->add(Pap_Db_Table_Banners::NAME);
        return $select;
    }
}
?>
