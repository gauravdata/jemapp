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
class Pap_Common_Banner_Image extends Pap_Common_Banner {

    protected function getBannerCode(Pap_Common_User $user, $flags) {
        $imageUrl = $this->getImageUrl();
        if((@$_SERVER['HTTPS'] == 'on') && (strpos($imageUrl, 'https') === false)){
            $imageUrl = str_ireplace('http', 'https', $imageUrl);
        }
        $description = $this->getDescription($user);
        $format = $this->getBannerFormat();

        $format = Pap_Common_UserFields::replaceCustomConstantInText('image_src', $imageUrl, $format);
        $format = Pap_Common_UserFields::replaceCustomConstantInText('image_name', basename($imageUrl), $format);
        $format = Pap_Common_UserFields::replaceCustomConstantInText('alt', $description, $format);
        $format = Pap_Common_UserFields::replaceCustomConstantInText(Pap_Db_Table_Banners::SEOSTRING, $this->getSeoString(), $format);

        $format = $this->replaceUrlConstants($format, $user, $flags, '');
        $format = $this->replaceUserConstants($format, $user);
        $format = $this->replaceWidthHeightConstants($format, $flags);

        return $format;
    }

    public function getImageUrl() {
        return $this->getData(1);
    }

    public static function getBannerFormat() {
        return Gpf_Settings::get(Pap_Settings::IMAGE_BANNER_FORMAT_SETTING_NAME);
    }

    protected function setDetectedSize($size){
        $this->setData(4, $size);
    }

    protected function getDetectedSize(){
        return $this->getData(4);
    }

    protected function beforeSaveAction() {
        $this->detectImageSize();
    }

    private function detectImageSize(){
        $image = $this->encodeImageUrlForGetImageSize($this->getImageUrl());
        if (($size = @getimagesize($image)) !== false) {
            $this->setDetectedSize($size[0].'x'.$size[1]);
        } else {
            $this->setDetectedSize(Gpf_DbEngine_Row::NULL);
        }
    }
    
    /**
     * @return String
     */
    protected function encodeImageUrlForGetImageSize($url) {
    	$url = urldecode($url);
        $url = str_replace(' ', '%20', $url);
        return $url;
    }

    protected function setUndefinedSize(){
        if($this->getDetectedSize() != null){
            $size = explode('x',$this->getDetectedSize());
            $this->width = $size[0];
            $this->height = $size[1];
        }
    }
}

?>
