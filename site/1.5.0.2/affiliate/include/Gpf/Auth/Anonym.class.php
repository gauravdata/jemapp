<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Anonym.class.php 26591 2009-12-16 12:01:23Z mbebjak $
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
class Gpf_Auth_Anonym extends Gpf_Auth_User {

    public function isLogged() {
        return false;
    }

    public function getAccountId() {
        if ($this->accountid === null) {
            throw new Gpf_Exception("No accountId defined for Anonym user");
        }
        return parent::getAccountId();
    }

    public function init() {
        $this->theme = '';

        parent::init();
    }

    public function setTheme($themeId) {
        $this->theme = $themeId;
    }

    public function getUserId() {
        throw new Gpf_Exception("No userId defined for Anonymous user");
    }
    
    public function isExists() {
    	return true;
    }
}

?>
