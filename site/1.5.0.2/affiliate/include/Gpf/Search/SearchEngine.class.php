<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: SearchEngine.class.php 23410 2009-02-06 08:12:30Z vzeman $
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
class Gpf_Search_SearchEngine extends Gpf_Object implements Gpf_Search_Model_DataSet {
    const QGRAM_COUNT = 16;
    protected $query;
    protected $pageNr;

    public function __construct($query) {
        $this->query = $query;
    }

    /**
     * Genrates array of qgrams from input word
     *
     * @param string $string
     * @return array
     */
    public static function getQGramPairsArray($string) {
        $ret = array();
        $string = trim($string);
        for ($i=0; $i < self::strlen($string); $i++) {
            if ($i > self::QGRAM_COUNT-1) break;
            $value = self::substr($string, $i, 2);
            if (!self::strlen(trim($value))) break;
            $ret[] = $value;
        }
        if (self::strlen($ret[count($ret)-1]) == 1) $ret[count($ret)-1] .= '_';

        return $ret;
    }

    /**
     * Get QGram search string for match against SQL
     *
     * @param string $string
     * @return string
     */
    protected function getQGramPairsSearchString($string) {
        return implode(' ', $this->getQGramPairsArray($string));
    }


    /**
     * Returns array of words
     *
     * @param string $content
     * @return array(word => count)
     */
    protected static function getWords($content) {
        if (strlen($content)) {
            $content = self::strtolower(str_replace(self::getWordseparators(), ' ', $content));
            $arr = explode(' ', $content);

            $retArr = array();
            foreach ($arr as $val) {
                if (self::isValidWord($val)) {
                    $val = $val;
                    if (isset($retArr[$val])) {
                        $retArr[$val]++;
                    } else {
                        $retArr[$val] = 1;
                    }
                }
            }
            return $retArr;
        } else {
            return array();
        }
    }

    /**
     * Insert into database all words from array
     *
     * @param array(word => count) $wordArray
     */
    public static function createWords($content) {
        $ret = array();
        $wordArray = self::getWords($content);

        foreach ($wordArray as $word => $count) {
            $wordObject = new Gpf_Db_Word();
            $wordObject->set('wordtext', $word);
            try {
                $wordObject->insert();
            } catch (Gpf_Exception $e) {
                $wordObject->loadFromData(array('wordtext'));
            }

            if ($wordObject->isPersistent()) {
                $ret[$wordObject->get('wordid')] = $count;
            }
        }
        return $ret;
    }

    protected static function getWordSeparators() {
        return array('{', '}', '|', '=', '„', '+', '\\',
    	';', ',', '.', '<', '>', '?', '`', '~', '!', '#', '^', '&', '*',
    	'(', ')', '[', ']', '"', "'", ':', '/', "\n", "\t", "\r", "’", "“");
    }


    protected static function strtolower($val) {
        if (Gpf_Php::isFunctionEnabled('mb_strtolower')) {
            return mb_strtolower($val, 'UTF-8');
        } else {
            return strtolower($val);
        }
    }

    protected static function substr($val, $from, $len) {
        if (Gpf_Php::isFunctionEnabled('mb_substr')) {
            return mb_substr($val, $from, $len, 'UTF-8');
        } else {
            return substr($val, $from, $len);
        }
    }


    protected static function loadStopWords() {
        $arr = explode("\n", file_get_contents(Gpf_Paths::getInstance()->getAccountDirectoryPath() . 'stopwords.txt'));
        $ret = array();
        foreach ($arr as $word) {
            $word = trim($word);
            if (strlen($word)) $ret[] = trim($word);
        }

        return array_unique($ret);
    }

    protected static function isStopWord($word) {
        static $arrStopWords;
        if (empty($arrStopWords)) {
            $arrStopWords = self::loadStopWords();
        }
        return array_search(strtolower($word), $arrStopWords);
    }

    protected static function isValidWord($word) {
        return self::strlen(trim($word)) > 1 && !self::isStopWord($word);
    }

    protected static function strlen($word) {
        if (Gpf_Php::isFunctionEnabled('mb_strlen')) {
            return mb_strlen($word, 'UTF-8');
        } else {
            return strlen($word);
        }
    }

    protected function getMysqlMinWordLen() {
        static $min_len;

        if ($min_len == null) {
            $sql = "SHOW VARIABLES LIKE 'ft_min_word_len'";
            $sth = $this->createDatabase()->execute($sql);
            $row = $sth->fetchArray();
            $min_len = $row['Value'];
        }

        return $min_len;
    }

    /**
     *
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createSelect() {
        return null;
    }

    /**
     * execute search and return recordset of products
     *
     * @param string $query
     * @return Gpf_Data_RecordSet
     */
    public function getData() {
        return $this->buildSelect()->getAllRows();
    }

    /**
     *
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function buildSelect() {
        $select = $this->createSelect();
        $select->limit->set(($this->pageNr-1)*$this->getRecordsPerPage(),
        $this->getRecordsPerPage());
        return $select;
    }

    public function getAllCount() {
        try {
            $select = new Gpf_SqlBuilder_SelectBuilder();
            $select->select->add('count(*)');
            $select->from->addSubselect($this->createSelect(), 's');

            $sth = $this->createDatabase()->execute($select->toString());
            $row = $sth->fetchRow();
            return min(array($row[0], $this->getRecordsPerPage()*3));
        } catch (Exception $e) {
            return 0;
        }
    }

    public function setPage($pageNr) {
        $this->pageNr = $pageNr;
    }

    public function setSort($code) {
    }

    public function getSortOptions() {
        return array();
    }

    public function getRecordsPerPage() {
        return 20;
    }

    public function getRecordsName() {
        return $this->_('entries');
    }
}
