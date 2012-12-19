<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
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

class Pap_Api_Affiliate extends Pap_Api_Object {

    private $dataValues = null;
    private $equalsFields = array();

    public function __construct(Gpf_Api_Session $session) {
        if($session->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            $this->class = "Pap_Affiliates_Profile_PersonalDetailsForm";
        } else {
            $this->class = "Pap_Signup_AffiliateForm";
        }
         
        parent::__construct($session);

        $this->addEqualField('username');
         
        $this->getDataFields();
    }

    private function addEqualField($name) {
        $this->equalsFields[] = $name;
    }

    private function getEqualFields() {
        return $this->equalsFields;
    }

    public function getUserid() { return $this->getField("userid"); }
    public function setUserid($value) {
        $this->setField("userid", $value);
        $this->setField("Id", $value);
    }

    public function getRefid() { return $this->getField("refid"); }
    public function setRefid($value) { $this->setField("refid", $value); }

    public function getStatus() { return $this->getField("rstatus"); }
    public function setStatus($value) { $this->setField("rstatus", $value); }

    public function getMinimumPayout() { return $this->getField("minimumpayout"); }
    public function setMinimumPayout($value) { $this->setField("minimumpayout", $value); }

    public function getPayoutOptionId() { return $this->getField("payoutoptionid"); }
    public function setPayoutOptionId($value) { $this->setField("payoutoptionid", $value); }

    public function getNote() { return $this->getField("note"); }
    public function setNote($value) { $this->setField("note", $value); }

    public function getPhoto() { return $this->getField("photo"); }
    public function setPhoto($value) { $this->setField("photo", $value); }

    public function getUsername() { return $this->getField("username"); }
    public function setUsername($value) { $this->setField("username", $value); }

    public function getPassword() { return $this->getField("rpassword"); }
    public function setPassword($value) { $this->setField("rpassword", $value); }

    public function getFirstname() { return $this->getField("firstname"); }
    public function setFirstname($value) { $this->setField("firstname", $value); }

    public function getLastname() { return $this->getField("lastname"); }
    public function setLastname($value) { $this->setField("lastname", $value); }

    public function getParentUserId() { return $this->getField("parentuserid"); }
    public function setParentUserId($value) { $this->setField("parentuserid", $value); }

    public function getIp() { return $this->getField("ip"); }
    public function setIp($value) { $this->setField("ip", $value); }

    public function getNotificationEmail() { return $this->getField("notificationemail"); }
    public function setNotificationEmail($value) { $this->setField("notificationemail", $value); }

    public function enableCreateSignupReferralCommissions() { $this->setField("createSignupReferralComm", Gpf::YES); }

    public function getData($index) {
        $this->checkIndex($index);
        return $this->getField("data$index");
    }
    public function setData($index, $value, $operator = 'L') {
        $this->checkIndex($index);
        $this->setField("data$index", $value);
        if ($operator == '=') {
            $this->addEqualField('data' . $index);
        }
    }

    public function setPayoutOptionField($code, $value) {
        $this->setField($code, $value);
    }

    public function getDataName($index) {
        $this->checkIndex($index);
        $dataField = "data$index";
         
        if(!is_array($this->dataValues) || !isset($this->dataValues[$dataField])) {
            return '';
        }
         
        return $this->dataValues[$dataField]['name'];
    }

    public function getDataStatus($index) {
        $this->checkIndex($index);
        $dataField = "data$index";
         
        if(!is_array($this->dataValues) || !isset($this->dataValues[$dataField])) {
            return 'U';
        }
         
        return $this->dataValues[$dataField]['status'];
    }

    public function sendConfirmationEmail() {
        $params = new Gpf_Rpc_Params();
        $params->add('ids', array($this->getUserid()));
        return $this->sendActionRequest('Pap_Merchants_User_AffiliateForm', 'sendSignupConfirmation', $params);
    }

    /**
     * @param $campaignID
     * @param $sendNotification
     */
    public function assignToPrivateCampaign($campaignID, $sendNotification = false) {
        $params = new Gpf_Rpc_Params();
        $params->add('campaignId', $campaignID);
        $params->add('sendNotification', ($sendNotification ? Gpf::YES : Gpf::NO));
        $params->add('ids', array($this->getUserid()));
        return $this->sendActionRequest('Pap_Db_UserInCommissionGroup', 'addUsers', $params);
    }

    private function checkIndex($index) {
        if(!is_numeric($index) || $index > 25 || $index < 1) {
            throw new Exception("Incorrect index '$index', it must be between 1 and 25");
        }
         
        return true;
    }

    protected function fillEmptyRecord() {
        $this->setField("userid", "");
        $this->setField("agreeWithTerms", Gpf::YES);
    }

    protected function getPrimaryKey() {
        return "userid";
    }

    protected function getGridRequest() {
        return new Pap_Api_AffiliatesGrid($this->getSession());
    }

    protected function fillFieldsToGridRequest($request) {
        foreach(parent::getFields() as $field) {
            if($field->get(self::FIELD_VALUE) != '') {
                $operator = 'L';
                if (in_array($field->get(self::FIELD_NAME), $this->getEqualFields())) {
                    $operator = '=';
                }
                $request->addFilter($field->get(self::FIELD_NAME), $operator, $field->get(self::FIELD_VALUE));
            }
        }
    }

    /**
     * retrieves names and states of data1..data25 fields
     *
     */
    protected function getDataFields() {
        $request = new Gpf_Rpc_RecordsetRequest("Gpf_Db_Table_FormFields", "getFields", $this->getSession());
        $request->addParam("formId","affiliateForm");
        $request->addParam("status","M,O");
         
        try {
            $request->sendNow();
        } catch(Exception $e) {
            throw new Exception("Cannot load datafields. Error: ".$e->getMessage());
        }
         
        $recordset = $request->getRecordSet();
        $this->dataValues = array();
        foreach($recordset as $record) {
            $this->dataValues[$record->get("code")]['name'] = $record->get("name");
            $this->dataValues[$record->get("code")]['status'] = $record->get("status");
        }
    }

    private function sendActionRequest($className, $method, Gpf_Rpc_Params $params) {
        $request = new Gpf_Rpc_ActionRequest($className, $method, $this->getSession());
        $request->setParams($params);
        return $request->sendNow();
    }

    protected function beforeCallRequest(Gpf_Rpc_FormRequest $request) {
        $request->addParam('isFromApi', Gpf::YES);
    }
}
?>
