<?php
class AW_Colorswatches_Model_Resource_Swatchattribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('awcolorswatches/swatchattribute');
    }

    /**
     * @param array $attributeIds
     *
     * @return AW_Colorswatches_Model_Resource_Swatchattribute_Collection
     */
    public function addAttributeIdsFilter($attributeIds)
    {
        return $this->addFieldToFilter('attribute_id', array('in' => $attributeIds));
    }

    /**
     * @return AW_Colorswatches_Model_Resource_Swatchattribute_Collection
     */
    public function addIsEnabledFilter()
    {
        return $this->addFieldToFilter('is_enabled', array('eq' => 1));
    }
}