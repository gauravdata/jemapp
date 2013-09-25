<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Common_Banner_Factory extends Gpf_Object {
    const BannerTypeText 		= 'T';
    const BannerTypeImage 		= 'I';
    const BannerTypeHtml 		= 'H';
    const BannerTypeFlash       = 'F';
    const BannerTypePopup 		= 'P';
    const BannerTypePopunder	= 'U';

    const BannerTypeLandingPage = 'L';
    const BannerTypeOffline     = 'O';
    const BannerTypePdf 		= 'V';
    const BannerTypePromoEmail  = 'E';
    const BannerTypeLink	= 'A';


    /**
     * Returns banner object
     *
     * @throws Gpf_DbEngine_NoRowException
     * @param string $bannerId banner ID
     * @return Pap_Common_Banner
     */
    public function getBanner($bannerId) {
        if ($bannerId == '') {
            throw new Pap_Common_Banner_NotFound($bannerId);
        }
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->from->add(Pap_Db_Table_Banners::getName());
        $select->select->addAll(Pap_Db_Table_Banners::getInstance());
        $select->where->add(Pap_Db_Table_Banners::ID, '=', $bannerId);
        return $this->getBannerFromRecord($select->getOneRow());
    }

    /**
     * Returns banner object for given banner record
     *
     * @param string $record banner record loaded from other code
     * @return Pap_Common_Banner
     */
    public function getBannerFromRecord(Gpf_Data_Record $record) {
        $banner = $this->getBannerObjectFromType($record->get('rtype'));
        if($banner == null) {
            throw new Pap_Common_Banner_NotFound($record->get('id'));
        }

        $banner->fillFromRecord($record);
        return $banner;
    }

    /**
     * @param string $bannerId
     * @param string $bannerType
     * @return Pap_Common_Banner
     */
    public function getBannerObject($bannerId, $bannerType) {
        $obj = $this->getBannerObjectFromType($bannerType);
        if($obj == null) {
            throw new Pap_Common_Banner_NotFound($bannerId);
        }
        $obj->setId($bannerId);
        $obj->load();

        return $obj;
    }

    /**
     * @param string $bannerType
     * @return Pap_Common_Banner
     */
    public static function getBannerObjectFromType($bannerType) {
        switch ($bannerType) {
            case self::BannerTypeText:
                return new Pap_Common_Banner_Text();
            case self::BannerTypeImage:
                return new Pap_Common_Banner_Image();
            case self::BannerTypeFlash:
                return new Pap_Common_Banner_Flash();
            case self::BannerTypeHtml:
                return new Pap_Common_Banner_Html();
            case self::BannerTypePromoEmail:
                return new Pap_Common_Banner_PromoEmail();
            case self::BannerTypePdf:
                return new Pap_Common_Banner_PDF();
            case self::BannerTypeLink:
            	return new Pap_Common_Banner_Link();
        }
        $bannerTypeRequest  = new Pap_Common_Banner_BannerRequest($bannerType);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannerFactory.getBannerObjectFromType',$bannerTypeRequest);
        return $bannerTypeRequest->getBanner();
    }
}

?>
