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
class Pap_Signup_SignupFormContext  {

    private $ip;

    /**
     * @var Gpf_Rpc_Form
     */
    private $form;
    private $row;

    private $allowSave;

    public function __construct($ip, Gpf_Rpc_Form $form, $row) {
        $this->ip = $ip;
        $this->form = $form;
        $this->row = $row;
        $this->allowSave = true;
    }

    public function setAllowSave($allow) {
        $this->allowSave = $allow;
    }

    public function isSaveAllowed() {
        return $this->allowSave;
    }

    /**
     * @return Gpf_Rpc_Form
     */
    public function getForm() {
        return $this->form;
    }

    public function getIp() {
        return $this->ip;
    }

    public function getRow() {
        return $this->row;
    }
}
?>
