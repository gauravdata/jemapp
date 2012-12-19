<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Word.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Word extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Words::getInstance());
        parent::init();
    }

    public function insert() {
        $this->set('wordlength', strlen($this->get('wordtext')));
        $qgrams = Gpf_Search_SearchEngine::getQGramPairsArray($this->get('wordtext'));
        foreach ($qgrams as $id => $qgram) {
        	if ($id < Gpf_Search_SearchEngine::QGRAM_COUNT) {
        	    $this->set('w' . ($id+1), $qgram);
        	}
        }
        parent::insert();
    }
}
