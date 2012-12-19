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
class Pap_Features_PagePeelBanner_Banner extends Pap_Common_Banner_Image {

    const HTML = '<div id="banner_peel_TYPE" style="overflow: hidden; position: absolute; width: WIDTHSMALLpx; height:HEIGHTSMALLpx; z-index: 0; SIDE: 0px; top: 0px; padding-bottom:10px; padding-OPOSITE: 10px;">
                  <embed style="position:relative;left:POSITIONCORRECTIONpx;" name="banner_peel_TYPE_sub"
                  id="banner_peel_TYPE_sub"
                  flashvars="bannertype=TYPE&bannerwidth=WIDTHBIG&bannerheight=HEIGHTBIG&img=BIGIMG&smallimg=SMALLIMG&link={$targeturl_encoded}&scrolltxt=SCROLLTEXT&bigtxt=BIGTEXT&bgcolor=BGCOLOR&textcolor=CC0000&smallperc=SMALLPERCENTAGE"
                  scale="exactfit" pluginspage="http://www.macromedia.com/go/getflashplayer"
                  type="application/x-shockwave-flash" allowscriptaccess="always" wmode="transparent"
                  src="URL" height="HEIGHTBIG" width="WIDTHBIG">
                  </div><img style="border: 0pt none ;" src="TRACKIMG" alt="" height="1" width="1">';


    const JAVASCRIPT = "<script type='text/javascript'>
                      function peelbannerbefore(type,w,h)
                      {
                          var peel = document.getElementById('banner_peel_'+type);
                            peel.style.width='WIDTHBIGpx';
                            peel.style.height='HEIGHTBIGpx';
                            if(type=='R')
                              document.getElementById('banner_peel_'+type+'_sub').style.left='0px';
                            peel.style.zIndex=1000;
                      }
                      function peelbannerafter(type,w,h)
                      {
                          var peel = document.getElementById('banner_peel_'+type);
                            if(type=='R')
                                document.getElementById('banner_peel_'+type+'_sub').style.left=POSITIONCORRECTION + 'px';
                            peel.style.width='WIDTHSMALLpx';
                            peel.style.height='HEIGHTSMALLpx';
                            peel.style.zIndex=0;

                      }
                      </script>";


    const WINDOW_PREVIEW = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                                                   <html xmlns="http://www.w3.org/1999/xhtml">
                                                   <head><title></title></head>
                                                   <body>BODY_PLACE</body></html>';

    const TYPE_LEFT = 'L';
    const TYPE_LEFT_AND_RIGHT = 'LR';
    const TYPE_RIGHT = 'R';

    protected function getBannerCode(Pap_Common_User $user, $flags) {
        return $this->getComleteCode($user,$flags);
    }

    public function getPreview(Pap_Common_User $user) {
        return '<iframe src="'.
        Pap_Tracking_BannerViewer::getBannerScriptUrl('preview',$this->getId(),$this->getChannelId(),$this->getParentBannerId())
        .'" width = "'.($this->getWidth()+15).'" height= "'.($this->getHeight()+15).'" frameborder=0></iframe>';
    }

    public function getWindowPreview(){
        return str_replace('BODY_PLACE', $this->getComleteCode(), self::WINDOW_PREVIEW);
    }

    private function getImpressionCode(Pap_Common_User $user = null){
        if($user === null){
            return $this->getEmptyImg();
        }
        $src = Pap_Tracking_ImpressionTracker::getInstance()->getSrcCode($this, $user, $this->channel);
        $src = str_replace('&amp;','&',$src);
        return $src;
    }

    private function getComleteCode(Pap_Common_User $user = null,$flags =null){
        $type = $this->getData2();
        if($type === self::TYPE_LEFT_AND_RIGHT){
            return $this->getHTMLCode(self::TYPE_LEFT, $user, $flags) . $this->getHTMLCode(self::TYPE_RIGHT, $user, $flags);
        }
        return $this->getHTMLCode($type, $user, $flags);
    }

    private function getHTMLCode($type, Pap_Common_User $user = null, $flags =null){
        $code =  str_replace(
        array('TYPE','WIDTHBIG', 'WIDTHSMALL', 'POSITIONCORRECTION', 'HEIGHTBIG','HEIGHTSMALL',
        'SMALLPERCENTAGE','WINDOW','SCROLLTEXT','BIGTEXT','BIGIMG','SMALLIMG',
        'BGCOLOR','URL','SIDE', 'OPOSITE' ,'TRACKIMG'),

        array($type,$this->getWidth(), $this->getWidthSmall(),
        $this->getPositionCorrection($type),$this->getHeight(),$this->getHeightSmall(),
        $this->getPercentage(),$this->getData(1),urlencode($this->getData(4)),
        urlencode($this->getData(5)), $this->getData(6),$this->getData(7),
        '0x'.$this->getData(3),$this->getSwfUrl(),$this->getSide($type), $this->getOpositeSide($type),
        $this->getImpressionCode($user)),
        self::HTML . self::JAVASCRIPT);

        if($user === null){
            return str_replace('{$targeturl_encoded}', urlencode($this->getDestinationUrl()), $code);
        }
        return $this->replaceUrlConstants($code, $user, $flags, '');
    }

    private function getPositionCorrection($type) {
        if($type === self::TYPE_LEFT){
            return 0;
        }
        return $this->getWidthSmall()-$this->getWidth();
    }

    private function getWidthSmall() {
        $imgFileName = $this->getData(7);
        if (($size = @getimagesize(urldecode($imgFileName))) !== false) {
            return $size[0];
        }
        return 100;
    }

    private function getHeightSmall() {
        $imgFileName = $this->getData(7);
        if (($size = @getimagesize(urldecode($imgFileName))) !== false) {
            return $size[1];
        }
        return 100;
    }

    private function getPercentage() {
        return intval(($this->getWidthSmall()/$this->getWidth())*100);
    }

    private function getEmptyImg(){
        return Gpf_Paths::getInstance()->getFullScriptsUrl() . 'pix.gif';
    }

    private function getSwfUrl(){
        return Gpf_Paths::getInstance()->getFullBaseServerUrl()
        . "include/Pap/Features/PagePeelBanner/EdgeBanner/edgeBanner.swf";
    }

    private function getSide($type){
        if($type === self::TYPE_LEFT){
            return 'left';
        }
        return 'right';
    }

    private function getOpositeSide($type){
        if($type !== self::TYPE_LEFT){
            return 'left';
        }
        return 'right';
    }

    public function getImageUrl() {
        return $this->getData(6);
    }

    protected function setDetectedSize($size){
        $this->setData(8, $size);
    }

    protected function getDetectedSize(){
        return $this->getData(8);
    }

    protected function setUndefinedSize(){
        parent::setUndefinedSize();
        if ($this->width == null) {
            $this->width = 1;
        }
        if ($this->height == null) {
            $this->height = 1;
        }
    }
}
?>
