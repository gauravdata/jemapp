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
class Pap_Merchants_Banner_BannerForm extends Gpf_View_FormService {

    /**
     * @var Pap_Common_Banner_Factory
     */
    private $bannerFactory;
    
    public function __construct() {
        $this->bannerFactory = new Pap_Common_Banner_Factory();
    }
    
    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Common_Banner();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Banner");
    }

    /**
     * @service banner read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $bannerFactory = new Pap_Common_Banner_Factory();
        try {
        	$banner = $bannerFactory->getBanner($form->getFieldValue("Id"));
            $banner->fillForm($form);
            $this->decodeSize($form, $banner);
            $this->addRichTextLinks($form, $banner);
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannerForm.load', $form);
        } catch (Pap_Common_Banner_NotFound $e) {
            throw new Exception($this->_("Banner does not exist"));
        }
        return $form;
    }

    /**
     * @service banner write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        return $this->_save($params, "update");
    }

    /**
     * @service banner write
     * @param $fields
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @service banner add
     * @param $fields
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->_save($params, "add");
    }

    /**
     * @service banner add
     * @param $fields
     */
    public function cloneBanners(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $campaign = $action->getParam('campaign');
        $url = $action->getParam('url');
        try {
            foreach ($action->getIds() as $id) {
                $row = new Pap_Db_Banner();
                $row->setId($id);
                $row->load();
                $this->cloneBanner($row,$campaign,$url);
            }
        } catch (Exception $e) {
            $action->setErrorMessage($e->getMessage());
            $action->addError();
        }
        return $action;
    }

    private function cloneBanner(Pap_Db_Banner $banner, $campaign, $url = null){
        $bannerClone =  clone $banner;
        $bannerClone->setCampaignId($campaign);
        if($url != null){
            $bannerClone->setDestinationUrl($url);
        }
        $bannerClone->setPrimaryKeyValue(NULL);
        $bannerClone->insert();
    }
     
    /**
     * @service banner delete
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
    
    protected function deleteRow(Gpf_DbEngine_RowBase $row) {
        $banner = $this->bannerFactory->getBanner($row->getId());
        $banner->delete();
    }
    
    /**
     * @return Pap_Common_Banner
     */
    protected function createBannerObject($type) {
        return Pap_Common_Banner_Factory::getBannerObjectFromType($type);
    }
     
    /**
     * function that does the saving
     *
     * @param Gpf_Rpc_Params $params
     * @param string $type = add/update
     * @return unknown
     */
    private function _save(Gpf_Rpc_Params $params, $type) {
        $form = new Gpf_Rpc_Form($params);
        $banner = $this->createBannerObject($form->getFieldValue('rtype'));
        if($type == "add") {            
            $banner->set("dateinserted", Gpf_Common_DateUtils::now());
            $banner->setDestinationUrl('');
        } else if ($type == "update") {
            $banner->set('bannerid', $form->getFieldValue("Id"));
            try {
                $banner->load();
            } catch (Gpf_DbEngine_NoRowException $e) {
                $form->setErrorMessage($this->_("Banner does not exist"));
                return $form;
            }
        } else {
            throw new Gpf_Exception($this->_("Server error: Unknown type given for BannerForm._save()"));
        }

        try {
        	$banner->set(Pap_Db_Table_Campaigns::ACCOUNTID, $this->getAccountId($form));
        	$banner->encodeSize($form, 'size');
        	$this->removeRichTextLinks($form);
        	$form->fill($banner);
        	if (!$this->checkBeforeSave($banner, $form, $type)) {
        		return $form;
        	}
            $banner->save();
            $this->clearRotatorsWithBanner($banner);
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($banner);
        $form->setInfoMessage($this->_("Banner was saved"));
        return $form;
    }
    
    protected function clearRotatorsWithBanner($banner){
        $bannerInRotator = new Pap_Db_BannerInRotator();
        $bannerInRotator->setRotatedBannerId($banner->getId());
        
        foreach($bannerInRotator->loadCollection() as $bannerInRotatorDelete){
            Pap_Db_Table_CachedBanners::deleteCachedBannersForBanner($bannerInRotatorDelete->getParentBannerId());
        }
    }

    /**
     * checks correctness of the banner data
     *
     * @param Gpf_Data_Row $row
     * @param string $type = add/update
     *      * @return unknown
     */
    protected function checkBeforeSave(Gpf_Data_Row $row, Gpf_Rpc_Form $form, $type) {
    	$row->initValidators($form);
    	return $form->validate();
    }

    /**
     * decodes one field size into multiple fields required by client
     *
     * @param Gpf_Rpc_Form $form
     */
    private function decodeSize(Gpf_Rpc_Form $form, Pap_Common_Banner $banner) {
    	$form->setField('size', $banner->getSizeType(Pap_Db_Table_Banners::SIZE));
        if ($form->getFieldValue('size') == Pap_Db_Banner::SIZE_PREDEFINED) {
        	$form->setField('size_predefined', $banner->getWidth().'x'.$banner->getHeight());
        	return;
        }
         
        if ($form->getFieldValue('size') == Pap_Db_Banner::SIZE_OWN) {
            $form->setField('size_width', $banner->getWidth());
            $form->setField('size_height', $banner->getHeight());
        }
    }

    /**
     * removes style from rich text links, and convert the constants for {} to real {}
     *
     * @param Gpf_Rpc_Form $form
     */
    private function removeRichTextLinks(Gpf_Rpc_Form $form) {
        if($form->getFieldValue('rtype') != Pap_Common_Banner_Factory::BannerTypePromoEmail
        && $form->getFieldValue('rtype') != Pap_Common_Banner_Factory::BannerTypePdf)
        {
            return;
        }
         
        $data2 = $form->getFieldValue('data2');
        $data2 = str_replace('style="color: blue; text-decoration: underline;" ', '', $data2);
        $data2 = str_replace('%7B', '{', $data2);
        $data2 = str_replace('%7D', '}', $data2);

        $form->setField('data2', $data2);
    }

    /**
     * removes style from rich text links, and convert the constants for {} to real {}
     *
     * @param Gpf_Rpc_Form $form
     */
    private function addRichTextLinks(Gpf_Rpc_Form $form, Pap_Common_Banner $banner) {
        if($banner->getBannerType() != Pap_Common_Banner_Factory::BannerTypePromoEmail
        && $banner->getBannerType() != Pap_Common_Banner_Factory::BannerTypePdf)
        {
            return;
        }
         
        $data2 = $form->getFieldValue('data2');
        $data2 = $this->addDefaultStyle($data2);
        $data2 = str_replace('"{', '"%7B', $data2);
        $data2 = str_replace('}"', '%7D"', $data2);

        $form->setField('data2', $data2);
    }
    
    private function addDefaultStyle($text) {
        if(preg_match("/style\s*=\s*['\"].*?['\"]/", $text)) {
            return $text;
        }
        return str_replace(array('<a', '<A'), '<a style="color: blue; text-decoration: underline;"', $text);
    }
    
   	/**
   	 * @param Gpf_Rpc_Form
   	 * @return String
   	 */
    private function getAccountId(Gpf_Rpc_Form $form) {
        if (Gpf_Session::getAuthUser()->isMasterMerchant()) {
            if ($form->existsField('campaignid')) {
                return $this->getAccountIdFromCampaignId($form->getFieldValue('campaignid'));
            }
            if ($form->existsField('id')) {
                return $this->getAccountIdFromBannerId($form->getFieldValue('id'));
            }
        }
        return Gpf_Session::getAuthUser()->getAccountId();
    }

    /**
     * @param $campaignId
     * @return String
     */
    private function getAccountIdFromCampaignId($campaignId) {
        $campaign = Pap_Common_Campaign::getCampaignById($campaignId);
        if ($campaign == null) {
            throw new Gpf_Exception($this->_('Campaign with id %s does not exist.', $campaignId));
        }
        return $campaign->getAccountId();
    }

    /**
     * @param $bannerId
     * @return String
     */
    private function getAccountIdFromBannerId($bannerId) {
        $banner = new Pap_Db_Banner();
        $banner->setPrimaryKeyValue($bannerId);
        try {
            $banner->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Gpf_Exception($this->_('Banner with id %s does not exist.', $bannerId));
        }
        return $banner->getAccountId();
    }
}

?>
