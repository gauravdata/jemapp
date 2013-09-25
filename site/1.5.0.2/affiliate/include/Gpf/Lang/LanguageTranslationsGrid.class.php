<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Dictionary.class.php 19083 2008-07-10 16:32:14Z aharsani $
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
class Gpf_Lang_LanguageTranslationsGrid extends Gpf_View_CsvGridService implements Gpf_View_Grid_HasRowFilter {
    private $languageId;

    protected function initViewColumns() {
        $this->addViewColumn('id', $this->_("Source message"), true);
        $this->addViewColumn('translation', $this->_("Translation"), true);
        $this->addViewColumn('type', $this->_("Type"), true);
        $this->addViewColumn('module', $this->_("Module"), true);
        $this->addViewColumn('status', $this->_("Status"), true);
        $this->addViewColumn('customer', $this->_("Is custom"), true);
    }

    protected function initDataColumns() {
        //source;translation;type;module;status;customer
        $this->setKeyDataColumn('source');
        $this->addDataColumn('translation', 'translation');
        $this->addDataColumn('type', 'type');
        $this->addDataColumn('module', 'module');
        $this->addDataColumn('status', 'status');
        $this->addDataColumn('customer', 'customer');
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('id', '80px', 'A');
        $this->addDefaultViewColumn('translation', '80px', 'N');
        $this->addDefaultViewColumn('status', '20px', 'N');
        $this->addDefaultViewColumn('customer', '20px', 'N');
    }

    public function filterRow(Gpf_Data_Row $row) {
        if ($row->get('type') == Gpf_Lang_Parser_Translation::TYPE_METADATA) {
            return null;
        }

        $search = $this->filters->getFilter('search');
        if (sizeof($search) == 1) {
            $searchString = $search[0]->getValue();
            if ((strpos($row->get('source'), $searchString) === false) && (strpos($row->get('translation'), $searchString) === false)) {
                return null;
            }
        }

        return parent::filterRow($row);
    }

    /**
     * @service language read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $language = new Gpf_Db_Language();
        $language->setId($params->get(Gpf_Db_Table_Languages::ID));
        $language->load();
        
        $filters = $params->get('filters');
        $filters[0][2] = htmlspecialchars($filters[0][2]);
        $params->set('filters',$filters); 
        
        $this->setCsvReader(new Gpf_Io_Csv_Reader(Gpf_Lang_CsvLanguage::getAccountCsvFileName($language->getCode())));
        return parent::getRows($params);
    }
}
?>
