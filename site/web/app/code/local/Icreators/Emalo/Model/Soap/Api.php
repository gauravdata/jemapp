<?php
/**
 * Emalo Import Module
 *
 */
class Icreators_Emalo_Model_Soap_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * 
	 * Send
	 * This function is called to send directy a XML (string)
	 * @param string $xmlStr
	 */
    protected $_logfile; 
	protected $_storeId;
	protected $_websiteId;
	protected $_rootCatalogId;
	
	protected $_germanStoreId;
	protected $_englishStoreId;
	protected $_dutchStoreId;
	
	public function send($xmlStr)
	{

if($this->validate())
{
		$response=$this->writeXml($xmlStr);
		$response=$this->Updatedb($xmlStr);
		
	    return $response;
}
else
{
	return false;
}
	}	
	
	public function writeXml($xmlStr)
    {
    
    	try
		 {
				$dir = Mage::getBaseDir().DS.'var'.DS.'emalo'.DS.'import'.DS;
		        if (!file_exists($dir))
				 {
		             if (!mkdir($dir,0777,true))
					  {
		    	           throw new Exception ('failed to create dir, check your permission ');
						   return FALSE;
                      }
	           	}
				$xml = simplexml_load_string($xmlStr);
				$typeOfFile 	= $xml->getName();
				$xmlFile 		= $dir.$typeOfFile.'_'.date('YmdHis').'.xml';
				$fp 			= fopen($xmlFile, 'w');
				fputs($fp, $xmlStr);
				fclose($fp); 
				return true ;	
    	   } 
		   
		  catch (Exception $e) 
		  {
			 echo $e->getMessage();
			  
		  }
		  
    	return false;
    }
	
	public function Updatedb($xmlData)
	{
		// get default values for now
		
    	$currentStore 	= Mage::app()->getStore()->getCode();
    	$magDb 			= Mage::getSingleton('core/resource')->getConnection('core_read');
		$tableName = Mage::getSingleton('core/resource')->getTableName('core_store');
		$sql = 'SELECT store_id, website_id FROM '.$tableName.' WHERE code="default"';
		$row = Mage::getSingleton('core/resource')
        	 	->getConnection('core_read')
	     		->fetchRow($sql);
		$storeId = $row['store_id'];
		$websiteId = $row['website_id'];
	
    	$return = '';
		$xml = simplexml_load_string($xmlData);
    	switch ($xml->getName())
		 {
    		case 'Products':
	    		
	    		$return = $this->saveProducts($xml, $storeId, $websiteId);
				break;
				
			case 'Catalogs':
	    		
	    		$return = $this->buildCatalog($xml, $storeId, $websiteId);
	    		break;	
				
			case 'Configuration':
	    		
				foreach($xml->children() as $child)
				{
  					if($child->getName() == 'Dimensions')
					 {
  						$return = $this->retrieveConfigAtts($child, $storeId, $websiteId);
  					 }
					 elseif($child->getName() =='PriceLists')
					  {
						//do nothing
  					  } 
					elseif($child->getName() =='Catalogs')
					  {
  						$return = $this->buildCatalog_config($child, $storeId, $websiteId);
						break;
  					  } 
					
 				}
 				break;	
				   	
				case 'Stock':
				
				$return = $this->setStock($xml, $storeId, $websiteId);
    			break; 
    				
    	}
		return $return;
	}
	//************************   Saving Products to DB  *************************************
	
	
	public function saveProducts($xml, $storeId, $websiteId)
	{
		
		$noErrors				= true;
		$this->_storeId 		= $storeId;
		$this->_websiteId 		= $websiteId;
		$length = count($xml->Product);		

		
		
		foreach($xml->children() as $child)
		{
			if($child->getName() == 'Product' && (int)$child->NumVariants == 0) 
			{
				// simple product
				$str=$this->createSimpleProduct($child,$storeId);
				return $str;
			} 
			elseif ($child->getName() == 'Product' && (int)$child->NumVariants > 0)
			 {
				// configurable product
				$str=$this->createConfigurableProduct($child,$storeId);
			    return $str;
				
			} // else mode, not needed here.
		}	
				
		
		return $noErrors;
	}
	
	
	public function createSimpleProduct($xml,$storeId) 
	{
		
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); // needed due to error in Magento		
		$magDb 			= Mage::getSingleton('core/resource')->getConnection('core_write');	
		$attributeSetId = Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
		$visibilityIdOn = 4; // simple products (catalog and search)
		
		// details
		$productDdgId 	= (string)$xml->Details->ProductID;
		$productNumber 	= (string)$xml->Details->ProductNumber;
		$productName 	= (string)$xml->Details->Name1;
		$productShortDes= (string)$xml->Details->Name2;
		$productdes     = (string)$xml->ProductDescriptions->ProductDescription->DescriptionText;
		$baseUnit		= (string)$xml->Details->BaseUnit;
		$productLength	= (string)$xml->Details->ProductLength;
		$productWidth	= (string)$xml->Details->ProductWidth;
		$productHeight	= (string)$xml->Details->ProductHeight;
		$productWeight	= (string)$xml->Details->ProductWeight;		
		$weightUnit		= (string)$xml->Details->WeightUnit;
		$eanNumber		= (string)$xml->Details->EANNumber;
		$inActive		= (string)$xml->Details->InActive;
		

		
	
		//product images
		
		$img_url_arr =array();
		$img_des_arr =array();
		
		foreach($xml->Images->children() as $child)
		{
		$des=$child->DocumentDesc; 
		$url=$child->Url;
		array_push($img_des_arr, $des);
		array_push($img_url_arr, $url);	
		}
					
		// price and tiers
		$price 		= 0.0000;
		$tierArray 	= array();
		$tiers		= false;
		$length 	= count($xml->Prices->Price);
		if($length > 1) {
			// different tier prices or different price lists
			foreach($xml->Prices->children() as $child){
				if((int)$child->PriceListID == 1 && (int)$child->Quantity == 0) {
					$price = (string)$child->Price;
					$price = str_replace(',','.', $price);
					$price = number_format($price, 4);
					

				} elseif((int)$child->PriceListID == 1 && (int)$child->Quantity > 0) {
					$tierPrice = (string)$child->Price;
					$tierPrice = str_replace(',','.', $tierPrice);
					$tierPrice = number_format($tierPrice, 4);
					$qty = (int)$child->Quantity;
					$newtier = array(
		        				'website_id' => 0, 
		        				'all_groups' => 1, 
								'cust_group' => 32000, 
								'price_qty' => $qty, 
								'price' => $tierPrice,
								'delete' => ''
        					);
        			array_push($tierArray, $newtier);
        			$tiers = true;
					
				}
			}
		} 
		
		elseif ($length == 1) {
			$price = (string)$xml->Prices->Price->Price;
			$price = str_replace(',','.', $price);
			$price = number_format($price, 4);
			
		}
		 else {
			// price missing, leave to zero
		}
		
		
		// catalogs
		$root_cat_id 	= Mage::app()->getStore($storeId)->getRootCategoryId();
		$categories = $this->getCategories($root_cat_id ,$storeId);
		$magentoCategories = array();
		$length = count($xml->Catalogs->Catalog);
		if($length > 0)
		 {
			foreach ($xml->Catalogs->children() as $child)
			 {
				$catalogDdgId = (string)$child->CatalogID;
				$searchDdg = 'emalo_'.$catalogDdgId;
				$key = array_search($searchDdg, $categories);
				$magentoCategoryId = $categories[$key+1];
				array_push($magentoCategories, $magentoCategoryId);
				
			}
		} 
		
		else
		 {
			// No category
			
		}
			
		// okay, we've got all information we need (hopefully)
		// check if product exists
		$sku = $productNumber;
		$tableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity');
		$sql = "SELECT entity_id FROM ".$tableName." WHERE sku = ?";
		$stmt = $magDb->query($sql, $sku);
		$entity = $stmt->fetch();
					
		$product = Mage::getModel('catalog/product');					     
		if ($entity) {  
		   	$productId = $entity['entity_id'];
		   	$product->load($productId);
		} else {
		  	$product->setTypeId('simple');
			$product->setSku($sku);
		}
				
		$product->setName($productName);
		$product->setWebsiteIds(array($this->_websiteId)); 
		$product->setCategoryIds($magentoCategories); 
		$product->setAttributeSetId($attributeSetId); 
		$product->setTaxClassId(0);  // to do: what tax class?
		$product->setTitle($productName);
		$product->setShortDescription($productShortDes);
		$product->setDescription($productdes);                               		
		$product->setBaseUnit($baseUnit);
		$product->setLength($productLength);
		$product->setWidth($productWidth);
		$product->setHeight($productHeight);
		$product->setWeight($productWeight);
		$product->setWeightUnit($weightUnit);
		$product->setEanNumber($eanNumber);
		$product->setPrice($price);
		$product->setTierPrice($tierArray);	
		if($inActive == '-1') {
			$product->setStatus(2); //disabled
		} else {
			$product->setStatus(1); //enabled
		}
		$product->setVisibility($visibilityIdOn); 				

		$stockData = $product->getStockData();
		
		// we don't have any qty yet, so let's put it to 100
		$qty = 1;
		$stockData['qty'] = $qty;
		if($qty == 0) {
			$stockData['is_in_stock'] = 0;
		} else {
			$stockData['is_in_stock'] = 1;
		}
		$stockData['manage_stock'] = 1;
		$stockData['use_config_manage_stock'] = 0;
		$product->setStockData($stockData);
		
		try {
			$product->save();	
			$productId = $product->getId();
			$product = Mage::getModel('catalog/product');
			$product->load($productId);
			$product->setStoreId($storeId);	
			$product->save();
			$this->imgCleanUp($sku,$productId);
			$this->save_img($img_url_arr,$img_des_arr,$sku);
			
			
		} 
		
		catch (Exception $e) 
		{		
		echo $e->getMessage();
		return false;
			
		}	
		
		unset($product);  
		return true;			
	}
	

 public function createConfigurableProduct($xml,$storeId) 
	{
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); // needed due to error in Magento		
		$magDb 			= Mage::getSingleton('core/resource')->getConnection('core_write');	
		$attributeSetId = Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
		$visibilityIdOf = 1; // all simples belonging to configurables 
		$visibilityIdOn = 4; // simple products (catalog and search)
		
		
		// details
		$productDdgId 	= (string)$xml->Details->ProductID;
		$productNumber 	= (string)$xml->Details->ProductNumber;
		$productName 	= (string)$xml->Details->Name1;
		$baseUnit		= (string)$xml->Details->BaseUnit;
		$productLength	= (string)$xml->Details->ProductLength;
		$productWidth	= (string)$xml->Details->ProductWidth;
		$productHeight	= (string)$xml->Details->ProductHeight;
		$productWeight	= (string)$xml->Details->ProductWeight;		
		$weightUnit		= (string)$xml->Details->WeightUnit;
		$eanNumber		= (string)$xml->Details->EANNumber;
		$inActive		= (string)$xml->Details->InActive;
        

