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
class Pap_Merchants_Config_BannersFormatForm extends Gpf_Object {

	/**
     * Load default format for "reset to default format" button
     *
     * @service banner_format_setting read
     * @param $fields
     */
	public function loadDefaultFormat(Gpf_Rpc_Params $params) {
	   $form = new Gpf_Rpc_Form($params);
	   $form->setField("format", $this->loadDefaultFormatFromName($form->getFieldValue("Id")));

	   return $form;
	}

    /**
     * Load format for edit
     *
     * @service banner_format_setting read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField("format", $this->loadFormatFromName($form->getFieldValue("Id")));


        return $form;
    }

    private function loadDefaultFormatFromName($name) {
        $format = null;


        switch ($name) {
            case Pap_Settings::TEXT_BANNER_FORMAT_SETTING_NAME:
                $format = Pap_Settings::TEXT_BANNER_DEFAULT_FORMAT;
                break;
            case Pap_Settings::IMAGE_BANNER_FORMAT_SETTING_NAME:
                $format = Pap_Settings::IMAGE_BANNER_DEFAULT_FORMAT;
                break;
            case Pap_Settings::FLASH_BANNER_FORMAT_SETTING_NAME:
                $format = Pap_Settings::FLASH_BANNER_DEFAULT_FORMAT;
                break;
        }


        return $format;
    }

    private function loadFormatFromName($name) {
    	$format = null;


        switch ($name) {
        	case Pap_Settings::TEXT_BANNER_FORMAT_SETTING_NAME:
        		$format = Pap_Common_Banner_Text::getBannerFormat();
        		break;
            case Pap_Settings::IMAGE_BANNER_FORMAT_SETTING_NAME:
                $format = Pap_Common_Banner_Image::getBannerFormat();
                break;
            case Pap_Settings::FLASH_BANNER_FORMAT_SETTING_NAME:
                $format = Pap_Common_Banner_Flash::getBannerFormat();
                break;
        }


    	return $format;
    }

    /**
     * Load names and setting names for BannersAndLinksFormat
     *
     * @service banner_format_setting read
     * @param $fields
     */
    public function loadNames(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $accountId = Gpf_Session::getAuthUser()->getAccountId();

        $result = new Gpf_Data_RecordSet();
        $result->setHeader(array('settingName', 'name'));
        $result->add(array(Pap_Settings::TEXT_BANNER_FORMAT_SETTING_NAME, $this->_('Text link format')));
        $result->add(array(Pap_Settings::IMAGE_BANNER_FORMAT_SETTING_NAME, $this->_('Image banner format')));
        $result->add(array(Pap_Settings::FLASH_BANNER_FORMAT_SETTING_NAME, $this->_('Flash banner format')));

        return $result;
    }

    /**
     * @service banner_format_setting write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	$name = $form->getFieldValue("Id");
    	$format = $form->getFieldValue("format");

    	Gpf_Settings::set($name, $format);

    	$form->setInfoMessage($this->_("Banner format successfully saved"));
        
    	Pap_Db_Table_CachedBanners::clearCachedBanners();
        
        return $form;
    }
}

?>
