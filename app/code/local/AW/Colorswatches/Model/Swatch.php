<?php
class AW_Colorswatches_Model_Swatch extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('awcolorswatches/swatch', 'swatch_id');
    }

    /**
     * @param int $optionsId
     *
     * @return AW_Colorswatches_Model_Swatch
     */
    public function loadByOptionId($optionsId)
    {
        return $this->load($optionsId, 'option_id');
    }

    /**
     * @return AW_Colorswatches_Model_Swatch
     */
    public function deleteImage()
    {
        if ($this->getData('image')) {
            AW_Colorswatches_Helper_Image::deleteImage($this->getData('image'));
        }
        $this->setData('image', '');
    }

    /**
     * @return string
     */
    public function getOptionLabel($storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        return $this->_getResource()->getOptionLabel($this->getOptionId(), $storeId);
    }
}