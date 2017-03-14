<?php

    class Twm_StockPriceRule_Model_CatalogRule_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product {

        /**
         * Collect validated attributes
         *
         * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
         * @return Mage_CatalogRule_Model_Rule_Condition_Product
         */
        public function collectValidatedAttributes($productCollection)
        {
            $attribute = $this->getAttribute();
            if ('stock_amount' != $attribute) {
                return parent::collectValidatedAttributes($productCollection);
            }

            return $this;
        }

        /**
         * Add special attributes
         *
         * @param array $attributes
         */
        protected function _addSpecialAttributes(array &$attributes)
        {
            parent::_addSpecialAttributes($attributes);
            $attributes['stock_amount'] = Mage::helper('catalog')->__('Stock Availability');
        }

        /**
         * Validate Product Rule Condition
         *
         * @param Varien_Object $object
         *
         * @return bool
         */
        public function validate(Varien_Object $object)
        {
            $stockAmount = 0;

            if($object->getTypeId() == 'configurable') {
                $allProducts = $object->getTypeInstance(true)->getUsedProducts(null, $object);

                foreach ($allProducts as $product) {
                    if ($product->isSaleable()) {
                        $stockAmount += $product->getStockItem()->getQty();
                    }
                }
            }
            else {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($object);
                $stockAmount += $stockItem->getQty();
            }

            $object->setStockAmount($stockAmount);
            return parent::validate($object);
        }

    }