//product images
		
		$img_url_arr =array();
		$img_des_arr =array();
		
		foreach($xml->Images->children() as $child)
		{
		$des=$child->DocumentDesc; 
		$url=$child->Url;
		array_push($img_des_arr, $des);
		array_push($img_url_arr, $url);	
		}
		

	
		// price and tiers
		$price 		= 0.0000;
		$tierArray 	= array();
		$tiers		= false;
		$length 	= count($xml->Prices->Price);
		if($length > 1) {
			// different tier prices or different price lists
			foreach($xml->Prices->children() as $child){
				if((int)$child->PriceListID == 1 && (int)$child->Quantity == 0) {
					$price = (string)$child->Price;
					$price = str_replace(',','.', $price);
					$price = number_format($price, 4);
					
				} 
				elseif((int)$child->PriceListID == 1 && (int)$child->Quantity > 0) 
				{
					$tierPrice = (string)$child->Price;
					$tierPrice = str_replace(',','.', $tierPrice);
					$tierPrice = number_format($tierPrice, 4);
					$qty = (int)$child->Quantity;
					$newtier = array(
		        				'website_id' => 0, 
		        				'all_groups' => 1, 
								'cust_group' => 32000, 
								'price_qty' => $qty, 
								'price' => $tierPrice,
								'delete' => ''
        					);
        			array_push($tierArray, $newtier);
        			$tiers = true;
        			
				}
			}
		} 
		elseif ($length == 1) 
		{
			$price			= (string)$xml->Prices->Price->Price;
			$price			= str_replace(',','.', $price);
			$price        	= number_format($price, 4);
		

		}
		 else
		 {
			// price missing, leave to zero
		}
		
         // catalogs
		$root_cat_id 	= Mage::app()->getStore($storeId)->getRootCategoryId();
		$categories = $this->getCategories($root_cat_id ,$storeId);
		$magentoCategories = array();
		$length = count($xml->Catalogs->Catalog);
		if($length > 0)
		 {
			foreach ($xml->Catalogs->children() as $child)
			 {
				$catalogDdgId = (string)$child->CatalogID;
				$searchDdg = 'emalo_'.$catalogDdgId;
				$key = array_search($searchDdg, $categories);
				$magentoCategoryId = $categories[$key+1];
				array_push($magentoCategories, $magentoCategoryId);
				
			}
		} 
		
		else
		 {
			// No category
			
		}
	
		
		// variants,
		// for each variant we store a simple product,
		// then at the end, we make one configurable
		$productIdArray 	    	= array();	
		foreach($xml->Variants->children() as $child)
		{
			// only collect the dimensions
			$length 			= count($child->Dimensions->Dimension);
			$variantId			= (string)$child->VariantID;
			$sku 				= $productNumber.'-'.$variantId;
			$attributesArray 	= array();
			for($i = 0; $i < $length; $i++) 
			{
				$dimensionName 			= (string)$child->Dimensions->Dimension[$i]->DimensionName;
				$dimensionNameFinal 	= $this->smallCharsNoSpaces($dimensionName);						
				$dimensionArray			= $this->getConfigAttrbitueArray($dimensionNameFinal);
				$valueDesc 				= (string)$child->Dimensions->Dimension[$i]->ValueDesc;
				$key 					= array_search($valueDesc, $dimensionArray);
				$attributesArray[$i]['attributeName'] = $dimensionNameFinal;
				$attributesArray[$i]['attributeKey'] = $key;
			}
			
			$sql = "SELECT entity_id FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')."  WHERE sku = ?";
			$stmt = $magDb->query($sql, $sku);
			$entity = $stmt->fetch();
						
			$product = Mage::getModel('catalog/product');					     
			if ($entity) {  
			   	// update
			   	$productId = $entity['entity_id'];
			   	$product->load($productId);
			} else {
			   	// insert
			  	$product->setTypeId('simple');
				$product->setSku($sku);
			}
				
			// Varient Prices
				
			$var_price_length = count($child->VariantPrices->VariantPrice);
			
			$var_price_price = 0.0000;
		    $var_price_tierArray = array();
	      	$var_price_tiers = false;
		
		if($var_price_length > 1) 
		{
			
			// different tier prices or different price lists
			foreach($child->VariantPrices->children() as $varchild)
			{
				if((int)$varchild->PriceListID == 1 && (int)$child->Quantity == 0)
				 {
					$var_price_price = (string)$varchild->Price;
					$var_price_price = str_replace(',','.', $var_price_price);
					$var_price_price = number_format($var_price_price, 4);
					
				} 
				elseif((int)$varchild->PriceListID == 1 && (int)$child->Quantity > 0) 
				{
					$tierPrice = (string)$varchild->Price;
					$tierPrice = str_replace(',','.', $tierPrice);
					$tierPrice = number_format($tierPrice, 4);
					$qty = (int)$varchild->Quantity;
					$newtier = array(
		        				'website_id' => 0, 
		        				'all_groups' => 1, 
								'cust_group' => 32000, 
								'price_qty' => $qty, 
								'price' => $tierPrice,
								'delete' => ''
        					);
        			array_push($var_price_tierArray, $newtier);
        			$var_price_tiers = true;
        			
				}
			}
		} 
		elseif ($var_price_length == 1) 
		{
			$var_price_price			= (string)$child->VariantPrices->VariantPrice->Price;
			$var_price_price			= str_replace(',','.', $var_price_price);
			$var_price_price        	= number_format($var_price_price, 4);
		

		}
		 else
		 {
			// price missing, leave to zero
		}
				
			
			
			// Varient prices end
			
			
			$product->setName($productName);
			$product->setWebsiteIds(array($this->_websiteId)); 
	     	$product->setCategoryIds($magentoCategories); 
			$product->setAttributeSetId($attributeSetId); 
			$product->setTaxClassId(0); // to do: what tax class?
			$product->setTitle($productName);
			$product->setShortDescription($productName);
			$product->setDescription($productName);
			
			$product->setBaseUnit($baseUnit);
			$product->setLength($productLength);
			$product->setWidth($productWidth);
			$product->setHeight($productHeight);
			$product->setWeight($productWeight);
			$product->setWeightUnit($weightUnit);
			$product->setEanNumber($eanNumber);
			for($i = 0; $i < $length; $i++)
			 {
				$product->setData($attributesArray[$i]['attributeName'], $attributesArray[$i]['attributeKey']);
			}
				
				if($var_price_price)
				{			
			$product->setPrice($var_price_price);
				}
				else
				{
					$product->setPrice($price);
				}
		
			$product->setTierPrice($var_price_tierArray);	

			if($inActive == '-1') {
				$product->setStatus(2); //disabled
			} else {
				$product->setStatus(1); //enabled
			}
			$product->setVisibility($visibilityIdOf); 				

			$stockData = $product->getStockData();
			// we don't have any qty yet, so let's put it to 1
			$qty = 1;
			$stockData['qty'] = $qty;
			if($qty == 0) {
				$stockData['is_in_stock'] = 0;
			} else {
				$stockData['is_in_stock'] = 1;
			}
			$stockData['manage_stock'] = 1;
			$stockData['use_config_manage_stock'] = 0;
			$product->setStockData($stockData);
		
			try {
				$product->save();
				$productId = $product->getId();				
				array_push($productIdArray, $productId);
				$product = Mage::getModel('catalog/product');
					$product->load($productId);
					$product->setStoreId($storeId);	
					$product->save();
					
				
							
			
			} catch (Exception $e) {	
				
			}	
		
		}
	
		$sku = $productNumber;
		

		$sql = "SELECT entity_id FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')."  WHERE sku = ?";
		$stmt = $magDb->query($sql, $sku);
		$entity = $stmt->fetch();
			
		$product = Mage::getModel('catalog/product');
   
		if ($entity) {  
		  	// update
		   	$productId = $entity['entity_id'];
		   	$product->load($productId);
		} else {
		   	// insert
		   	$product->setTypeId('configurable'); // !
			$product->setSku($sku);
		}
		$product->setName($productName);
		$product->setWebsiteIds(array($this->_websiteId)); 
	    $product->setCategoryIds($magentoCategories);
		$product->setAttributeSetId($attributeSetId); 
		$product->setTaxClassId(0); // to do: what tax class?
		$product->setTitle($productName);
		$product->setShortDescription($productName);
		$product->setDescription($productName);
		
		$product->setBaseUnit($baseUnit);
		$product->setLength($productLength);
		$product->setWidth($productWidth);
		$product->setHeight($productHeight);
		$product->setWeight($productWeight);
		$product->setWeightUnit($weightUnit);
		$product->setEanNumber($eanNumber);
					
		$product->setPrice($price);
	
		$product->setTierPrice($tierArray);	

		if($inActive == '-1') {
			$product->setStatus(2); //disabled
		} else {
			$product->setStatus(1); //enabled
		}
		$product->setVisibility($visibilityIdOn);
		
		//setting up stock values for default
		
		    $stockData = $product->getStockData();
			$qty = 1;
			$stockData['qty'] = $qty;
			if($qty == 0)
			 {
				$stockData['is_in_stock'] = 0;
			} 
			else 
			{
				$stockData['is_in_stock'] = 1;
			}
			$stockData['manage_stock'] = 1;
			$stockData['use_config_manage_stock'] = 0;
			$product->setStockData($stockData);
				
		try {
			$product->save();	
			$productId = $product->getId();	
			
				$product = Mage::getModel('catalog/product');
				$product->load($productId);
				$product->setStoreId($storeId);	
				$product->setDescription($description_dutch);
				$product->setShortDescription($description_dutch);
				$product->save();
				$this->imgCleanUp($sku,$productId);
			    $this->save_img($img_url_arr,$img_des_arr,$sku);
				
			
			
	
		} catch (Exception $e) {				
			echo $e->getMessage();		
			
		}	
		
	 	//okay, we need to check(!) if the _super tables already contain these...
		$sql1 = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute')." (product_id, attribute_id, position) VALUES (?, ?, ?)";
		$sql2 = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute_label')." (product_super_attribute_id, store_id, value) VALUES (?, ?, ?)";
		$sql3 = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute_pricing')." (product_super_attribute_id, value_index, is_percent, 
			pricing_value, website_id) VALUES (?, ?, ?, ?, ?)";
		$sql4 = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_link')." (product_id, parent_id) VALUES (?, ?)";
		$sql5 = "SELECT product_super_attribute_id FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute')."  
			 WHERE product_id = ? AND attribute_id = ? AND position = ?";	
						
		$sql1Check = "SELECT product_super_attribute_id 
		FROM  ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute')." WHERE product_id = ? AND attribute_id = ?";
		$sql2Check = "SELECT value_id 
		FROM  ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute_pricing')." WHERE product_super_attribute_id = ? AND value_index = ?";
		$sql3Check = "SELECT product_id, parent_id FROM  ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_link')." 
			WHERE product_id = ? AND parent_id = ?";
			
		$sqlReplace = "REPLACE INTO ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_link')." (product_id, parent_id) VALUES (?, ?)";
		
		$sqlUpdate = "UPDATE  ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute_pricing')." SET pricing_value = ? 
			WHERE value_id = ?";
		
		$position = 0;
		for($i = 0; $i < $length; $i++) {
			$attributeName = $attributesArray[$i]['attributeName'];
			// get attribute_id for attribute 
			$sql 	= 'SELECT attribute_id FROM '.Mage::getSingleton('core/resource')->getTableName("eav_attribute").' WHERE attribute_code = ?';
			$stmt 	= $magDb->query($sql, $attributeName);
			$obj 	= $stmt->fetchObject();
			$attributeId 	=  $obj->attribute_id;	
	
			$exists = $magDb->query($sql1Check, array($productId, $attributeId));
			$findEntry = $exists->fetch();
		
			if($findEntry) {
				// it is an update, a little more complex
				$productSuperAttributeId = $findEntry['product_super_attribute_id'];

				// no price differences yet
				//foreach price that differs... check if price is there and then update
				//if price isn't there, add
//				foreach($pricesThatDiffer as $attributteOptionValueId => $priceThatDiffers) {
//					//check if price is there already, use replace into
//					$stmt = $magDb->query($sql2Check, array($productId, $attributteOptionValueId));
//					$entity = $stmt->fetch();
//					if ($entity) {
//						$valId = $entity['value_id'];
//						$stmt = $magDb->query($sqlUpdate, array($priceThatDiffers, $valId));
//					} else {
//						$stmt = $magDb->query($sql3, array($productSuperAttributeId, $attributteOptionValueId, 0, $priceThatDiffers, 0));
//					}
//				}
				// link simple product to configurable
		       	foreach($productIdArray as $productIdOfProduct) {
		       		$stmt = $magDb->query($sql3Check, array($productIdOfProduct, $productId));
       				$entity = $stmt->fetch();
       				if ($entity) {
       					// don't bother, it has been linked
       				} else {
       					$stmt = $magDb->query($sql4, array($productIdOfProduct, $productId));
       				}
		       	}
				
						
			} else {
				// new configurable, create
				$stmt = $magDb->query($sql1, array($productId, $attributeId, $position));
				$superAttributeId = $magDb->lastInsertId();
				$stmt = $magDb->query($sql2, array($superAttributeId, $this->_storeId, $attributeName));
//				//foreach price that differs...
//				foreach($pricesThatDiffer as $attributteOptionValueId => $priceThatDiffers) {
//					$stmt = $magDb->query($sql3, array($superAttributeId, $attributteOptionValueId, 0, $priceThatDiffers, 0));
//				}
		       	// link simple product to configurable
		       	foreach($productIdArray as $productIdOfProduct) {
		       		$stmt = $magDb->query($sql3Check, array($productIdOfProduct, $productId));
       				$entity = $stmt->fetch();
       				if ($entity) {
       					// don't bother, it has been linked
       				} else {
       					$stmt = $magDb->query($sql4, array($productIdOfProduct, $productId));
       				}
		       	}
  
				
			}
		}
		unset($product);  			
	}
	
	public function getConfigAttrbitueArray($attributeName)
	{
		$magDb 		= Mage::getSingleton('core/resource')->getConnection('core_write');

		$configAtts = array();
		$sql = 'SELECT attribute_id FROM '.Mage::getSingleton('core/resource')->getTableName("eav_attribute").'  WHERE attribute_code = ?';
		$stmt = $magDb->query($sql, $attributeName);
		$obj = $stmt->fetchObject();
		$attributeId =  $obj->attribute_id;	
		
		$sql1 = "SELECT val.value, val.option_id FROM ".Mage::getSingleton('core/resource')->getTableName('eav_attribute_option')." AS opt JOIN ".Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value')." AS val ON opt.option_id = val.option_id WHERE opt.attribute_id = '".$attributeId."'";
		$stmt1 = $magDb->query($sql1);
		$results = $stmt1->fetchAll();
		foreach($results as $result) {
			$id = $result['option_id'];
			$configAtts[$id] = $result['value'];
		}

		return $configAtts;
	} 	
	
	//Fuction to clean images of product
	public function imgCleanUp($sku,$productId)
	{
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
$sql = "DELETE FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery_value')." WHERE value_id IN (SELECT value_id FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery')." WHERE entity_id=".$productId.")";
		
$sql_2 = "DELETE FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery')." WHERE entity_id=".$productId."";
		try {
			$db->query($sql);
			$db->query($sql_2);
		} 
		catch (Exception $e)
		 {
			 return $e->getMessage();
		}	
		return $this;	
	}
	
	// Function to save image of product
	public function save_img($img_url_arr,$img_des_arr,$sku)
	{
		$img_api = Mage::getModel('catalog/product_attribute_media_api');
		$count_img=count($img_url_arr);
		for($i=0;$i<$count_img;$i++)
		{
			$fileName = $img_url_arr[$i];
			$name = $img_des_arr[$i];
			$label = $img_des_arr[$i];
    	    $position = 1;  
			
			 $newImage = array(
                         'file' => array(
                                         'content' => base64_encode(file_get_contents($fileName)),
                                         'mime'    => 'image/jpeg',
                                         'name'	  => $name
                                         ),
                         'label'    => $label,
                         'position' => $position,
                         'types'    => ($position == '1') ? array('image', 'small_image', 'thumbnail')
						 :array('no_selection', 'no_selection','no_selection'),
                         'exclude'  => 0
                              );
							  
        if (isset($newImage)) 
		{
            try 
			{
                $img_api->create($sku, $newImage);
            } 
			catch (Exception $e) 
			{
               
              return $e->getMessage();
            }
        }
		
		}
	}
	//*************************************************  Adding Product Functions END **************************************************
	
	
	
	//********************************  Adding Categories ******************************
	

// Function for Build Category via catalog xml	
	 function buildCatalog($xml, $storeId, $websiteId)
	{
		$noErrors				= true;
		$root_cat_id 	= Mage::app()->getStore($storeId)->getRootCategoryId();
		$categories 			= array();
		$subcategories 			= array();
		$descriptions			= array();	
		$parents				= array();
		$products				= array();
		$actives				= array();	
		$img_name               = array();

		$length = count($xml->Catalog);		
		
		for($i = 0; $i < $length; $i++)
		 {
			$categoryId 			= (string)$xml->Catalog[$i]->Details->CatalogID;
			$categoryName 			= (string)$xml->Catalog[$i]->Details->CatalogName;
			$categoryDescription 	= (string)$xml->Catalog[$i]->Details->CatalogDesc;
			$categoryParent		 	= (int)$xml->Catalog[$i]->Details->ParentID;
			$categoryActive		 	= (int)$xml->Catalog[$i]->Details->InActive;
			
			
			//adding image for category
			$img_count=count($xml->Catalog[$i]->Images->Image);	
			
			if($img_count>0)
			{
				$image_dir = Mage::getBaseDir().DS.'media'.DS.'catalog'. DS .'category'.DS;
				  if (!file_exists($image_dir)) 
				  {
					if (!mkdir($image_dir,0777,true)) 
					{
					throw new Exception ('failed to create dir, check your permission in media');
					}
				  }
				  
				for($im_cnt=0;$im_cnt<$img_count;$im_cnt++)	
				{
					$img_name[$categoryId]=$xml->Catalog[$i]->Images->Image->DocumentDesc;
					$imgname=$xml->Catalog[$i]->Images->Image->DocumentDesc;
					$img_url=$xml->Catalog[$i]->Images->Image->Url;
					$mag_img_url=$image_dir.DS.$imgname;
					$img_res=copy($img_url,$mag_img_url);
	
				}
				
			}
			
			// Products?
			$nrOfProducts = count($xml->Catalog[$i]->Products->Product);
			

			if($nrOfProducts > 0)
			 {
				$allSkus = array();
				foreach($xml->Catalog[$i]->Products->children() as $child)
				{
					$sku = (string)$child->ProductNumber;
					array_push($allSkus, $sku);
				}
				$products[$categoryId] = $allSkus;
			}

			if($categoryParent == 0) 
			{
				
				if(!array_key_exists($categoryId, $categories)) {
					$categories[$categoryId] = $categoryName;
					$descriptions[$categoryId] = $categoryDescription;
					$actives[$categoryId] = $categoryActive;
				}
			} 
			else 
			{
				// subcategory
				
				if(!array_key_exists($categoryId, $categories)) {
					$subcategories[$categoryId] = $categoryName;
					$descriptions[$categoryId] = $categoryDescription;
					$parents[$categoryId] = $categoryParent;
					$actives[$categoryId] = $categoryActive;
				}
			}
		}
		$noErrors = $this->addCategories($categories, $descriptions, $actives, $products,$root_cat_id,$storeId, $websiteId,$img_name);
		if($noErrors) 
		{
$noErrors = $this->addSubCategories($subcategories, $descriptions, $actives, $parents, $products,$root_cat_id,$storeId, $websiteId,$img_name);
		} 
		else 
		{
			

		}
		

		return $noErrors;
	}



// Function for Build Category via Configuration XML
	 function buildCatalog_config($xml, $storeId, $websiteId)
	{
		$noErrors				= true;
		$root_cat_id 	= Mage::app()->getStore($storeId)->getRootCategoryId();
		$categories 			= array();
		$subcategories 			= array();
		$descriptions			= array();	
		$parents				= array();
		$products				= array();
		$actives				= array();	
		$img_name               = array();

		$length = count($xml->Catalog);		
		
		for($i = 0; $i < $length; $i++) 
		{
			$categoryId 			= (string)$xml->Catalog[$i]->CatalogID;
			$categoryName 			= (string)$xml->Catalog[$i]->CatalogName;
			$categoryDescription 	= (string)$xml->Catalog[$i]->CatalogDesc;
			$categoryParent		 	= (int)$xml->Catalog[$i]->ParentID;
			$categoryActive		 	= (int)$xml->Catalog[$i]->InActive;
			
			
			//adding image for category
			$img_count=count($xml->Catalog[$i]->Images->Image);	
			
			if($img_count>0)
			{
				$image_dir = Mage::getBaseDir().DS.'media'.DS.'catalog'. DS .'category'.DS;
				  if (!file_exists($image_dir)) 
				  {
					if (!mkdir($image_dir,0777,true)) 
					{
					throw new Exception ('failed to create dir, check your permission in media');
					}
				  }
				  
				for($im_cnt=0;$im_cnt<$img_count;$im_cnt++)	
				{
					$img_name[$categoryId]=$xml->Catalog[$i]->Images->Image->DocumentDesc;
					$imgname=$xml->Catalog[$i]->Images->Image->DocumentDesc;
					$img_url=$xml->Catalog[$i]->Images->Image->Url;
					$mag_img_url=$image_dir.DS.$imgname;
					$img_res=copy($img_url,$mag_img_url);
	
				}
				
			}
			
			// Products?
			$nrOfProducts = count($xml->Catalog[$i]->Products->Product);
			

			if($nrOfProducts > 0)
			 {
				$allSkus = array();
				foreach($xml->Catalog[$i]->Products->children() as $child)
				{
					$sku = (string)$child->ProductNumber;
					array_push($allSkus, $sku);
				}
				$products[$categoryId] = $allSkus;
			}

			if($categoryParent == 0) 
			{
				
				if(!array_key_exists($categoryId, $categories)) {
					$categories[$categoryId] = $categoryName;
					$descriptions[$categoryId] = $categoryDescription;
					$actives[$categoryId] = $categoryActive;
				}
			} 
			else 
			{
				// subcategory
				
				if(!array_key_exists($categoryId, $categories)) {
					$subcategories[$categoryId] = $categoryName;
					$descriptions[$categoryId] = $categoryDescription;
					$parents[$categoryId] = $categoryParent;
					$actives[$categoryId] = $categoryActive;
				}
			}
		}
		 
		$noErrors = $this->addCategories($categories, $descriptions, $actives, $products,$root_cat_id,$storeId, $websiteId,$img_name);
		if($noErrors) 
		{
			
$noErrors = $this->addSubCategories($subcategories, $descriptions, $actives, $parents, $products,$root_cat_id,$storeId, $websiteId,$img_name);

		} 
		else 
		{
			

		}
		

		return $noErrors;
	}




		
	/**
	 * addCategories
	 * 
	 * @param array $categories
	 */
	public function addCategories($categories, $descriptions, $actives, $products,$root_cat_id,$storeId,$websiteId,$img_name)
	{
		
		$catApi 		= Mage::getModel('catalog/category_api');
		$noErrors		= true;
		
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); // needed due to error in Magento		
		$magDb 			= Mage::getSingleton('core/resource')->getConnection('core_write');
		
		foreach($categories as $categoryId => $categoryName)
		 {
			$categoryArray 	= $this->getCategories($root_cat_id,$storeId);
			$image_name=$img_name[$categoryId];
			$ddg_Id = 'emalo_'.$categoryId;
			$description = $descriptions[$categoryId];
			$active = 1;
			if ($actives[$categoryId] == -1) {
				$active = 0; // set category inactive
			}
			$magentoId = '';
			$skus = $products[$categoryId];
			
			
			if(!in_array($ddg_Id, $categoryArray))
			 {
				if ( trim($categoryName) != '' ) 
				{	
					$categoryData = array(
						'name' 				=> $categoryName, 
	        			'default_sort_by' 	=> 'position', 
	        			'available_sort_by' => 'position',
						'description' 		=> $description,
	        			'emalo_cat' 	    => $categoryId, 
	        			'is_active' 		=> $active, 
	        			'include_in_menu'   => 2,
						'image'             => $image_name
					);
					try
					 {
						$magentoId = $catApi->create($root_cat_id,$categoryData);
						
					} catch (Exception $e) 
					{
						return  $e->getMessage();
						$noErrors = false;
					}
				}
			}
			 else 
			 {
				//category already exists, let's update
				$key = array_search($ddg_Id, $categoryArray);
				$magentoId = $categoryArray[$key+1];				
				if ( trim($categoryName) != '' )
				 {	
					$categoryData = array(
						'name' 				=> $categoryName, 
	        			'default_sort_by' 	=> 'position', 
	        			'available_sort_by' => 'position',
						'description' 		=> $description,
	        			'emalo_cat' 	    => $categoryId, 
	        			'is_active' 		=> $active, 
	        			'include_in_menu'   => 2,
						'image'             => $image_name
					);
					try
					 {
						$catApi->update($magentoId, $categoryData);
						
					} 
					catch (Exception $e)
					 {
						return  $e->getMessage();
						$noErrors = false;
					}
				}
			}
			
			
			// first remove all current products
			$assignedProducts = $catApi->assignedProducts($magentoId,$storeId);
			foreach ($assignedProducts as $assignedProduct) {
				
				$catApi->removeProduct($magentoId,$assignedProduct['product_id']);
			}

			foreach($skus as $sku) {
				$sql = "SELECT entity_id FROM  ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')."  WHERE sku = ?";
				$stmt = $magDb->query($sql, $sku);
				$entity = $stmt->fetch();
							
				$product = Mage::getModel('catalog/product');					     
				if ($entity) {  
				   	$productId = $entity['entity_id'];
				   	$catApi->assignProduct($magentoId, $productId);
					
				} else {
				   	// product doesn't exist
					
				}
			}
		}
		return $noErrors;
	}

	/**
	 * addCategories
	 * 
	 * @param array $categories
	 */
	public function addSubCategories($categories, $descriptions, $actives, $parents, $products,$root_cat_id,$storeId, $websiteId,$img_name)
	{
		
		$catApi 		= Mage::getModel('catalog/category_api');
		$noErrors		= true;
		
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); // needed due to error in Magento		
		$magDb 			= Mage::getSingleton('core/resource')->getConnection('core_write');

		foreach($categories as $categoryId => $categoryName) 
		{
			$categoryArray 	= $this->getCategories($root_cat_id, $storeId);
			$image_name=$img_name[$categoryId];
			$ddg_Id = 'emalo_'.$categoryId;
			$description = $descriptions[$categoryId];
			if($actives[$categoryId] == -1) {
				//inActive
				$active = 0;
			} else {
				$active = 1;
			}
			$magentoId = '';
			$skus = $products[$categoryId];

			

			// get magento Parent Category ID:
			$parentDdgCatId = 'emalo_'.$parents[$categoryId];

			$key = array_search($parentDdgCatId, $categoryArray);
			$magentoParentId = $categoryArray[$key+1];
			if(!in_array($ddg_Id, $categoryArray)) {
				if ( trim($categoryName) != '' ) {	
					$categoryData = array(
						'name' 				=> $categoryName, 
	        			'default_sort_by' 	=> 'position', 
	        			'available_sort_by' => 'position',
						'description' 		=> $description,
	        			'emalo_cat' 	    => $categoryId, 
	        			'is_active' 		=> $active, 
	        			'include_in_menu'   => 2,
						'image'             => $image_name
					);
					try
					 {
						$magentoId = $catApi->create($magentoParentId, $categoryData, $storeId);
						
						
					} 
					catch (Exception $e)
					 {
						return $e->getMessage();
						$noErrors = false;
					}
				}
			} 
			else
			 {
				//category already exists, let's update
				$key = array_search($ddg_Id, $categoryArray);
				$magentoId = $categoryArray[$key+1];

				if ( trim($categoryName) != '' ) {	
					$categoryData = array(
						'name' 				=> $categoryName, 
	        			'default_sort_by' 	=> 'position', 
	        			'available_sort_by' => 'position',
						'description' 		=> $description,
	        			'emalo_cat' 	    => $categoryId, 
	        			'is_active' 		=> $active, 
	        			'include_in_menu'   => 2,
						'image'             => $image_name
					);
					try
					 {
						$catApi->update($magentoId, $categoryData);
						
					} catch (Exception $e)
					 {
						return $e->getMessage();
						$noErrors = false;
					}
				}
			}
			
			
			// first remove all current products
			$assignedProducts = $catApi->assignedProducts($magentoId, $storeId); // store id is needed
			foreach ($assignedProducts as $assignedProduct) 
			{
				$catApi->removeProduct($magentoId,$assignedProduct['product_id']);
			}
			
			foreach($skus as $sku)
			 {
				$sql = "SELECT entity_id FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')."  WHERE sku = ?";
				$stmt = $magDb->query($sql, $sku);
				$entity = $stmt->fetch();
							
				$product = Mage::getModel('catalog/product');					     
				if ($entity) {  
				   	$productId = $entity['entity_id'];
				   	$catApi->assignProduct($magentoId, $productId);
				   	

				} 
				else 
				{

				   	// product doesn't exist
				   
				}
			}
		}
		return $noErrors;
	}	
	
	public function getCategories($parentId, $storeId)
    {
        $collection = Mage::getModel('catalog/category')->getCollection()
           	  	->setStoreId($storeId)//$this->_getStoreId($store))
            	->addAttributeToSelect('name')
           		->addAttributeToSelect('emalo_cat');    
        $i=0;
        // we want: categoryName, PPPCategoryId, CategoryId, ParentId	
    	foreach ($collection as $cat) {
    		if($cat->getName() == "Root Catalog") {
    			// do nothing
    		} elseif($cat->getName() <> '') {
	    		$result[$i] = $cat->getName();
	    		$i++;
				$cat_attrib=$cat->getAttributes();
                $cat_att_id=$cat_attrib['emalo_cat']->getFrontend()->getValue($cat);
               	$result[$i] = "emalo_".$cat_att_id;
	    		$i++;
	    		$result[$i] = $cat->getId();
	    		$i++;
	    		$result[$i] = $cat->getParentId();
	    		$i++;	    		
    		}
		}
        return $result;
    }
	
	// ********************************************* Add Categories Functions END **********************************************************
	
	
	//**************  Function to add configuration ******************
	
	public function retrieveConfigAtts($xml, $storeId, $websiteId, $rootCatalogId)
	{
		

		$noErrors				= true;
		$this->_storeId 		= $storeId;
		$this->_websiteId 		= $websiteId;
		$this->_rootCatalogId 	= $rootCatalogId;
		
		

		$length = count($xml->Dimension);		
		
		for($i = 0; $i < $length; $i++)
		 {
			$attribute = (string)$xml->Dimension[$i]->Details->DimensionName; 
			$attribute = $this->smallCharsNoSpaces($attribute);
			// is it there yet?
			$attributeId = $this->getAttributeIfExists($attribute);
			if($attributeId == 0)
			 {
				$attributeId = $this->createAttribute($attribute);
			}
			if($attributeId > 0)
			 {
				
				$length2 	= count($xml->Dimension[$i]->Values->Value);
				$tags		= array();	
				for($j = 0; $j < $length2; $j++)
				 {
					$tagId 		= (string)$xml->Dimension[$i]->Values->Value[$j]->ValueID;
					$tagName 		= (string)$xml->Dimension[$i]->Values->Value[$j]->ValueDesc;
					if(!array_key_exists($tagId, $tags)) 
					{
						$tags[$tagId] = $tagName;
					}
				}
				$this->addNewTags($tags, $attributeId);
			} 
			else 
			{
				
			}
		}
		
		return $noErrors;
	}
	
	public function getAttributeIfExists($attribute)
	{
		$magDb 		= Mage::getSingleton('core/resource')->getConnection('core_write');
		
		// get attribute_id for attribute
		$sql = 'SELECT attribute_id FROM '.Mage::getSingleton('core/resource')->getTableName('eav_attribute').'  WHERE attribute_code = ?';
		$stmt = $magDb->query($sql, $attribute);
		$entity = $stmt->fetch();
		if ($entity)
		 {
		    return $entity['attribute_id'];
		} 
		else 
		{
			return 0;
		}			
	}	
	
	public function createAttribute($attributeName) 
	{
		$magDb = Mage::getSingleton('core/resource')->getConnection('core_write');

		// get product entity type
		$entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getEntityTypeId();
		
		$data = array (
			'attribute_group_id' => '32',
		  	'entity_type_id'  => $entityTypeId,         // 4  product
		  	'attribute_code'  => $attributeName,
		  	'backend_type'    => 'int',     // MySQL-DataType
		  	'frontend_input'  => 'select', // Type of the HTML-Form-Field
			'source_model'	  => '',
		  	'is_global'       => '1',
		  	'is_visible'      => '1',
		  	'is_required'     => '0',
		  	'is_user_defined' => '1',
		  	'frontend_label'  => $attributeName,
			'is_searchable'	  => '0',
			'is_visible_in_advanced_search' => '0'
		);
		
		// save attribute
		$attribute = new Mage_Eav_Model_Entity_Attribute();
		$attribute->setStoreId(0)
					->addData($data);
		try
		 {
			$attribute->save();
		} 
		catch (Exception $e)
		 {
			return $e->getMessage();
		}
		
		// get attribute id
		$attributeId = Mage::getModel('catalog/entity_attribute')->loadByCode($entityTypeId, $attributeName)->getId(); 
		$sql = "SELECT attribute_set_id FROM ".Mage::getSingleton('core/resource')->getTableName('eav_attribute_set')."  WHERE attribute_set_name = 'Default' AND entity_type_id = ?";
		$stmt = $magDb->query($sql, $entityTypeId);
		$entity = $stmt->fetch();
		
		if ($entity)
		 {
			$myAttributeSetId = $entity['attribute_set_id'];
			$sql = "SELECT attribute_group_id FROM ".Mage::getSingleton('core/resource')->getTableName('eav_attribute_group')." WHERE attribute_group_name = 'General' AND attribute_set_id =?";
			$stmt = $magDb->query($sql, $myAttributeSetId);
			$entity2 = $stmt->fetch();
		
			if ($entity2) 
			{
				
				$myAttributeGroupId = $entity2['attribute_group_id'];
				$sql = "SELECT MAX(sort_order) AS maxsort from ".Mage::getSingleton('core/resource')->getTableName('eav_entity_attribute')." where attribute_set_id = ?";
				$stmt = $magDb->query($sql, $myAttributeSetId);
				$entity2 = $stmt->fetch();
				$maxSortOrder = $entity2['maxsort'] + 1;
				$sql = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('eav_entity_attribute')." (entity_type_id, attribute_set_id, attribute_group_id, 
				attribute_id, sort_order) VALUES (?, ?, ?, ?, ?)";
				$stmt = $magDb->query($sql, array($entityTypeId, $myAttributeSetId, $myAttributeGroupId, $attributeId, $maxSortOrder));
		
			}
		}
		return $attributeId;
	}
	
	public function addNewTags($tags, $attributeId)
	{
		$magDb = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		foreach($tags as $tagId => $tagName) 
		{
			$tagName = trim($tagName);	
			// Check if tag exists
$sql = "SELECT val.value FROM ".Mage::getSingleton('core/resource')->getTableName('eav_attribute_option')." AS opt JOIN ".Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value')." AS val ON opt.option_id = val.option_id WHERE val.value = '".$tagName."' AND opt.attribute_id = '".$attributeId."'";
			$stmt = $magDb->query($sql);
			$entity = $stmt->fetch();
		
		    if ($entity)
			 {
		    	// do nothing, it's there
		    }
			 else 
			 {
				$sql = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('eav_attribute_option')." (attribute_id) VALUES (?)";
				$stmt = $magDb->query($sql, $attributeId);
				$optionId = $magDb->lastInsertId();
				$sql = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value')." (option_id, store_id, value) VALUES (?,?,?)";
				$stmt = $magDb->query($sql, array($optionId, 0, $tagName));
				
		    }
		}
	}

