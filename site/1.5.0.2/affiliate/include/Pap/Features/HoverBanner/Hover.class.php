<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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
class Pap_Features_HoverBanner_Hover extends Pap_Common_Banner {

    const FORMAT = '<html><head></head><body style="margin: 0px;"><a href="{$targeturl}" target="_parent"><img src="{$image_src}" style="border: none;"/></a>{$impression_track}</body></html>';
    const TYPE_HOVER = 'U';

    protected function getBannerCode(Pap_Common_User $user, $flags) {
        $code = $this->getBaseCode($user, 'hover.php', $flags);
        $code .= "<script>setTimeout(\"showOnLoad('" . $this->getId() . "','".$this->get('data3')."')\", 500);</script>";

        return $code;
    }

    public function getDisplayCode(Pap_Common_User $user) {
        return $this->getDisplayCodeFrom('data1', $user);
    }

    private function getDisplayCodeFrom($data, Pap_Common_User $user, $flags = '') {
        $imageUrl = $this->get($data);
        $description = $this->getDescription($user);
        $format = self::FORMAT;

        $format = Pap_Common_UserFields::replaceCustomConstantInText('image_src', $imageUrl, $format);

        $format = $this->replaceUserConstants($format, $user);
        $format = $this->replaceUrlConstants($format, $user, $flags, '');

        return $format;
    }

    public function getPreview(Pap_Common_User $user) {
        $flag = self::FLAG_MERCHANT_PREVIEW;
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            return $this->getAfiliatePreview($user);
        }
        return $this->getDisplayCodeFrom('data2', $user, $flag);
    }

    public function getPreviewCode(Pap_Common_User $user) {
        return $this->getDisplayCodeFrom('data1', $user, self::FLAG_AFFILIATE_PREVIEW);
    }

    private function getAfiliatePreview(Pap_Common_User $user) {
        $code = '<div>'.$this->getBaseCode($user, 'preview.php');
        $code .= '<a onclick="show(\'' . $this->getId() . '\','.$this->get('data3').');">'.
        $this->_('Click here to see hover banner').'</a></div>';
        $code .= '<div>'.$this->getDisplayCodeFrom('data2', $user, self::FLAG_AFFILIATE_PREVIEW).'</div>';

        return $code;
    }

    private function getBaseCode(Pap_Common_User $user, $fileName = 'hover.php', $flags = '') {
        $url = Gpf_Paths::getInstance()->getFullBaseServerUrl() . "include/Pap/Features/HoverBanner/LyteBox/";
        $code = '<script type="text/javascript" src="'.
        $url . 'lytebox.js"></script>
                <link rel="stylesheet" href="'.
        $url . 'lytebox.css" type="text/css" media="screen" />
                <div id="' . $this->getId() . '" rel="lyteframe" rev="width: {$width}px; height: {$height}px; scrolling: no;" href="'.
        Gpf_Paths::getInstance()->getFullScriptsUrl() . $fileName .
                '?'.Gpf_Settings::get(Pap_Settings::PARAM_NAME_USER_ID).'='.$user->getId().
                '&amp;'.Gpf_Settings::get(Pap_Settings::PARAM_NAME_BANNER_ID).'='.$this->getId() . '"></div>';
        return $this->replaceWidthHeightConstants($code, $flags);
    }
     
    protected function setUndefinedSize() {
        if (($imageSize = @getimagesize($this->getData1())) !== false) {
            list($this->width, $this->height) = @getimagesize($this->getData1());
            return;
        }
        $this->width = 400;
        $this->height = 300;
    }
}

?>
