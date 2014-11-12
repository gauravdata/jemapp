<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 22-10-14
 * Time: 16:09
 */ 
class Twm_ThreeIsOne_Model_Rules_Observer extends Amasty_Rules_Model_Observer {

    protected $collectingTotals = false;

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
                $item = ( $item->getParentItem() ? $item->getParentItem() : $item );

                /* @var $product Mage_Catalog_Model_Product */
                $productId = $item->getProduct()->getId();
                $price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, $attributeCode, $storeId);

                if ($price <= 0) {
                    continue;
                }
                $r[$item->getId()]['discount'] = 0;
                $r[$item->getId()]['base_discount'] = 0;
            }
        }
        $return = parent::_initRule($rule, $address, $quote);
        $return = $return + $r;
        return $return;
    }

    public function handleFinalPrice($observer)
    {
        if ($this->collectingTotals) return;
        $product = $observer->getProduct();

        $session = Mage::getModel('checkout/cart')->getCheckoutSession();
        if ($session->hasQuote()) {
            $this->collectingTotals = true;
            $items = $session->getQuote()->collectTotals()->getAllItems();
            $this->collectingTotals = false;
            foreach ($items as $item) {
                if (!$item->getParentItemId()){
                    continue;
                }
                $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
                $appliedIds = $item->getAppliedRuleIds();

                if ($appliedIds) {
                    $collection = Mage::getModel('salesrule/rule')->getCollection();

                    $collection->getSelect()->where('is_active = 1')
                        ->where("`main_table`.rule_id in (?)", explode(',',$appliedIds))
                        ->where("`main_table`.simple_action = 'price_attribute'");

                    foreach ($collection as $rule) {
                        $attributeId = (int)$rule->getDiscountAmount();
                        $attributeCode = $this->_attrCodeById($attributeId);
                    }
                }
            }
        }
        if ($product) {
            /* @var $product Mage_Catalog_Model_Product */
            $productId = $product->getId();
            $storeId = Mage::app()->getStore()->getStoreId();
            if (isset($attributeCode)) {
                $price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, $attributeCode, $storeId);
                if ($price > 0) {
                    $product->setFinalPrice($price);
                }
            } else {
                // reset price
                $basePrice = $product->getPrice();
                $specialPrice = $product->getSpecialPrice();
                $specialPriceFrom = $product->getSpecialFromDate();
                $specialPriceTo = $product->getSpecialToDate();

                $price = $product->getPriceModel()->calculateSpecialPrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo);
                if ($price > 0) {
                    $product->setFinalPrice($price);
                }
            }
        }
    }

    protected function _getTotalQty($quote)
    {
        $qty = 0;
        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
            if (!$item->getId()){
                continue;
            }
            $qty += (int)$item->getQty();
        }
        return $qty;
    }

    protected function _getAddress(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address = $item->getAddress();
        } elseif ($this->_address) {
            $address = $this->_address;
        } elseif ($item->getQuote()->getItemVirtualQty() > 0) {
            $address = $item->getQuote()->getBillingAddress();
        } else {
            $address = $item->getQuote()->getShippingAddress();
        }
        return $address;
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