//**********************************************************************  Function to Configuration END **************************************




//*******************  Function to create Stock Values **********************************

public function setStock($xml, $storeId, $websiteId)
	{
	
		$noErrors				= true;
		
		$length = count($xml->Product);		

		foreach($xml->children() as $child)
		{
			
			if($child->getName() == 'Product') 
			{
				$this->updateProductStock($child);	
			} 
		}
			
		
		return $noErrors;
	}

public function updateProductStock($xml) 
	{
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); // needed due to error in Magento		
		$magDb 			= Mage::getSingleton('core/resource')->getConnection('core_write');	

				
		// details
		$productDdgId 	= (string)$xml->ProductID;
		$productNumber 	= (string)$xml->ProductNumber;
		$productStock 	= (string)$xml->Stock;
		$valuationPrice	= (string)$xml->ValuationPrice;
		$variantID		= (string)$xml->VariantID;
		
		
		if($variantID != '0') 
		{
			$sku = $productNumber.'-'.$variantID;
		} 
		else
		 {
			$sku = $productNumber;
		}
		
		// check if product exists
		
		$sql = "SELECT entity_id FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')." WHERE sku = ?";
		$stmt = $magDb->query($sql, $sku);
		$entity = $stmt->fetch();
					
		$product = Mage::getModel('catalog/product');					     
		if ($entity)
		 {  
		   	$productId = $entity['entity_id'];
		   	$product->load($productId);
		   	$stockData = $product->getStockData();
			
			$qty = $productStock;
			$stockData['qty'] = $qty;
			if($qty == 0) 
			{
				$stockData['is_in_stock'] = 0;
			} 
			else
			 {
				$stockData['is_in_stock'] = 1;
			}
			$stockData['manage_stock'] = 1;
			$stockData['use_config_manage_stock'] = 0;
			$product->setStockData($stockData);
			
			try {
				$product->save();	
				$productId = $product->getId();
		
				
			} 
			catch (Exception $e) 
			{				
				return $e->getMessage();		
					
			}	
		} 
		else
		 {
		  	// error, product should be there!
			
		}		
		unset($product);  			
	}


