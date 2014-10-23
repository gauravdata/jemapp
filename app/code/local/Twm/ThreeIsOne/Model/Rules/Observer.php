<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 22-10-14
 * Time: 16:09
 */ 
class Twm_ThreeIsOne_Model_Rules_Observer extends Amasty_Rules_Model_Observer {

    protected function _initRule($rule, $address, $quote)
    {
        if ($rule->getSimpleAction() == Twm_ThreeIsOne_Helper_Rules_Data::TYPE_PRICE_ATTR
            || $rule->getSimpleAction() == Twm_ThreeIsOne_Helper_Rules_Data::TYPE_PRICE_ATTR
        ) {
            $storeId = Mage::app()->getStore()->getStoreId();
            $attributeId = (int)$rule->getDiscountAmount();
            $attributeModel = Mage::getModel('eav/entity_attribute')->load($attributeId);
            $attributeCode = $attributeModel->getAttributeCode();
            $r = array();
            foreach ($this->_getAllItems($address) as $item) {
                if ($item->getParentItemId()){
                    continue;
                }
                /* @var $item Mage_Sales_Model_Quote_Item */
                /* @var $product Mage_Catalog_Model_Product */
                $productId = $item->getProduct()->getId();
                $price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, $attributeCode, $storeId);

                $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
                // Set the custom price
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                // Enable super mode on the product.
                $item->getProduct()->setIsSuperMode(true);
            }
            return $r;
        }
        return parent::_initRule($rule, $address, $quote);
    }

    public function handleFinalPrice($observer)
    {
        $product = $observer->getProduct();

        $session = Mage::getModel('checkout/cart')->getCheckoutSession();
        if ($session->hasQuote() && !$product->getIsSalesRulePriceAttrApplied()) {
            $appliedIds = $session->getQuote()->getAppliedRuleIds();
            if (count($appliedIds) > 0) {
                $collection = Mage::getModel('salesrule/rule')->getCollection();

                $collection->getSelect()->where('is_active = 1')
                    ->where("`main_table`.rule_id in (?)", explode(',',$appliedIds));

                //$storeId = Mage::app()->getStore()->getStoreId();

                foreach ($collection as $rule) {
                    if ($rule->getSimpleAction() == Twm_ThreeIsOne_Helper_Rules_Data::TYPE_PRICE_ATTR
                        || $rule->getSimpleAction() == Twm_ThreeIsOne_Helper_Rules_Data::TYPE_PRICE_ATTR)
                    {

                        $attributeId = (int)$rule->getDiscountAmount();
                        $attributeCode = $this->_attrCodeById($attributeId);
                    }
                }
            }
        }

        if ($product && $attributeCode) {
            /* @var $product Mage_Catalog_Model_Product */
            //$productId = $product->getId();
            //$price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, $attributeCode, $storeId);
            $price = $this->getData($attributeCode);
            // Set the custom price
            $product->setCustomPrice($price);
            $product->setOriginalCustomPrice($price);
            $product->setFinalPrice($price);
            // Enable super mode on the product.
            $product->setIsSuperMode(true);
            $product->setIsSalesRulePriceAttrApplied(true);
        }
    }

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

    public function handleFormCreation($observer)
    {
        parent::handleFormCreation($observer);

        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection->getSelect()->where('main_table.backend_model = ?', 'catalog/product_attribute_backend_price');

        $html = '<select id="tmp_rule_discount_amount" class="select required-entry" name="tmp_discount_amount">';
        foreach ($collection as $attr) {
            $html .= "<option value=\"{$attr->getId()}\">{$attr->getFrontendLabel()}</option>";
        }
        $html .= '</select>';

        /* @var $discountAmount Varien_Data_Form_Element_Text */
        $discountAmount = $observer->getForm()->getElement('discount_amount');
        $discountAmount->setAfterElementHtml($html . "

            <script type=\"application/javascript\">
                $('rule_simple_action').on('change', function(e, elm) {
                    if(elm.value == 'price_attribute'){
                        $('rule_discount_amount').hide().name = 'tmp_discount_amount';
                        $('tmp_rule_discount_amount').show().name = 'discount_amount';
                    } else {
                        $('rule_discount_amount').show().name = 'discount_amount';
                        $('tmp_rule_discount_amount').hide().name = 'tmp_discount_amount';
                    }
                });
                var v = $('rule_simple_action').getValue();
                if (v  == 'price_attribute'){
                    $('rule_discount_amount').hide().name = 'tmp_discount_amount';
                    $('tmp_rule_discount_amount').show().name = 'discount_amount';
                } else {
                    $('rule_discount_amount').show().name = 'discount_amount';
                    $('tmp_rule_discount_amount').hide().name = 'tmp_discount_amount';
                }
                $('tmp_rule_discount_amount').setValue($('rule_discount_amount').getValue());
            </script>");
    }
}