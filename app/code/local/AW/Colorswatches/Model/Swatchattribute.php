<?php
class AW_Colorswatches_Model_Swatchattribute extends Mage_Core_Model_Abstract
{
    protected $_attributeModel = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('awcolorswatches/swatchattribute', 'entity_id');
    }

    /**
     * @param int $attributeId
     *
     * @return AW_Colorswatches_Model_Swatchattribute
     */
    public function loadByAttributeId($attributeId)
    {
        return $this->load($attributeId, 'attribute_id');
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttributeModel()
    {
        if (null === $this->_attributeModel) {
            $this->_attributeModel = Mage::getModel('eav/entity_attribute')->load($this->getAttributeId());
        }
        return $this->_attributeModel;
    }

    /**
     * @return AW_Colorswatches_Model_Resource_Swatch_Collection
     */
    public function getSwatchCollection()
    {
        /** @var AW_Colorswatches_Model_Resource_Swatch_Collection $collection */
        $collection = Mage::getModel('awcolorswatches/swatch')->getCollection();
        if (null !== $this->getAttributeId()) {
            $collection->addAttributeIdFilter($this->getAttributeId());
        }
        $collection->addOrderByPosition();
        return $collection;
    }
}