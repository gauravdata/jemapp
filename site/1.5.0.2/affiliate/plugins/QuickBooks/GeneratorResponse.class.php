<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Juraj Simon
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */

class QuickBooks_GeneratorResponse extends Gpf_Object  {
    // TODO - IIF Export Format - easy solution for adding other export button than CSV, if needed other formats need to refactor (iif not supported anymore, use webConnector instead!)

    const DELIMITER = "\t";

    const VEND_BEGIN = 'VEND';
    const TRNS_BEGIN = 'TRNS';
    const TRNS_END = 'ENDTRNS';
    const SPL = 'SPL';
    const ACCNT = 'ACCNT';

    const VEND_HEADER = "NAME\tREFNUM\tADDR1\tADDR2\tADDR3\tADDR4\tFIRSTNAME\tLASTNAME\tEMAIL\tPHONE1\tVTYPE\tCOMPANYNAME";
    const TRNS_HEADER = "TRNSID\tTRNSTYPE\tDATE\tACCNT\tNAME\tAMOUNT\tDOCNUM\tMEMO\tTOPRINT";
    const SPL_HEADER = "SPLID\tTRNSTYPE\tDATE\tACCNT\tNAME\tAMOUNT\tDOCNUM\tMEMO\tQNTY";
    const ACCNT_HEADER = "NAME\tACCNTTYPE";

    const TRNS_TYPE = 'BILL';

    private $fileName;
    /**
     * @var Gpf_Data_RecordSet
     */
    private $iif;
    /**
     * @var array
     */
    private $users;
    private $buffer;

    /**
     * Generate CSV file to output from RecordSet $cvs
     *
     * @param String $fileName
     * @param Gpf_Data_RecordSet $iif
     */
    function __construct($fileName, $iif, $users) {
        $this->fileName = $fileName;
        $this->iif = $iif;
        $this->users = $users;
        $this->buffer = "";
    }

    public function generateFile() {
        $this->buildData();
        $file = new Gpf_File_Download_String($this->fileName, $this->buffer);
        $file->setAttachment(true);
        return $file;
    }

    private function getAccountDefinition() {
        $accountName = Gpf_Settings::get(QuickBooks_Config::ACCOUNT_NAME);
        $accountType = Gpf_Settings::get(QuickBooks_Config::ACCOUNT_TYPE);
        return self::ACCNT.self::DELIMITER.$accountName.self::DELIMITER.$accountType;
    }

    private function buildAccountData() {
        $this->buffer .= '!' . self::ACCNT . self::DELIMITER . self::ACCNT_HEADER .$this->addDelimiter(10). "\n";
        $this->buffer .= $this->getAccountDefinition() .$this->addDelimiter(10). "\n";
    }

    private function buildUsersData() {
        //ADDR1: companyname
        //ADDR2: firstname lastname
        //ADDR3: street number
        //ADDR4: city state ZIP code
        $this->buffer .= '!' . self::VEND_BEGIN . self::DELIMITER . self::VEND_HEADER . "\n";
        $userCounter = 1;
        foreach ($this->users as $user) {
            $email = (($user['notificationemail'] !== null && $user['notificationemail'] !== '') ? $user['notificationemail'] : $user['username']);
            $this->buffer .= self::VEND_BEGIN . self::DELIMITER . $user['firstname'] . ' ' . $user['lastname'] . self::DELIMITER . $userCounter . self::DELIMITER . $user['data3'] . self::DELIMITER . $user['firstname'] . ' ' . $user['lastname'] . self::DELIMITER . $user['data4'] . self::DELIMITER . $user['data5'] . ' ' . $user['data6'] . ' ' . $user['data8'] . self::DELIMITER . $user['firstname'] . self::DELIMITER . $user['lastname'] . self::DELIMITER . $email . self::DELIMITER . $user['data9'] . self::DELIMITER . 'Affiliate' . self::DELIMITER . $user['data3'] . "\n";
            $userCounter ++;
        }
    }

    private function buildTransactionsData() {
        $this->buffer .= '!' . self::TRNS_BEGIN . self::DELIMITER . self::TRNS_HEADER . $this->addDelimiter(3) . "\n";
        $this->buffer .= '!' . self::SPL . self::DELIMITER . self::SPL_HEADER . $this->addDelimiter(3) . "\n";
        $this->buffer .= '!' . self::TRNS_END .$this->addDelimiter(12). "\n";
         
        foreach ($this->iif as $record) {
            $users = $record->get('users');
            foreach ($users[$record->get('payouthistoryid')]['users'] as $user) {
                $counter  = 1;
                $this->buffer .= self::TRNS_BEGIN . self::DELIMITER . $record->get('payouthistoryid') .
                self::DELIMITER . self::TRNS_TYPE . self::DELIMITER .
                strftime('%m/%d/%Y', strtotime($record->get('dateinserted'))) . self::DELIMITER .
                Gpf_Settings::get(QuickBooks_Config::TRNS_ACCOUNT_TYPE) . self::DELIMITER .
                $user['firstname'] . ' ' . $user['lastname'] . self::DELIMITER .
                $user['amount']*(-1) . self::DELIMITER .
                $record->get('payouthistoryid') . self::DELIMITER .
                $record->get('merchantnote') . self::DELIMITER .
                Gpf_Settings::get(QuickBooks_Config::TRNS_TOPRINT) .
                $this->addDelimiter(3). "\n";

                $this->buffer .= self::SPL . self::DELIMITER .
                $counter . self::DELIMITER .
                self::TRNS_TYPE .  self::DELIMITER .
                strftime('%m/%d/%Y', strtotime($record->get('dateinserted'))) . self::DELIMITER .
                Gpf_Settings::get(QuickBooks_Config::SPL_ACCOUNT_TYPE) . self::DELIMITER .
                $user['firstname'] . ' ' . $user['lastname'] . self::DELIMITER .
                $user['amount'] . self::DELIMITER .
                $counter . self::DELIMITER .
                $record->get('affiliatenote') . self::DELIMITER .
                $this->addDelimiter(3). "\n";

                $this->buffer .= self::TRNS_END .$this->addDelimiter(12). "\n";
            }
        }
    }

    private function buildData() {
        if (Gpf_Settings::get(QuickBooks_Config::ADD_ACCOUNT) == Gpf::YES) {
            $this->buildAccountData();
        }

        $this->buildUsersData();

        $this->buildTransactionsData();
    }

    private function addDelimiter($count = 1) {
        $value = '';
        for ($i = 0; $i<$count; $i++) {
            $value .= self::DELIMITER;
        }
        return $value;
    }

    private function encode($array) {
        for ($i = 0; $i < count($array); $i++) {
            if (strpos($array[$i], "\"")) {
                $array[$i] = str_replace("\"", "\"\"", $array[$i]);
                $array[$i] = "\"$array[$i]\"";
            } elseif (strpos($array[$i], self::DELIMITER)) {
                $array[$i] = "\"$array[$i]\"";
            }
        }
        $this->buffer .= implode(self::DELIMITER, $array)."\n";
    }
}
