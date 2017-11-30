<?php
class AW_Storecredit_Model_Mysql4_History extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_serializableFields   = array(
        'additional_info' => array(null, array())
    );

    public function _construct()
    {
        $this->_init('aw_storecredit/history', 'history_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));
        return parent::_beforeSave($object);
    }
}