<?php

class Biztech_Translator_Block_Adminhtml_Translator_TranslateGrid extends Mage_Adminhtml_Block_Template {

    protected $_results;

    public function __construct() {
        parent::__construct();
    }

    public function setResults($result) {
        $this->_results = $result;

        return $this;
    }

    public function getResults() {
        return $this->_results;
    }

    public function getRequestParams($extraParams) {
        $params = Mage::app()->getRequest()->getParams();

        foreach ($extraParams as $key => $extraParam) {
            $params[$key] = $extraParam;
        }
        unset($params['key']);
        unset($params['isAjax']);
        unset($params['form_key']);
        unset($params['searchString']);
        return $params;
    }

}