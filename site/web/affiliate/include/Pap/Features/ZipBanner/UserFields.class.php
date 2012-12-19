<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: User.class.php 18993 2008-07-07 08:20:50Z mjancovic $
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
class Pap_Features_ZipBanner_UserFields extends Gpf_Object {
    const BANNERID = 'bannerid';
    const CHANNELID = 'channelid';
    const TARGETURL = 'targeturl';
    const TARGETURL_ENC = 'targeturl_encoded';
    const IMPRESS_TRACK = 'impression_track';
    
    /**
     * @var Pap_Common_User
     */
    private $user;
    
    /**
     *
     * @var Pap_Common_UserFields
     */
    private $fields;
    
    
    public function __construct() {
        $this->user = null;
        $this->fields = Pap_Common_UserFields::getInstance();
    }
    
    /**
     * @service affiliate read
     */
    public function getVariablesRpc(Gpf_Rpc_Params $params) {
      $list = $this->fields->getUserFields();
      $list[self::BANNERID] =  $this->_('Banner Id');
      $list[self::CHANNELID] =  $this->_('Channel Id');
      $list[self::TARGETURL] =  $this->_('Target URL');
      $list[self::TARGETURL_ENC] =  $this->_('Encoded target URL');
      $list[self::IMPRESS_TRACK] =  $this->_('Impression track');
      return new Gpf_Rpc_Map($list);
    }
    
    public function setUser(Pap_Common_User $user) {
        $this->fields->setUser($user);
    }
    
    public function replaceUserConstantsInText($content) {
        return $this->fields->replaceUserConstantsInText($content);
    }
    
    public function replaceCustomConstantInText($code, $value, $context) {
        return Pap_Common_UserFields::replaceCustomConstantInText($code, $value, $context);
    }
}
