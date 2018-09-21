<?php
class AW_Colorswatches_Model_Resource_Swatch extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('awcolorswatches/swatch', 'swatch_id');
    }

    /**
     * @param int $optionId
     * @param int $storeId
     *
     * @return string
     */
    public function getOptionLabel($optionId, $storeId)
    {
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setStoreFilter($storeId)
        ;
        $optionCollection->getSelect()->where('main_table.option_id=?', $optionId);
        return $optionCollection->getFirstItem()->getValue();
    }
}