<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Context.class.php 18001 2008-05-13 16:05:33Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Common_BannerReplaceVariablesContext  {

    private $text;
    /**
     * @var Pap_Common_Banner
     */
    private $banner;
    /**
     * @var Pap_Common_User
     */
    private $user;
    
    public function __construct($text, Pap_Common_Banner $banner, Pap_Common_User $user = null) {
        $this->text = $text;
        $this->banner = $banner;
        $this->user = $user;
    }
    
    public function getText() {
        return $this->text;
    }
    
    public function setText($text) {
        $this->text = $text;
    } 
    
    /**
     * @return Pap_Common_Banner
     */
    public function getBanner() {
        return $this->banner;
    }
    
    /**
     * @return Pap_Common_User
     */
    public function getUser() {
        return $this->user;
    }
}
?>
