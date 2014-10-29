<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 22-10-14
 * Time: 15:47
 */ 
class Twm_ThreeIsOne_Model_Catalog_Product extends Mage_Catalog_Model_Product {

//    public function getFinalPrice($qty=null)
//    {
//        $session = Mage::getModel('checkout/cart')->getCheckoutSession();
//        if ($session->hasQuote()) {
//            $appliedIds = $session->getQuote()->getAppliedRuleIds();
//            if ($appliedIds) {
//                $collection = Mage::getModel('salesrule/rule')->getCollection();
//
//                $collection->getSelect()->where('is_active = 1')
//                    ->where("`main_table`.rule_id in (?)", explode(',',$appliedIds));
//
//                $storeId = Mage::app()->getStore()->getStoreId();
//
//                foreach ($collection as $rule) {
//                    if ($rule->getSimpleAction() == Twm_ThreeIsOne_Helper_Rules_Data::TYPE_PRICE_ATTR
//                        || $rule->getSimpleAction() == Twm_ThreeIsOne_Helper_Rules_Data::TYPE_PRICE_ATTR)
//                    {
//                        $attributeId = (int)$rule->getDiscountAmount();
//                        $attributeCode = $this->_attrCodeById($attributeId);
//
//                        /* @var $product Mage_Catalog_Model_Product */
//                        //$productId = $this->getId();
//                        //$price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, $attributeCode, $storeId);
//                        $price = $this->getData($attributeCode);
//                        // Set the custom price
//                        //$this->setCustomPrice($price);
//                        //$this->setOriginalCustomPrice($price);
//                        //$this->setFinalPrice($price);
//                        // Enable super mode on the product.
//                        //$this->setIsSuperMode(true);
//
//                        return $price;
//                    }
//                }
//            }
//        }
//        if (!isset($attributeCode)) {
//            //$price = $this->getPrice();
//            //$this->setFinalPrice($price);
//        }
//        return parent::getFinalPrice($qty);
//    }

    protected function _attrCodeById($id)
    {
        $cacheId = "attr-code-by-id-$id";
        if (false !== ($data = Mage::app()->getCache()->load($cacheId))) {
            return $data;
        }
        $attributeModel = Mage::getModel('eav/entity_attribute')->load($id);
        $data = $attributeModel->getAttributeCode();
        Mage::app()->getCache()->save($data, $cacheId);
        return $data;
    }
}