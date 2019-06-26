<?php

class Biztech_Translator_Model_Mysql4_Logcron extends Mage_Core_Model_Mysql4_Abstract
{


    public function _construct()
    {
        // Note that the auspost_id refers to the key field in your database table.
        $this->_init('translator/logcron', 'trans_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId() && !$object->getCronDate()) { //add an other constraint here
            $object->setCronDate(Mage::getSingleton('core/date')->gmtDate());
        }
        return $this;
    }

}