<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 10-11-14
 * Time: 15:08
 */ 
class Twm_CustomRewrite_Model_Previousnext_Observer extends AW_Previousnext_Model_Observer {
    public function memorizeProductCollection($observer)
    {
        try {
            $productIds = array();

            /** Start extra code */
            $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
            $attributeSetModel->load('Sfeerafbeelding', 'attribute_set_name');
            /** End extra code */

            /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
            $productCollection = $observer->getCollection();
            $productCollection->load();
            $productCollection->clear();

            $productCollectionClone = clone $productCollection;
            $productCollectionClone->setPageSize(1000);
            $productCollectionClone->setCurPage(1);
            $productCollectionClone->clear();

            foreach($productCollectionClone as $product) {
                /** Start extra code */
                if ($attributeSetModel->getId() == $product->getAttributeSetId()) {
                    continue;
                }
                /** End extra code */
                $productIds[] = $product->getId();
            }
            Mage::getSingleton('core/session')->setData('aw_prevnext_pids', $productIds);

            $request = Mage::app()->getRequest();
            Mage::getSingleton('core/session')->setData('aw_prevnext_req', $request);

            if (Mage::registry('current_category')) {
                $categoryId = (string)Mage::registry('current_category')->getData('entity_id');
            } else {
                $categoryId = null;
            }
            Mage::getSingleton('core/session')->setData('aw_prevnext_cat', $categoryId);
        } catch (Exception $e) {
            //
        }
    }

}