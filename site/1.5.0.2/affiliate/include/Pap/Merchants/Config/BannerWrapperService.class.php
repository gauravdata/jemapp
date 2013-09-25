<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
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
class Pap_Merchants_Config_BannerWrapperService extends Gpf_Object {

    const CONST_HTML = 'html';
    const CONST_WIDTH = 'width';
    const CONST_HEIGHT = 'height';
    const CONST_NAME = 'bannername';
    const CONST_BANNERID = 'bannerid';
    const CONST_HTMLCOMPL = 'htmlcompleteurl';
    const CONST_HTMLCOMPL_ENCODED = 'htmlcompleteurlEncoded';
    const CONST_HTMLCLEAN = 'htmlcleanurl';
    const CONST_HTMLJSURL = 'htmljsurl';
    const CONST_CLICKURL = 'clickurl';
    const CONST_TARGETURL = 'targeturl';
    const CONST_SEOSTRING = 'seostring';
    const URL_PARAM_WRAPPER = 'w';
    const URL_VALUE_INNERPAGE = 1;
    const URL_VALUE_CLEAN = 2;
    const INNERPAGE_BEGIN = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><body style="margin:0; padding:0;">';
    const INNERPAGE_END = '</body></html>';

    /**
     * Load wrapper for edit
     * @service banner_format_setting read
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $row = new Pap_Db_BannerWrapper();
        $row->setId($form->getFieldValue("Id"));
        $row->load();
        $form->setField("editor", $row->getCode());
        return $form;
    }

    /**
     * @service banner_format_setting read
     * @return Gpf_Data_RecordSet
     */
    public function loadWrapperNames(Gpf_Rpc_Params $params) {
        $row = new Pap_Db_BannerWrapper();
        $collection = $row->loadCollection();
        $result = new Gpf_Data_RecordSet();
        $result->setHeader(array('id', 'name'));
        foreach ($collection as $row){
            $result->add(array($row->getId(), $row->getName()));
        }
        return $result;
    }

    /**
     * @service banner_format_setting read
     * @return Gpf_Rpc_Map
     */
    public function loadEditorConstants(Gpf_Rpc_Params $params) {
        return new Gpf_Rpc_Map(array(
        self::CONST_TARGETURL => $this->_('Target URL'),
        self::CONST_CLICKURL => $this->_('Click URL'),
        self::CONST_NAME => $this->_('Banner Name'),
        self::CONST_BANNERID => $this->_('Banner ID'),
        self::CONST_WIDTH => $this->_('Width'),
        self::CONST_HEIGHT => $this->_('Height'),
        self::CONST_HTML => $this->_('Banner Html'),
        self::CONST_HTMLCOMPL => $this->_('Url to complete page with banner code.'),
        self::CONST_HTMLCOMPL_ENCODED => $this->_('Url to complete page with banner code (URLEncoded).'),
        self::CONST_HTMLCLEAN => $this->_('Url to clean banner code.'),
        self::CONST_HTMLJSURL => $this->_('Url to javascript banner code'),
        self::CONST_SEOSTRING => $this->_('Seo string')
        ));
    }

    /**
     * save wrapper code
     * @service banner_format_setting write
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $wrapperId = $form->getFieldValue("Id");
        $wrapperCode = $form->getFieldValue("editor");
        $row = new Pap_Db_BannerWrapper();
        $row->setId($wrapperId);
        $row->load();
        $row->setCode($wrapperCode);
        $row->save();
        $form->setInfoMessage($this->_("Banner wrapper successfully saved"));
        Pap_Db_Table_CachedBanners::clearCachedBanners();
        return $form;
    }

    /**
     *  @service banner_format_setting write
     */
    public function deleteWrapper(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        try {
            foreach ($action->getIds() as $id) {
                $row = new Pap_Db_BannerWrapper();
                $row->setId($id);
                $row->delete();
            }
        } catch (Exception $e) {
            $action->setErrorMessage($e->getMessage());
            $action->addError();
        }
        return $action;
    }

    /**
     *  @service banner_format_setting write
     */
    public function addWrapper(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        try {
            foreach ($action->getIds() as $name) {
                $row = new Pap_Db_BannerWrapper();
                $row->setName($name);
                $row->insert();
            }
        } catch (Exception $e) {
            $action->setErrorMessage($e->getMessage());
            $action->addError();
        }
        return $action;
    }

    public function getBannerInWrapper($bannercode, Pap_Common_Banner $banner, Pap_Common_User $user){
        $wrapper = new Pap_Db_BannerWrapper();
        $wrapper->setId($banner->getWrapperId());
        $wrapper->load();


        $code = $wrapper->getCode();
        $code = $this->replaceConstant(self::CONST_WIDTH, $banner->getWidth(), $code);
        $code = $this->replaceConstant(self::CONST_HEIGHT, $banner->getHeight(), $code);
        $code = $this->replaceConstant(self::CONST_HTML, $bannercode, $code);
        $code = $this->replaceConstant(self::CONST_NAME, $banner->getName(), $code);
        $code = $this->replaceConstant(self::CONST_BANNERID, $banner->getId(), $code);
        $completeUrl = $banner->getBannerScriptUrl($user)
        . '&' . self::URL_PARAM_WRAPPER . '=' . self::URL_VALUE_INNERPAGE;
        if($banner->getDynamicLink() != '') {
            $completeUrl .= '&'. Pap_Db_Table_CachedBanners::DYNAMIC_LINK . '=' . urlencode($banner->getDynamicLink());
        }
        $code = $this->replaceConstant(self::CONST_HTMLCOMPL, $completeUrl, $code);
        $code = $this->replaceConstant(self::CONST_HTMLCOMPL_ENCODED, urlencode($completeUrl), $code);
        $code = $this->replaceConstant(self::CONST_HTMLCLEAN, $banner->getBannerScriptUrl($user)
        . '&' . self::URL_PARAM_WRAPPER . '=' . self::URL_VALUE_CLEAN, $code);
        $code = $this->replaceConstant(self::CONST_CLICKURL, $banner->getClickUrl($user), $code);
        $code = $this->replaceConstant(self::CONST_TARGETURL, $banner->getDestinationUrl($user), $code);
        $code = $this->replaceConstant(self::CONST_HTMLJSURL, $banner->getBannerScriptUrl($user), $code);
        $code = $this->replaceConstant(self::CONST_SEOSTRING, $banner->getSeoString(), $code);
        return Pap_Common_Banner::cleanIncompleteCode($code);
    }

    private function replaceConstant($code, $value, $text) {
        return str_replace('{$'.$code.'}', $value, $text);
    }

    public static function fillCachedBanner(Pap_Common_Banner $banner, Pap_Db_CachedBanner $cachedBanner){
        if ($cachedBanner->getParentBannerId() != '') {
            $banner->setParentBannerId($cachedBanner->getParentBannerId());
        }
        
        $banner->fillCachedBanner($cachedBanner, Pap_Affiliates_User::loadFromId($cachedBanner->getUserId()));
        if($cachedBanner->getWrapper() == self::URL_VALUE_INNERPAGE){
            $cachedBanner->setCode(self::INNERPAGE_BEGIN . $cachedBanner->getCode() . self::INNERPAGE_END);
        }
    }

    public static function isWrapperRequest(Pap_Common_Banner $banner, Pap_Tracking_Request $request){
        return $banner->getWrapperId() !== null &&
        $request->getRequestParameter(self::URL_PARAM_WRAPPER) !== '';
    }
}

?>
