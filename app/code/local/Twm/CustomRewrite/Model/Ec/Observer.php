<?php
/**
 * Created by PhpStorm.
 * User: mathijs
 * Date: 2-11-15
 * Time: 10:49
 */ 
class Twm_CustomRewrite_Model_Ec_Observer extends Anowave_Ec_Model_Observer
{

    protected function getAjax(Mage_Core_Block_Abstract $block, $content = null)
    {
        if(Mage::registry('current_category'))
        {
            $category = Mage::registry('current_category');
        }
        else
        {
            $collection = $block->getProduct()->getCategoryIds();

            if (!$collection)
            {
                $collection[] = Mage::app()->getStore()->getRootCategoryId();
            }

            $category = Mage::getModel('catalog/category')->load
            (
                end($collection)
            );

        }

        $doc = new DOMDocument('1.0','utf-8');
        $dom = new DOMDocument('1.0','utf-8');

        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        foreach ($dom->getElementsByTagName('button') as $button)
        {
            if (!$button->hasAttribute('no-gtm'))
            {
                /**
                 * Reference existing click event(s)
                 */
                $click = $button->getAttribute('onclick');

                $button->setAttribute('onclick', 'AEC.ajax(this,dataLayer)');
                $button->setAttribute('data-id', $block->getProduct()->getSku());
                $button->setAttribute('data-name', Mage::helper('core')->jsQuoteEscape($block->getProduct()->getName()));
                $button->setAttribute('data-price', $block->getProduct()->getFinalPrice());
                $button->setAttribute('data-category', Mage::helper('core')->jsQuoteEscape($category->getName()));
                $button->setAttribute('data-brand', $block->getProduct()->getAttributeText('manufacturer'));
                $button->setAttribute('data-variant', Mage::helper('core')->jsQuoteEscape($block->getProduct()->getResource()->getAttribute('color')->getFrontend()->getValue($block->getProduct())));
                $button->setAttribute('data-click', $click);

                if ('grouped' == $block->getProduct()->getTypeId())
                {
                    $button->setAttribute('data-grouped', 1);
                }

                if ('configurable' == $block->getProduct()->getTypeId())
                {
                    $button->setAttribute('data-configurable', 1);
                }
            }
            else
                $button->removeAttribute('no-gtm');
        }

        return $this->getDOMContent($dom, $doc);
    }

}