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
class Pap_Common_UserFields extends Gpf_Object  {

    /**
     * @var Pap_Common_User
     */
    private $user;
    /**
     * @var instance
     */
    static private $instance = null;
    static private $userFields = null;
    private $countryCodeFields = null;

    /*
     * TODO: this cache needs to be refatored
     */
    private $cache = array();

    private function __construct() {
        $this->user = null;
    }

    /**
     * returns instance of UserFields class
     *
     * @return Pap_Common_UserFields
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Pap_Common_UserFields();
        }
        return self::$instance;
    }

    /**
     * returns array of user fields
     *
     * @return unknown
     */
    public function getUserFields($type = array('M', 'O', 'R'), $mainFields = null) {
        if (is_array(self::$userFields)) {
            return self::$userFields;
        }
        $ffTable = Gpf_Db_Table_FormFields::getInstance();
        $formFields = $ffTable->getFieldsNoRpc("affiliateForm", $type, $mainFields);

        self::$userFields = array();
        self::$userFields['firstname'] = $this->_('First Name');
        self::$userFields['lastname'] = $this->_('Last Name');
        self::$userFields['username'] = $this->_('Username');
        self::$userFields['password'] = $this->_('Password');
        self::$userFields['ip'] = $this->_('IP');
        self::$userFields['photo'] = $this->_('Photo');

        foreach($formFields as $record) {
            $code = $record->get('code');
            $name = $record->get('name');
            self::$userFields[$code] = $name;
        }

        return self::$userFields;
    }

    /**
     * @service affiliate read
     */
    public function getVariablesRpc(Gpf_Rpc_Params $params) {
        return new Gpf_Rpc_Map(self::getUserFields());
    }

    public function getUserFieldsValues($mainFields = null) {
        if($this->user == null) {
            throw new Gpf_Exception("You have to set User before getting user fields value!");
        }

        if (array_key_exists($this->user->getId(), $this->cache)) {
            return $this->cache[$this->user->getId()];
        }

        $fields = $this->getUserFields(array('M', 'O', 'R', 'D'), $mainFields);
        $result = array();
        foreach($fields as $code => $name) {
            $result[$code] = $this->getUserFieldValue($code);
        }
        $this->cache[$this->user->getId()] = $result;
        return $result;
    }

    public function getUserFieldValue($code) {
        if($this->user == null) {
            throw new Gpf_Exception("You have to set User before getting user fields value!");
        }

        if($code == 'refid') {
            return $this->user->getRefId();
        } else if($code == 'firstname') {
            return $this->user->getFirstName();
        } else if($code == 'lastname') {
            return $this->user->getLastName();
        } else if($code == 'password') {
            return $this->user->getPassword();
        } else if($code == 'username') {
            return $this->user->getUserName();
        } else if($this->isCountryCode($code)){
            return $this->getCountryName($this->user->get($code));
        }
        try {
            return $this->user->get($code);
        } catch (Gpf_Exception $e) {
        }
        return '';
    }

    public function setUser(Pap_Common_User $user) {
        $this->user = $user;
    }

    public function loadUserById($userId) {
    }

    /**
     * replaces user fields values in standard format (${#data1#)
     * with their values
     *
     * @param string $text
     */
    public function replaceUserConstantsInText($text, $mainFields = null) {
        $values = $this->getUserFieldsValues($mainFields);

        // simple replace
        foreach($values as $code => $value) {
            $text = self::replaceCustomConstantInText($code, $value, $text);
        }

        return $text;
    }

    /**
     * removes user fields values in standard format (${#data1#)
     *
     * @param string $text
     */
    public function removeUserConstantsInText($text) {
        $fields = $this->getUserFields();
        foreach($fields as $code => $value) {
            $text = self::replaceCustomConstantInText($code, '', $text);
        }

        return $text;
    }

    /**
     * remove constants in standard format {*some comment*}
     * @throws Gpf_Exception
     *
     * @param string $text
     */
    public static function removeCommentsInText($text) {
        while ($indexOfStartTag = strpos($text, '{*')) {
            $start = substr($text, 0, $indexOfStartTag);
            $indexOfEndTag = strpos($text, '*}');
            if (!$indexOfEndTag) {
                throw new Gpf_Exception("Comment not closed !");
            }
            $end = substr($text, $indexOfEndTag+2);
            $text = $start.$end;
        }
        return $text;
    }

    public static function replaceCustomConstantInText($code, $value, $text) {
        return str_replace('{$'.$code.'}', $value, $text);
    }

    protected function getCountryName($country_code){
        if($country_code == null) {
            return '';
        }
        $country = new Gpf_Db_Country();
        $country->setCountryCode($country_code);
        try {
            $country->loadFromData();
            return Gpf_Lang::_localizeRuntime($country->getCountry());
        } catch (Gpf_Exception $e) {
            return '';
        }
    }

    private function isCountryCode($code){
        if ($this->countryCodeFields == null) {
            $this->loadCountryCodeFields();
        }
        return in_array($code, $this->countryCodeFields);
    }

    private function loadCountryCodeFields() {
        $this->countryCodeFields = array();
        foreach ($this->getFormFields() as $row){
            if( ($row->getType() == Gpf_Db_FormField::TYPE_COUNTRY_LISTBOX ||
            $row->getType() == Gpf_Db_FormField::TYPE_COUNTRY_LISTBOX_GWT)) {
                $this->countryCodeFields[] = $row->getCode();
            }
        }
    }

    /**
     * @return Gpf_DbEngine_Row_Collection
     */
    protected function getFormFields() {
        $field  = new Gpf_Db_FormField();
        $field->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
        $field->setFormId('affiliateForm');
        return $field->loadCollection();
    }
}

?>
