<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RichListBox.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Ui_RichListBox extends Gpf_Object implements Gpf_Rpc_Serializable {

    const REQUEST_CACHED = 'cachedRequest';
    const REQUEST_SEARCH = 'searchRequest';
    const REQUEST_ID = "idRequest";
    
    const REQUEST_TYPE = 'id';
    const FROM = 'from';
    const ROWS_PER_PAGE = 'rowsPerPage';
    const MAX_CACHED_COUNT = 'maxCachedCount';
    const SEARCH = 'search';

    private $requestType;
    /**
     * @var Gpf_Data_RecordSet
     */
    private $cachedData;
    private $size = 0;

    /**
     * @param $requestType
     * @param Gpf_Data_RecordSet $recordSet
     */
    function __construct($requestType, Gpf_Data_RecordSet $recordSet, $totalCount = null) {
        $this->requestType = $requestType;
        $this->getData($recordSet, $totalCount);
    }

    private function getData(Gpf_Data_RecordSet $recordSet, $totalCount) {
        if ($this->requestType !== self::REQUEST_ID) {
            $this->getCachedData($recordSet, $totalCount);
            return;
        }   
        $this->search($recordSet);
    }

    public function toObject() {
        $this->localizeRecordSet($this->cachedData);
        $response = new Gpf_Rpc_Object();
        $response->rows = $this->cachedData->toObject();
        $response->count = (int)$this->size;
       
        return $response;
    }

    public function toText() {
        throw new Gpf_Exception($this->_("Unsupported"));
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    private function localizeRecordSet(Gpf_Data_RecordSet $recordset) {
        foreach ($recordset as $record) {
            foreach ($record as $key => $value) {
                $record->set($key, $this->_localize($value));
            }
        }
        return $recordset;
    }
    

    private function search(Gpf_Data_RecordSet $recordSet) {
        $this->setData($recordSet);
    }

    private function setData(Gpf_Data_RecordSet $recordSet, $totalCount = null) {
        $this->cachedData = $recordSet;
        if ($totalCount == null) {
            $this->size = $this->cachedData->getSize();
            return;
        }
        $this->size = $totalCount;
    }

    private function getCachedData(Gpf_Data_RecordSet $recordSet, $totalCount) {
        $this->setData($recordSet, $totalCount);
    }
}

?>
