<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 30595 2010-12-23 13:27:26Z iivanco $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * Banner business object used for tracking and code generation
 *
 * @package PostAffiliatePro
 */
class Pap_Common_Banner extends Pap_Db_Banner {
    const BANNER_PREVIEW_HEIGHT = '50';
    const BANNER_PREVIEW_HEIGHT_ACTUAL_SIZE = '100%';
    const HTML_AMP = '&amp;';
    const FLAG_MERCHANT_PREVIEW = 1;
    const FLAG_AFFILIATE_PREVIEW = 4;
    const FLAG_DIRECTLINK = 2;
    const FLAG_RAW_CODE = 8;

    /**
     * @var Pap_Common_Banner_Rotator
     */
    private $rotator = null;
    /**
     * @var Pap_Db_Channel
     */
    protected $channel = null;

    private $dynamicLink = null;

    private $parentBannerId = null;

    /**
     * @var Pap_Common_Banner
     */
    private $parentBanner = null;
    protected $viewInActualSize;

    function __construct() {
        parent::__construct();
    }

    public function setChannel(Pap_Db_Channel $channel) {
        $this->channel = $channel;
    }

    public function fillForm(Gpf_Rpc_Form $form) {
        $form->load($this);
    }

    /**
     * stores width x height to the size field.
     *
     * @param Gpf_Rpc_Form $form
     * @param String $sizeFieldName
     */
    public function encodeSize(Gpf_Rpc_Form $form, $sizeFieldName) {
        if($form->getFieldValue($sizeFieldName) == Pap_Db_Banner::SIZE_PREDEFINED) {
            $form->setField($sizeFieldName, $form->getFieldValue($sizeFieldName).$form->getFieldValue('size_predefined'));
        }
        if($form->getFieldValue($sizeFieldName) == Pap_Db_Banner::SIZE_OWN) {
            $form->setField($sizeFieldName, $form->getFieldValue($sizeFieldName).$form->getFieldValue('size_width').'x'.$form->getFieldValue('size_height'));
        }
    }

    protected function getBannerCode(Pap_Common_User $user, $flags) {
    }

