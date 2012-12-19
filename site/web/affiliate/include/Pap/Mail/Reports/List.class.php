<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
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
class Pap_Mail_Reports_List {
    
    /**
     * 
     * @var Pap_Mail_Reports_HasHtml
     */
    private $transactionList;

    public function __construct(Pap_Mail_Reports_HasHtml $transactionList) {
        $this->transactionList = $transactionList;
    }
    
    public function getList() {
        return $this->transactionList->getHtml(10);
    }
    
    protected function getValueNames() {
        return array('list');
    }
    
    public function __get($name) {
        if (in_array($name, $this->getValueNames())) {
            $method = "get" . strtoupper($name[0]) . substr($name, 1);
            return $this->$method();
        }
        return 'Undefined';
    }
}
?>
