<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Rene Dohanisko
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: BannerUpload.class.php 18513 2008-06-13 15:19:18Z aharsani $
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
class Pap_Features_RebrandPdfBanner_Variables extends Gpf_Object {

    const VAR_AFF_SIGN = 'affsignup';
    const VAR_DEST_URL = 'destUrl';
    const VAR_AFF_ID = 'affId';
    const BANNER_ID = 'bannerId';

    /**
     * @var Pap_Common_User
     */
    private $user;
    /**
     * @var Pap_Common_Banner
     */
    private $banner;
    private $fields;

    function __construct(Pap_Common_User $user, Pap_Common_Banner $banner){
        $this->user = $user;
        $this->banner = $banner;
        $this->fields = Pap_Common_UserFields::getInstance();
        $this->fields->setUser($user);
    }

    static function getAll(){
        $fields = Pap_Common_UserFields::getInstance()->getUserFields(array('M', 'O', 'R'), Gpf::YES);
        $fields[self::VAR_AFF_SIGN] = Gpf_Lang::_('Affiliate Signup');
        $fields[self::VAR_DEST_URL] = Gpf_Lang::_('Destination URL');
        $fields[self::VAR_AFF_ID] = Gpf_Lang::_('Affiliate ID');
        $fields[self::BANNER_ID] = Gpf_Lang::_('Banner ID');

        $valueContext = new Gpf_Plugins_ValueContext($fields);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.RebrandPdfBanner_Variables.getAll', $valueContext);
        return $valueContext->get();
    }

    function getValue($code){
        if($code === self::VAR_AFF_ID){
            return $this->getAffiliateID();
        }else if($code === self::VAR_AFF_SIGN){
            return $this->getAffiliateSignup();
        }else if($code === self::VAR_DEST_URL){
            return $this->getDestURL();
        }else if($code === self::BANNER_ID){
            return $this->banner->getId();
        }
        $valueContext = new Gpf_Plugins_ValueContext($code);
        $valueContext->setArray(array('user' => $this->user));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.RebrandPdfBanner_Variables.getValue', $valueContext);
        if ($valueContext->get() != $code) {
            return $valueContext->get();
        }
        return $this->fields->getUserFieldValue($code);
    }

    function getAffiliateID(){
        return $this->user->getId();
    }

    function getAffiliateSignup(){
        return Pap_Affiliates_Promo_SignupForm::getSignupScriptUrl(true, $this->user);
    }

    private function convertUrlToNormal($url){
        return str_replace('&amp;','&',$url);
    }

    function getDestURL(){
        return $this->convertUrlToNormal($this->banner->getClickUrl($this->user, '', 0));
    }
}
?>
