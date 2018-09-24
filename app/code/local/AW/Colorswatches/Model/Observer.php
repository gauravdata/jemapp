<?php
class AW_Colorswatches_Model_Observer
{
    public function attributeSaveAfter($observer)
    {
        $attributeModel = $observer->getEvent()->getDataObject();
        $cswData = $attributeModel->getData('aw_csw');
        if (null === $cswData) {
            return;
        }
        /** @var AW_Colorswatches_Model_Swatchattribute $swatchAttributeModel */
        $swatchAttributeModel = Mage::getModel('awcolorswatches/swatchattribute')->loadByAttributeId(
            $attributeModel->getId()
        );
        $swatchAttributeModel->addData(
            array(
                'is_enabled'             => $cswData['is_enabled'],
                'is_display_popup'       => $cswData['is_display_popup'],
                'is_override_with_child' => $cswData['is_override_with_child'],
                'attribute_id'           => $attributeModel->getId(),
            )
        );
        $swatchAttributeModel->save();

        if (!array_key_exists('options', $cswData) || !is_array($cswData['options'])) {
            return;
        }

        $optionCollection = Mage::getModel('eav/entity_attribute_option')->getCollection();
        $fieldName = $optionCollection->getResource()->getIdFieldName();
        $optionCollection->addFieldToSelect($fieldName)
                         ->addFieldToFilter($fieldName, array('in' => array_keys($cswData['options'])));
        foreach ($optionCollection->getData() as $_field) {
            $existsOptionIds[] = $_field[$fieldName];
        }

        foreach ($cswData['options'] as $optionId => $imageUrl) {
            $imageUrl = urldecode($imageUrl);
            /** @var AW_Colorswatches_Model_Swatch $swatchModel */
            $swatchModel = Mage::getModel('awcolorswatches/swatch')->loadByOptionId($optionId);
            $swatchModel->setData('option_id', $optionId);
            if ($imageUrl) {
                $filename = AW_Colorswatches_Helper_Image::getFilenameFromUrl($imageUrl);
                $swatchModel->setData('image', $filename);
            } else {
                $swatchModel->deleteImage();
            }
            if (!in_array($optionId, $existsOptionIds)) {
                continue;
            }
            $swatchModel->save();
        }
    }

    /**
     * observer on click on Clear Image in Cache Management
     *
     * @param $observer
     */
    public function cleanImagesCache($observer)
    {
        AW_Colorswatches_Helper_Image::cleanImageCache();
        //remove unused images
        $swatchCollection = Mage::getModel('awcolorswatches/swatch')->getCollection();
        $swatchImageList = $swatchCollection->getColumnValues('image');
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(AW_Colorswatches_Helper_Image::getDirPath())
        );
        while ($it->valid()) {
            if (!$it->isDot() && $it->getSubPath()) {
                $imageName = '/' . $it->getSubPathName();
                if (!in_array($imageName, $swatchImageList)) {
                    AW_Colorswatches_Helper_Image::deleteImage($imageName);
                }
            }
            $it->next();
        }
    }

    /**
     * observer for compatibility with magento 1.4.1.1
     * you can remove it when support of 1.4.1.1 version will be outdated
     * @param $observer
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        //add tab to adminhtml attribute edit page
        $block = $observer->getEvent()->getBlock();
        if (!$block instanceof Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tabs) {
            return;
        }
        $tabsIds = $block->getTabsIds();
        if (in_array('awcolorswatch', $tabsIds)) {
            return;
        }
        $colorswatchTab = $block->getLayout()->createBlock(
            'awcolorswatches/adminhtml_catalog_product_attribute_edit_tab_colorswatch', 'tab_awcolorswatch'
        );
        $block->addTab('awcolorswatch', $colorswatchTab);
    }

    /**
     * @param $observer
     */
    public function overrideLayerBlocksTemplate($observer)
    {
        if (!AW_Colorswatches_Helper_Config::isEnabled() ||
            !AW_Colorswatches_Helper_Config::isCanShowInLayer() ||
            (@class_exists('AW_Mobile2_Helper_Data') &&
                (AW_Mobile2_Helper_Data::isIphoneTheme() || AW_Mobile2_Helper_Data::isIPadTheme())
            )
        ) {
            return;
        }
        $block = $observer->getEvent()->getBlock();
        $type = $block->getType();
        $template = $block->getTemplate();

        if ($type === 'catalog/layer_state' && $template === 'catalog/layer/state.phtml') {
            $block->setTemplate('aw_colorswatches/catalog/layer/state.phtml');
        }

        $layerBlockTypeList = array(
            'catalog/layer_view', 'catalogsearch/layer',
            'enterprise_search/catalog_layer_view', 'enterprise_search/catalogsearch_layer'
        );
        $filterBlockTypeList = array('catalog/layer_filter_attribute', 'catalogsearch/layer_filter_attribute');
        if (in_array($type, $layerBlockTypeList)) {
            foreach ($block->getFilters() as $filter) {
                if (in_array($filter->getType(), $filterBlockTypeList)) {
                    $filter->setTemplate('aw_colorswatches/catalog/layer/filter.phtml');
                }
            }
        }
    }
}