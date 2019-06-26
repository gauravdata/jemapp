<?php

class Biztech_Translator_Model_Config_Source_Attributes extends Varien_Data_Collection
{
    public function toOptionArray()
    {
        $attributes = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection();
        $attributes->addStoreLabel(Mage::app()->getStore()->getId());
        $excludedArray = $this->getExcludedAttributes();
        $allProductAttributes = array();
        foreach ($attributes as $attribute) {
            if (($attribute['frontend_input'] == 'textarea' || $attribute['frontend_input'] == 'text') && ($attribute['is_global'] != 1) && (!in_array($attribute['attribute_code'], $excludedArray))) {
                $allProductAttributes[] = array('label' => $attribute['attribute_code'], 'value' => $attribute['attribute_code']);
            }
        }
        return $allProductAttributes;
    }

    public function getExcludedAttributes()
    {

        $attributes = array('sku', 'required_options', 'has_options', 'created_at', 'updated_at', 'group_price', 'tier_price', 'custom_layout_update', 'old_id', 'category_ids');

        return $attributes;
    }
}
