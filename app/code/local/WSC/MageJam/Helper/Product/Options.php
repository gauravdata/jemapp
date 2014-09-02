<?php

class WSC_MageJam_Helper_Product_Options extends Mage_Core_Helper_Abstract
{

    /**
     * Retrieve list of product custom options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOptionList(Mage_Catalog_Model_Product $product)
    {
        $result = array();
        /** @var $option Mage_Catalog_Model_Product_Option */
        foreach ($product->getProductOptionsCollection() as $option) {
            $result[] = $this->_getOptionInfo($option);
        }
        return $result;
    }

    /**
     * Get full information about custom option in product
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return array
     */
    protected function _getOptionInfo(Mage_Catalog_Model_Product_Option $option)
    {
        $result = array(
            'option_id' => $option->getId(),
            'title' => $option->getTitle(),
            'type' => $option->getType(),
            'is_require' => $option->getIsRequire(),
            'sort_order' => $option->getSortOrder(),
            // additional_fields should be two-dimensional array for all option types
            'additional_fields' => array(
                array(
                    'price' => $option->getPrice(),
                    'price_type' => $option->getPriceType(),
                    'sku' => $option->getSku()
                )
            )
        );
        // Set additional fields to each type group
        switch ($option->getGroupByType()) {
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_TEXT:
                $result['additional_fields'][0]['max_characters'] = $option->getMaxCharacters();
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_FILE:
                $result['additional_fields'][0]['file_extension'] = $option->getFileExtension();
                $result['additional_fields'][0]['image_size_x'] = $option->getImageSizeX();
                $result['additional_fields'][0]['image_size_y'] = $option->getImageSizeY();
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT:
                $result['additional_fields'] = array();
                foreach ($option->getValuesCollection() as $value) {
                    $result['additional_fields'][] = array(
                        'value_id' => $value->getId(),
                        'title' => $value->getTitle(),
                        'price' => $value->getPrice(),
                        'price_type' => $value->getPriceType(),
                        'sku' => $value->getSku(),
                        'sort_order' => $value->getSortOrder()
                    );
                }
                break;
            default:
                break;
        }

        return $result;
    }
}