    /**
     * @return String
     */
    public function getParentBannerId() {
        return $this->parentBannerId;
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @return Pap_Common_Banner
     */
    public function getParentBanner() {
        if($this->parentBanner == null && $this->parentBannerId != null){
            $factory  = new Pap_Common_Banner_Factory();
            $this->parentBanner = $factory->getBanner($this->parentBannerId);
        }
        return $this->parentBanner;
    }

    /*
     *@param String $bannerId
     */
    function setParentBannerId($bannerId){
        $this->parentBannerId = $bannerId;
    }
     
    /**
     * Used by hower banner to display banner or in affiliate panel to get banner code
     *
     * @param Pap_Common_User $user
     * @return string
     */
    public function getCode(Pap_Common_User $user, $flags = '') {
        return $this->getCompleteCode($user, $flags);
    }

    public function getDynamicLinkCode(Pap_Common_User $user, $dynamicLink) {
        $this->setDynamicLink($dynamicLink);
        return $this->getCompleteCode($user, Pap_Tracking_ClickTracker::LINKMETHOD_REDIRECT);
    }

    public function getPreview(Pap_Common_User $user) {
        $flag = self::FLAG_MERCHANT_PREVIEW;
        if(Gpf_Session::getAuthUser()->isAffiliate()) {
            $flag = self::FLAG_AFFILIATE_PREVIEW;
        }
        return $this->getBannerCode($user, $flag);
    }

    public function getDisplayCode(Pap_Common_User $user) {
        $flags = '';
        if($this->getDynamicLink() != '') {
            $flags = Pap_Tracking_ClickTracker::LINKMETHOD_REDIRECT;
        }
        return $this->getBannerCode($user, $flags);
    }

    public function fillCachedBanner(Pap_Db_CachedBanner $cachedBanner, Pap_Common_User $user) {
        $cachedBanner->setHeaders('');
        $cachedBanner->setDynamicLink($this->getDynamicLink());
        $cachedBanner->setCode($this->getDisplayCode($user, ''));
    }

    public function getDirectLinkCode(Pap_Common_User $user) {
        return $this->getCompleteCode($user, self::FLAG_DIRECTLINK);
    }

    public function getCompleteCode(Pap_Common_User $user, $flags){
        $code = $this->getBannerCode($user, $flags);
        $id = $this->getWrapperId();
        if($this->getWrapperId() !== null && $this->getWrapperId() !== ''){
            $wrapperservice = new Pap_Merchants_Config_BannerWrapperService();
            $code = $wrapperservice->getBannerInWrapper($code, $this, $user);
        }
        return $code;
    }

    public function initValidators(Gpf_Rpc_Form $form) {
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::NAME, $this->_('name'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::STATUS, $this->_('status'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::SIZE, $this->_('size'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::DESTINATION_URL, $this->_('destination url'));
    }

    /**
     * Replaces width and height in banner format
     *
     * @param string $format
     * @param boolean $isPreview
     * @return string
     */
    protected function replaceWidthHeightConstants($format, $flags) {
        
        if($this->viewInActualSize == Gpf::YES) {
            $format = Pap_Common_UserFields::replaceCustomConstantInText('height', Pap_Common_Banner::BANNER_PREVIEW_HEIGHT_ACTUAL_SIZE, $format);
            $format = Pap_Common_UserFields::replaceCustomConstantInText('width', '', $format);
            return self::cleanIncompleteCode($format);
        }
        
        if($this->getWidth() > 0 && $this->getHeight() > 0) {
            if(($flags & self::FLAG_MERCHANT_PREVIEW)&&($this->getHeight() > Pap_Common_Banner::BANNER_PREVIEW_HEIGHT)) {
                $ratio = $this->getWidth()/$this->getHeight();
                $newHeight = Pap_Common_Banner::BANNER_PREVIEW_HEIGHT;
                $newWidth = $ratio*$newHeight;
            } else {
                $newHeight = $this->getHeight();
                $newWidth = $this->getWidth();
            }
            $format = Pap_Common_UserFields::replaceCustomConstantInText('width', $newWidth, $format);
            $format = Pap_Common_UserFields::replaceCustomConstantInText('height', $newHeight, $format);
        } else {
            if ($flags & self::FLAG_MERCHANT_PREVIEW) {
                $format = Pap_Common_UserFields::replaceCustomConstantInText('height', Pap_Common_Banner::BANNER_PREVIEW_HEIGHT, $format);
            } else {
                $format = Pap_Common_UserFields::replaceCustomConstantInText('height', '', $format);
            }
            $format = Pap_Common_UserFields::replaceCustomConstantInText('width', '', $format);
        }
        return self::cleanIncompleteCode($format);
    }

    public static function cleanIncompleteCode($code){
        $code = str_replace(array('width=""', "width=''", 'height=""', "height=''"), '', $code);
        return $code;
    }

    public function replaceBannerConstants($text, Pap_Common_User $user) {
        $text = str_replace('{$bannerid}', $this->getId(), $text);
        $valueContext = new Gpf_Plugins_ValueContext($text);
        $valueContext->setArray(array('bannerType' => $this->getBannerType(), 'user' => $user));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Banner.replaceBannerConstants', $valueContext);
        return $valueContext->get();
    }

    /**
     * Replaces Url constants: targeturl, targeturl_encoded, impression_track
     *
     * @return string
     */
    public function replaceUrlConstants($text, Pap_Common_User $user = null, $flags, $destinationUrl) {
        $clickUrl = $this->getClickUrl($user, $destinationUrl, $flags);
        $impressionTrack = $this->getImpressionTrackingCode($user, $flags);

        $clickUrlEncoded = $this->urlEncodeClickUrl($clickUrl);

        $text = Pap_Common_UserFields::replaceCustomConstantInText('targeturl', $clickUrl, $text);
        $text = Pap_Common_UserFields::replaceCustomConstantInText('targeturl_encoded', $clickUrlEncoded, $text);
        $text = Pap_Common_UserFields::replaceCustomConstantInText('impression_track', $impressionTrack, $text);

        $context = new Pap_Common_BannerReplaceVariablesContext($text, $this, $user);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Banner.replaceUrlConstants', $context);
        $text = $context->getText();

        return $text;
    }

    private function urlEncodeClickUrl($clickUrl) {
        return urlencode(str_replace('&amp;', '&', $clickUrl));
    }

    /**
     * Replaces user constants like username, firstname, ... data25
     *
     * @return string
     */
    public function replaceUserConstants($text, $user, $mainFields = null) {
        $userFields = Pap_Common_UserFields::getInstance();
        $userFields->setUser($user);

        $text = $userFields->replaceUserConstantsInText($text, $mainFields);
        $text = Pap_Common_UserFields::removeCommentsInText($text);

        return $text;
    }

    /**
     * Removes user constants like username, firstname, ... data25
     *
     * @return string
     */
    public function removeUserConstants($text, $mainFields = null) {
        $userFields = Pap_Common_UserFields::getInstance();

        $text = $userFields->removeUserConstantsInText($text, $mainFields);
        $text = Pap_Common_UserFields::removeCommentsInText($text);

        return $text;
    }

    /**
     * Replaces user constants like username, firstname, ... data25
     *
     * @return string
     */
    public function replaceClickConstants($text, $clickFieldsValues) {
        foreach($clickFieldsValues as $code => $value) {
            $text = Pap_Common_UserFields::replaceCustomConstantInText($code, $value, $text);
        }
        $text = Pap_Common_UserFields::removeCommentsInText($text);
        return $text;
    }

    /**
     * @param Pap_Common_User $user
     * @param string $specialDesturl
     * @return String click URL
     */
    public function getClickUrl(Pap_Common_User $user, $specialDesturl = '', $flags = '') {
        if($flags & Pap_Common_Banner::FLAG_MERCHANT_PREVIEW) {
            if($specialDesturl == '') {
                return $this->getDestinationUrl($user);
            }
            return $specialDesturl;
        }

        return Pap_Tracking_ClickTracker::getInstance()->getClickUrl($this, $user, $specialDesturl, $flags, $this->channel);
    }

    /**
     * @param Pap_Common_User $user
     * @return String impression tracking code
     */
    public function getImpressionTrackingCode(Pap_Common_User $user, $flags) {
        if($flags & Pap_Common_Banner::FLAG_MERCHANT_PREVIEW || $flags & Pap_Common_Banner::FLAG_AFFILIATE_PREVIEW) {
            return '';
        }
        return Pap_Tracking_ImpressionTracker::getInstance()->getImpressionTrackingCode($this, $user, $this->channel);
    }

    public function setDynamicLink($dynamicLink = null) {
        $this->dynamicLink = $dynamicLink;
    }

    public function getDynamicLink() {
        return $this->dynamicLink;
    }

    public function getBannerScriptUrl(Pap_Common_User $user) {
        return Pap_Tracking_BannerViewer::getBannerScriptUrl($user->getRefId(), $this->getId(), $this->getChannelId(), $this->getParentBannerId());
    }

    protected function getChannelId(){
        if($this->channel != null){
            return $this->channel->getValue();
        }
        return null;
    }

    /**
     * @return Pap_Db_Channel
     */
    public function getChannel() {
        return $this->channel;
    }

    public function getDestinationUrl($user = null) {
        if ($user === null) {
            return parent::getDestinationUrl();
        }
        return $this->replaceUserConstants(parent::getDestinationUrl(), $user);
    }
    
    public function setViewInActualSize($actualSize) {
        $this->viewInActualSize = $actualSize;
    }
}
?>
