<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 22-10-14
 * Time: 16:09
 */ 
class Twm_ThreeIsOne_Model_Rules_Observer extends Amasty_Rules_Model_Observer {

    public function handleValidation($observer)
    {
        parent::handleValidation($observer);

        $address = $observer->getEvent()->getAddress();
        foreach ($this->_getAllItems($address) as $item) {



            /* @var $item Mage_Sales_Model_Quote_Item */
            if ($item->getParentItemId()){
                continue;
            }
            $appliedIds = $item->getAppliedRuleIds();
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );

            if ($item->getCustomPrice() != null) {
                //continue;
            }

            $collection = Mage::getModel('salesrule/rule')->getCollection();
            $collection->getSelect()->where('is_active = 1')
                ->where("`main_table`.rule_id in (?)", explode(',',$appliedIds))
                ->where("`main_table`.simple_action = 'price_attribute'");

            if ($collection->count() <= 0) {
                //$item->setCustomPrice(null);
                //$item->setOriginalCustomPrice(null);
                // Enable super mode on the product.
                //$item->getProduct()->setIsSuperMode(false);
//var_dump('reset price to '.$item->getId().'# '. $item->getProduct()->getPrice());
                //$item->setPrice($item->getPrice());
            }
        }
        return $this;
    }

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
                //if ($item->getParentItemId()){
                //    continue;
                //}
                /* @var $item Mage_Sales_Model_Quote_Item */
                $item = ( $item->getParentItem() ? $item->getParentItem() : $item );

                /* @var $product Mage_Catalog_Model_Product */
                $productId = $item->getProduct()->getId();
                $price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, $attributeCode, $storeId);

                if ($price <= 0) {
                    continue;
                }
//var_dump("Set custom price {$price} (initrule) {$item->getId()} {$item->getProduct()->getId()}");
                // Set the custom price
                //$item->setCustomPrice($price);
                //$item->setOriginalCustomPrice($price);
                // Enable super mode on the product.
                //$item->getProduct()->setIsSuperMode(true);

                $r[$item->getId()]['discount'] = 1;
                $r[$item->getId()]['base_discount'] = 1;
            }
            return $r;
        }
        return parent::_initRule($rule, $address, $quote);
    }

    public function handleFinalPrice($observer)
    {
//var_dump("handle final price");
        $product = $observer->getProduct();

        $session = Mage::getModel('checkout/cart')->getCheckoutSession();
        if ($session->hasQuote()) {
            $appliedIds = $session->getQuote()->getAppliedRuleIds();
//var_dump("Applied ids $appliedIds");
            if ($appliedIds) {
                $collection = Mage::getModel('salesrule/rule')->getCollection();

                $collection->getSelect()->where('is_active = 1')
                    ->where("`main_table`.rule_id in (?)", explode(',',$appliedIds));

                foreach ($collection as $rule) {
                    if ($rule->getSimpleAction() == Twm_ThreeIsOne_Helper_Rules_Data::TYPE_PRICE_ATTR
                        || $rule->getSimpleAction() == Twm_ThreeIsOne_Helper_Rules_Data::TYPE_PRICE_ATTR)
                    {
                        $attributeId = (int)$rule->getDiscountAmount();
                        $attributeCode = $this->_attrCodeById($attributeId);
//var_dump("Found attr $attributeCode");
                    }
                }
            }
        }

        if ($product) {
            /* @var $product Mage_Catalog_Model_Product */
            if (isset($attributeCode)) {
                //$productId = $product->getId();
                //$price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, $attributeCode, $storeId);
                $price = $this->getData($attributeCode);

//var_dump("Set custom price {$price} (finalprice)");
                // Set the custom price
                //$product->setCustomPrice($price);
                //$product->setOriginalCustomPrice($price);
                $product->setFinalPrice($price);
                // Enable super mode on the product.
                //$product->setIsSuperMode(true);
            } else {
                $price = $product->getPrice();
//var_dump("Reset custom price {$price} (finalprice)");
                $product->setFinalPrice($price);
            }
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