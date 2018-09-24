<?php

class AW_Colorswatches_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Colorswatch_Images
    extends Mage_Adminhtml_Block_Template
{
    protected $_template = 'aw_colorswatches/catalog/product/attribute/edit/tab/colorswatch/images.phtml';
    protected $_swatchAttribute = null;

    /**
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttribute()
    {
        return Mage::registry('entity_attribute');
    }

    /**
     * @return AW_Colorswatches_Model_Swatchattribute
     */
    public function getSwatchAttribute()
    {
        if (null === $this->_swatchAttribute) {
            $this->_swatchAttribute = Mage::getModel('awcolorswatches/swatchattribute')->loadByAttributeId(
                $this->getAttribute()->getId()
            );
        }
        return $this->_swatchAttribute;
    }

    /**
     * @return string
     */
    public function getFileUploadUrl()
    {
        return $this->getUrl('adminhtml/awcolorswatches_attribute/ajaxFileUpload');
    }

    /**
     * @return array
     */
    public function getImageRelations()
    {
        $result = array();
        foreach($this->getAttribute()->getSource()->getAllOptions() as $option) {
            $optionId = intval($option['value']);
            if ($optionId <= 0) {
                continue;
            }
            $result[$optionId] = null;
        }
        /** @var AW_Colorswatches_Model_Resource_Swatch_Collection $swatchCollection */
        $swatchCollection = $this->getSwatchAttribute()->getSwatchCollection();
        foreach($swatchCollection as $item) {
            if (!$item->getImage()) {
                continue;
            }
            $result[$item->getOptionId()] = AW_Colorswatches_Helper_Image::resizeImage($item->getImage(), 100, 100);
        }
        return $result;
    }

    public function getOptionsConfig()
    {
        $options = $this->getAttribute()->getSource()->getAllOptions();
        $relations = $this->getImageRelations();
        $result = array();
        foreach ($options as $option) {
            $optionId = intval($option['value']);
            if ($optionId <= 0) {
                continue;
            }
            $result[] = array(
                'id'    => $optionId,
                'title' => $option['label'],
                'value' => $relations[$optionId],
            );
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getUploadNoteText()
    {
        $noteList = array(
            $this->__('Max file size is %s', AW_Colorswatches_Helper_Data::getMaximumFileUploadSize())
        );
        return join(', ', $noteList);
    }
}