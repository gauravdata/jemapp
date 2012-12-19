<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.\n
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.11
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
class Gpf_CPanel_CPanel extends Gpf_CPanel_Whm {

    const DISKQUOTA = 'diskquota';
    const DISKUSEDPERCENT = 'diskusedpercent';
    const DISKUSED = 'diskused';
    const HUMANDISKQUOTA = 'humandiskquota';
    const LOGIN = 'login';
    const EMAIL = 'email';
    const DOMAIN = 'domain';
    const USER = 'user';
    const HUMANDISKUSED = 'humandiskused';
    function __construct($host, $user, $passwd, $useSsl = true, $port = 2083) {
        parent::__construct($host, $user, $passwd, $useSsl, $port);
    }


    /**
     * Add Pop3 account under Cpanel account
     *
     * @param $domain
     * @param $email
     * @param $password
     * @param $quota
     * @return unknown_type
     */
    public function addPop3($popDomain, $email, $password, $quota = '250') {
        $xml = $this->execute('/xml-api/cpanel', array(
        'cpanel_xmlapi_version'=>2,
        'cpanel_xmlapi_module' => 'Email',
        'cpanel_xmlapi_func' => 'addpop',
        'email' => $email,
        'password' => $password,
        'quota' => $quota,
        'domain' => $popDomain,
        'cache_fix' => rand()
        ));

        return true;
    }

    /**
     * Delete Pop3 account under Cpanel account
     *
     * @param $domain
     * @param $email
     * @return unknown_type
     */
    public function deletePop3($popDomain, $email) {
        $xml = $this->execute('/xml-api/cpanel', array(
        'cpanel_xmlapi_version'=>2,
        'cpanel_xmlapi_module' => 'Email',
        'cpanel_xmlapi_func' => 'delpop',
        'email' => $email,
        'domain' => $popDomain,
        'cache_fix' => rand()
        ));

        return true;
    }

    /**
     * 
     * @return Gpf_Data_RecordSet
     */
    public function listPop3WithDisk($popDomain, $nearquotaonly = null, $no_validate = null, $regex = null) {
        $xml = $this->execute('/xml-api/cpanel', array(
        'cpanel_xmlapi_version'=>2,
        'cpanel_xmlapi_module' => 'Email',
        'cpanel_xmlapi_func' => 'listpopswithdisk',
        'domain' => $popDomain,
        'nearquotaonly' => $nearquotaonly,
        'no_validate' => $no_validate,
        'regex' => $regex, 
        'cache_fix' => rand()
        ));

        $result = new Gpf_Data_RecordSet();
        $result->setHeader(array(self::DISKQUOTA ,self::DISKUSEDPERCENT, self::DISKUSED, self::HUMANDISKQUOTA, self::LOGIN, self::EMAIL, self::DOMAIN, self::USER, self::HUMANDISKUSED));
        foreach($xml->data as $row) {
            $record = $result->createRecord();
            $record->add(self::DISKQUOTA, (string)$row->diskquota);
            $record->add(self::DISKUSEDPERCENT, (string)$row->diskusedpercent);
            $record->add(self::DISKUSED, (string)$row->diskused);
            $record->add(self::HUMANDISKQUOTA, (string)$row->humandiskquota);
            $record->add(self::LOGIN, (string)$row->login);
            $record->add(self::EMAIL, (string)$row->email);
            $record->add(self::DOMAIN, (string)$row->domain);
            $record->add(self::USER, (string)$row->user);
            $record->add(self::HUMANDISKUSED, (string)$row->humandiskused);
            $result->addRecord($record);
        }

        return $result;
    }
}

?>