//*********************************************  End of Stock function *****************************************	
	
		
	
	
	
	
	//***********************************************************  License Validation Function *************************************
	
	
	public function validate()
	{
		return true;	
	}

	public function validate2()
	{
		$icAccessArea = Mage::getStoreConfig('emalo_options/export/emaloAccessArea');
		$icCustomerNumber = Mage::getStoreConfig('emalo_options/export/emaloCustomerNumber');
		
		$params = implode('',
		array(
					'sAccessArea' 		=> $icAccessArea, 
					'sCustomerNumber' 	=> $icCustomerNumber,
		));
		$signMac = Zend_Crypt_Hmac::compute('cBbTxKP1wGhaBDdXjL8Lc#DdvKan%!@Z#8AzN2nE!CJKqUXPZFiU', 'sha1', $params);
		$license = base64_encode(pack('H*',$signMac));
		$backendKey		= trim(Mage::getStoreConfig('emalo_options/export/key'));
		if (strcmp($backendKey, $license) === 0)
		 {
					return true;	
		 }
		
			return false;
		
	} 
	  
		//***********************************************************  License Validation Function END  *************************************
		
		public function smallCharsNoSpaces($attributeName)
	{
		$attributeName = strtolower($attributeName);
		$attributeName = str_replace(' ', '_', $attributeName);
		return $attributeName;
	}	  
	  
}