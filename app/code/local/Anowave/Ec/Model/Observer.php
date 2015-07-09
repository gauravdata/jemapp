<?php
/**
 * Anowave Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2015 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
class Anowave_Ec_Model_Observer
{
    /**
     * Define constant(s)
     */
    public function __construct()
    {
    	@defined('VARIANT_SEPARATOR') || define('VARIANT_SEPARATOR', '-');
    }
    
    /**
     * Modifies transport layer and hooks tracking logic 
     * 
     * @param Varien_Event_Observer $observer
     * @return boolean
     */
	public function modify(Varien_Event_Observer $observer)
	{
		if (true)
		{
			/**
			 * Get transport layer
			 */
			$content = $observer->getTransport()->getHtml();
			
			/**
			 * Append data to blocks
			 */
			$template = $this->append
			(
				$observer->getBlock()
			);
			
			if ($template)
			{
				$content .= $template;
			}

			/**
			 * Augment transport layer
			 */
			$observer->getTransport()->setHtml
			(
				$this->alter($observer->getBlock(), $content)
			);
		}

		return true;
	}
	
	/**
	 * Appends tracking logic to transport layer blocks 
	 * 
	 * @param Mage_Core_Block_Abstract $block
	 * @return NULL
	 */
	protected function append(Mage_Core_Block_Abstract $block)
	{	
		switch ($block->getType())
		{
			case 'page/html_head':							return $this->getHead();
			case 'page/html_footer': 						return $this->getQueue(); 
			case 'catalog/product_view_type_simple':
			case 'catalog/product_view_type_grouped':
			case 'catalog/product_view_type_configurable':	return $this->trackProductViewDetails($block); 
			case 'catalog/product_list':					return $this->trackProductImpression($block);
			case 'checkout/cart':							return $this->getCart($block);
			case 'checkout/onepage':						return $this->getCheckout();
		}
		
		return null;
	}
	
	/**
	 * Alters transport layer contents and hooks tracking logic 
	 * 
	 * @param Mage_Core_Block_Abstract $block
	 * @param string $content
	 * @return string|$content
	 */
	protected function alter(Mage_Core_Block_Abstract $block, $content)
	{	
		switch ($block->getNameInLayout())
		{
			case 'product.info.addtocart': return $this->getAjax($block, $content);
				default:
					switch ($block->getType())
					{
						case 'catalog/product_list':		return $this->getClick($block, $content);
						case 'checkout/cart_item_renderer': 
						case 'checkout/cart_item_renderer_configurable':
															return $this->getDelete($block, $content);
					}
		}
		
		return $content;
	}
	
	/**
	 * Track order cancellation 
	 * 
	 * @param Varien_Event_Observer $observer
	 * @return boolean
	 */
	public function refund(Varien_Event_Observer $observer)
	{
		$order = $observer->getPayment()->getOrder();
		
		if ($order->getTotalRefunded() > 0)
		{
			if ($order->getIsVirtual()) 
			{
				$address = $order->getBillingAddress();
			} 
			else 
			{
				$address = $order->getShippingAddress();
			}
			
			$refund = array
			(
				'ecommerce' => array
				(
					'refund' => array
					(
						'actionField' => array
						(
							'id' => $order->getRealOrderId()
						),
						'products' => array()
					)
				)
			);
			
			foreach ($order->getAllVisibleItems() as $item)
			{
				$product = Mage::getModel('catalog/product')->load
				(
					$item->getProductId()
				);
					
				$collection = $product->getCategoryIds();
					
				if (!$collection)
				{
					$collection[] = Mage::app()->getStore()->getRootCategoryId();
				}
					
				$category = Mage::getModel('catalog/category')->load
				(
					end($collection)
				);
				
				/**
				 * Get product name
				 */
				$args = new stdClass();
					
				$args->id 	= $product->getSku();
				$args->name = $product->getName();
				
				list($parents) = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild
				(
					$product->getId()
				);
				
				if ($parents)
				{
					/**
					 * Get parent product(s)
					 */
					$parent = Mage::getModel('catalog/product')->load((int) $parents);
					
					/**
					 * Change name to parent product name and pass variant instead
					 */
					if ($parent->getId())
					{
						$args->id	= $parent->getSku();
						$args->name = $parent->getName();
						
						/**
						 * Use parents category
						 */
						$collection = $parent->getCategoryIds();
							
						if (!$collection)
						{
							$collection[] = Mage::app()->getStore()->getRootCategoryId();
						}
							
						$category = Mage::getModel('catalog/category')->load
						(
							end($collection)
						);
					}

					$variant = array();
				
					if ($item instanceof Mage_Sales_Model_Quote_Item) 
					{
						$request = new Varien_Object(unserialize($item->getOptionByCode('info_buyRequest')->getValue()));
					} 
					else if ($item instanceof Mage_Sales_Model_Order_Item) 
					{
						$request = new Varien_Object($item->getProductOptions());
					}
				
					$options = $request->getData('info_buyRequest');
					
					if (isset($options['super_attribute']) && is_array($options['super_attribute']))
					{
						foreach ($options['super_attribute'] as $id => $option)
						{
							$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($id);
							
							if ($attribute->usesSource()) 
							{
								$variant[] = join(':', array
								(
									$attribute->getFrontendLabel(), 
									$attribute->getSource()->getOptionText($option)
								));
							}
						}
					}
					
					/**
					 * Push variant(s)
					 */
					foreach ($variant as $value)
					{
						$variant[] = $value;
					}
				}
				
				$refund['ecommerce']['refund']['products'][] = array
				(
					'name' 		=> Mage::helper('core')->jsQuoteEscape($args->name),
					'id'		=> $args->id,
					'price' 	=> Mage::getBlockSingleton('ec/track')->getPriceItem($item, $order),
					'quantity' 	=> $item->getQtyOrdered(),
					'category' 	=> Mage::helper('core')->jsQuoteEscape($category->getName()),
					'variant'	=> join(VARIANT_SEPARATOR, $variant)
				);
			}
			
			$analytics = curl_init('www.google-analytics.com');
			
			curl_setopt($analytics, CURLOPT_HEADER, 0);
			curl_setopt($analytics, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($analytics, CURLOPT_POST, 1);
			
			
			$ua = Mage::getStoreConfig('ec/config/refund');
			
			if ($ua)
			{
				$payload = array
				(
					'v' 	=> 1,
					'tid' 	=> $ua,
					'cid' 	=> sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',mt_rand(0, 0xffff), mt_rand(0, 0xffff),mt_rand(0, 0xffff),mt_rand(0, 0x0fff) | 0x4000,mt_rand(0, 0x3fff) | 0x8000,mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
					't'		=> 'event',
					'ec'	=> 'Ecommerce',
					'ea'	=> 'Refund',
					'ni'	=> 1,
					'ti'	=> $refund['ecommerce']['refund']['actionField']['id'],
					'pa'	=> 'refund'
				);
				
				foreach ($refund['ecommerce']['refund']['products'] as $index => $product)
				{
					$key = 1 + $index;
				
					$payload["pr{$key}id"] = $product['id'];
					$payload["pr{$key}qt"] = $product['quantity'];
				}
				
				curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			}
			
			try
			{
				$response = curl_exec($analytics);
				
				if (!curl_error($analytics) && $response)
				{
					Mage::getSingleton('core/session')->addNotice("Refund tracking data sent to Google Analytics successfully. (ID:$ua)");
				}
				else 
				{
					Mage::getSingleton('adminhtml/session')->addWarning('Failed to send refund tracking data to Google Analytics.');
				}
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addWarning
				(
					$e->getMessage()
				);
			}
			
			return $this;
		}
		
		/**
		 * @todo Implement order cancellation tracking
		 */
		return true;
	}
	
	protected function getHead()
	{
		return Mage::helper('ec')->filter
		(
			(string) Mage::app()->getLayout()->createBlock('ec/track')->setTemplate('ec/header.phtml')->setType('ec/track')->toHtml()
		);
	}
	
	protected function getQueue()
	{
		return Mage::helper('ec')->filter
		(
			Mage::app()->getLayout()->createBlock('ec/track')->setTemplate('ec/footer.phtml')->toHtml()
		);
	}
	
	protected function getCheckout()
	{
		return Mage::helper('ec')->filter
		(
			Mage::app()->getLayout()->createBlock('ec/track')->setTemplate('ec/checkout.phtml')->toHtml()
		);
	}
	
	protected function getCart(Mage_Checkout_Block_Cart $block)
	{
		return Mage::app()->getLayout()->createBlock('ec/track')->setTemplate('ec/cart.phtml')->setData(array
		(
			'items' => $block->getItems(),
			'quote' => $block->getQuote()
 		))->toHtml();
	}
	
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
			/**
			 * Reference existing click event(s)
			 */
			$click = $button->getAttribute('onclick');
			
			$button->setAttribute('onclick', 		'AEC.ajax(this,dataLayer)');
			$button->setAttribute('data-id', 		$block->getProduct()->getSku());
			$button->setAttribute('data-name', 		Mage::helper('core')->jsQuoteEscape($block->getProduct()->getName()));
			$button->setAttribute('data-price', 	$block->getProduct()->getFinalPrice());
			$button->setAttribute('data-category', 	Mage::helper('core')->jsQuoteEscape($category->getName()));
			$button->setAttribute('data-brand',		$block->getProduct()->getAttributeText('manufacturer'));
			$button->setAttribute('data-variant', 	Mage::helper('core')->jsQuoteEscape($block->getProduct()->getResource()->getAttribute('color')->getFrontend()->getValue($block->getProduct())));
			$button->setAttribute('data-click', 	$click);
			
			if ('grouped' == $block->getProduct()->getTypeId())
			{
				$button->setAttribute('data-grouped',1);
			}
			
			if ('configurable' == $block->getProduct()->getTypeId())
			{
				$button->setAttribute('data-configurable',1);
			}
		}
	
		return $this->getDOMContent($dom, $doc);
	}
	
	protected function getDelete(Mage_Core_Block_Abstract $block, $content = null)
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
			
		$doc = new DOMDocument('1.0','utf-8');
		$dom = new DOMDocument('1.0','utf-8');
		
		@$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		foreach ($dom->getElementsByTagName('a') as $a)
		{
			if (false !== strpos($a->getAttribute('class'),'btn-remove'))
			{
				$a->setAttribute('onclick', 		'return AEC.remove(this, dataLayer)');
				$a->setAttribute('data-id', 		$block->getProduct()->getSku());
				$a->setAttribute('data-name', 		Mage::helper('core')->jsQuoteEscape($block->getProduct()->getName()));
				$a->setAttribute('data-price', 		$block->getProduct()->getFinalPrice());
				$a->setAttribute('data-category', 	Mage::helper('core')->jsQuoteEscape($category->getName()));
				$a->setAttribute('data-quantity',	$block->getQty());
			}
		}
		
		return $this->getDOMContent($dom, $doc);
	}
	
	protected function getClick(Mage_Core_Block_Abstract $block, $content = null)
	{
		$doc = new DOMDocument('1.0','utf-8');
		$dom = new DOMDocument('1.0','utf-8');
		
		@$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		$products = array();
		

		foreach ($block->getLoadedProductCollection() as $product)
		{
			$products[] = $product;
		}

		$query = new DOMXPath($dom);
		
		foreach ($query->query('//ul[contains(@class, "products-grid")]/li[contains(@class, "item")]|//ol[contains(@class, "products-list")]/li[contains(@class, "item")]') as $key => $element)
		{
			if (isset($products[$key]))
			{
				if (Mage::registry('current_category'))
				{
					$category = Mage::registry('current_category');
				}
				else 
				{
					$collection = $products[$key]->getCategoryIds();
					
					if (!$collection)
					{
						$collection[] = Mage::app()->getStore()->getRootCategoryId();
					}
					
					$category = Mage::getModel('catalog/category')->load
					(
						end($collection)
					);
				}
						
				foreach ($element->getElementsByTagName('a') as $a)
				{
					$click = $a->getAttribute('onclick');
					
					$a->setAttribute('data-id', 		$products[$key]->getSku());
					$a->setAttribute('data-name', 		Mage::helper('core')->jsQuoteEscape($products[$key]->getName()));
					$a->setAttribute('data-price', 		$products[$key]->getFinalPrice());
					$a->setAttribute('data-category', 	Mage::helper('core')->jsQuoteEscape($category->getName()));
					$a->setAttribute('data-quantity', 	1);
					$a->setAttribute('data-click',		$click);
					$a->setAttribute('onclick',			'return AEC.click(this,dataLayer)');
				}
			}
		}
		
		return $this->getDOMContent($dom, $doc);
	}
	
	protected function trackProductImpression(Mage_Core_Block_Abstract $block)
	{
		if(Mage::registry('current_category'))
		{
			$category = Mage::registry('current_category');
		}
		else 
		{
			if ($block && $block->getProduct())
			{
				$collection = $block->getProduct()->getCategoryIds();
			}
			else 
			{
				$collection = array();
			}
			
			if (!$collection)
			{
				$collection[] = Mage::app()->getStore()->getRootCategoryId();
			}
			
			$category = Mage::getModel('catalog/category')->load
			(
				end($collection)
			);
			
		} 
		
		return Mage::app()->getLayout()->createBlock('ec/track')->setTemplate('ec/impression.phtml')->setData(array
		(
			'collection' 	=> $block->getLoadedProductCollection(),
			'category'		=> $category
		))->toHtml();
	}

	protected function trackProductViewDetails(Mage_Core_Block_Abstract $block)
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
		
		$grouped = array();
		
		/* Check if product is configurable */
		if ('grouped' == $block->getProduct()->getTypeId())
		{
			foreach ($block->getProduct()->getTypeInstance(true)->getAssociatedProducts($block->getProduct()) as $product)
			{
				$grouped[] = $product;
			}
		}

		return Mage::app()->getLayout()->createBlock('ec/track')->setTemplate('ec/details.phtml')->setData(array
		(
			'product'  => $block->getProduct(),
			'grouped'  => $grouped,
			'category' => $category
		))->toHtml();
	}

	public function setOrder(Varien_Event_Observer $observer)
	{
		$orderIds = $observer->getEvent()->getOrderIds();
		
        if (empty($orderIds) || !is_array($orderIds)) 
        {
            return;
        }
        
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('ec_purchase');
        
        if ($block) 
        {
            $block->setOrderIds($orderIds);
            $block->setAdwords(new Varien_Object(array
            (
            	'google_conversion_id' 			=> Mage::getStoreConfig('ec/adwords/conversion_id'),
            	'google_conversion_language' 	=> 'en_GB',
            	'google_conversion_format' 		=> Mage::getStoreConfig('ec/adwords/conversion_format'),
            	'google_conversion_label' 		=> Mage::getStoreConfig('ec/adwords/conversion_label'),
            	'google_conversion_color' 		=> Mage::getStoreConfig('ec/adwords/conversion_color'),
            	'google_conversion_currency' 	=> Mage::app()->getStore()->getCurrentCurrencyCode()
            )));
        }
        else 
        {
        	return true;
        }
	}
	
	protected function getDOMContent(DOMDocument $dom, DOMDocument $doc)
	{
		$head = $dom->getElementsByTagName('head')->item(0);
		$body = $dom->getElementsByTagName('body')->item(0);
		
		if ($head instanceof DOMElement)
		{
			foreach ($head->childNodes as $child)
			{
				$doc->appendChild($doc->importNode($child, true));
			}
		}

		if ($body instanceof DOMElement)
		{
			foreach ($body->childNodes as $child)
			{
			    $doc->appendChild($doc->importNode($child, true));
			}
		}

		$content = $doc->saveHTML();
		
		return $content;
	}
	
	public function getFooter(Varien_Event_Observer $observer)
	{
		$footer = $observer->getModel()->getFooter() . Mage::helper('ec')->filter(Mage::getStoreConfig('ec/config/code', Mage::app()->getStore()->getStoreId()));
		
		$observer->getModel()->setFooter($footer);
		
		return true;
	}
}