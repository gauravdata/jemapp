<?php
class AW_Colorswatches_Model_Resource_Swatch_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('awcolorswatches/swatch');
    }

    /**
     * @param int $attributeId
     *
     * @return $this
     */
    public function addAttributeIdFilter($attributeId)
    {
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attributeId)
        ;
        $this->addFieldToFilter(
            'main_table.option_id', array('in' => $optionCollection->getAllIds())
        );
        return $this;
    }

    public function addOrderByPosition() {
        $this->getSelect()
            ->join(
                array('attr_option' => Mage::getSingleton('core/resource')->getTableName('eav/attribute_option')),
                'main_table.option_id = attr_option.option_id'
            )
            ->order('attr_option.sort_order ASC');

        return $this;
    }
}