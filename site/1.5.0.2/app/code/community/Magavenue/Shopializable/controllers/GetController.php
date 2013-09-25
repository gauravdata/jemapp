<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Magavenue
 * @package     Magavenue_Shopializable
 * @copyright   Copyright (c) 2010 Magavenue (http://www.magavenue.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magavenue_Shopializable_GetController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
	
		$type = $this->_request->getParam('type');

                /* Recuperation configuration */
                $this->readFile('configuration');

                echo '####CONFIGURATION-FILE####';

                /* Recuperation categories */
                $this->readFile('categories');

                echo '####CATEGORIES-FILE####';

                /* Recuperation CMS */
                $this->readFile('CMS');

                echo '####CMS-FILE####';

                /* Recuperation file */
                if($type == 'category'){
                        $id_category = $this->_request->getParam('id');
			$pagination = $this->_request->getParam('pagination');
			$filename = 'category'.$id_category.'-'.$pagination;
                        if( !$this->readFile($filename) ){
                                $this->generateCategory($id_category);
                                $this->readFile($filename);
                        }

		}else if($type == 'product'){
			$id_product = $this->_request->getParam('id');
			$filename = 'product'.$id_product;

                        if( !$this->readFile($filename) ){
                            $this->generateProduct($id_product);
                            $this->readFile($filename);
                        }

		}else if($type == 'bestSales'){
			$pagination = $this->_request->getParam('pagination');
			$filename = 'bestSales-'.$pagination;
                        $this->readFile($filename);
		}else if($type == 'pricesdrop'){
			$pagination = $this->_request->getParam('pagination');
			$filename = 'pricesdrop-'.$pagination;
                        $this->readFile($filename);
		}else if($type == 'newproducts'){
			$pagination = $this->_request->getParam('pagination');
			$filename = 'newproducts-'.$pagination;
                        $this->readFile($filename);
		} else{
			$filename = $type;
                        $this->readFile($filename);
                }
                

	}

        public function readFile($file){
            $filename=dirname(__FILE__).'/../xml/'.$file.'.xml';
            if( is_file($filename) ){
                $handle = fopen($filename, "r");
                echo $contents = fread($handle, filesize($filename));
                fclose($handle);
                return true;
            }
            
            return false;
        }
 
        public function generateProduct($id_product) {
	
		/* Recuperation du produit */
		$product = Mage::getModel ( 'catalog/product' )->load ( intval($id_product) );

		/* Creation du fichier Xml */
		$dom = new DomDocument();
		$document = $dom->createElement("document");
		$domProduct = $dom->createElement("product");
		$domProduct->setAttribute("id_product", $id_product);
		$category_id = $product->getCategoryIds();
		$domProduct->setAttribute("id_category", intval($category_id[0]));
		$domProduct->setAttribute("quantity", intval($product->getStockItem('qty')));
		$domProduct->setAttribute("solde", 0);
		if ($product->isGrouped()){
                    $domProduct->setAttribute ( "reduction", 0 );
                    $domProduct->setAttribute ( "price", Mage::helper('core')->currency( $this->getMinimumPriceOptionValue($product) ) );
                }else if ($product->getSpecialPrice ()) {
			$domProduct->setAttribute ( "reduction", 1 );
			$domProduct->setAttribute ( "pricereduct", Mage::helper('core')->currency( $product->getSpecialPrice() ) );
		} else {
			$domProduct->setAttribute ( "reduction", 0 );
			$domProduct->setAttribute("price", Mage::helper('core')->currency( $product->getPrice()) );

		}

                $domProduct->setAttribute("ecotax", 0);
                $domProduct->setAttribute("tax", 19.6);
                $domProduct->setAttribute("manufacturer", $product->getManufacturer());
		$domProduct->setAttribute("supplier", 0);

		/* Nom du Produit */
		$domProductName = $dom->createElement("product_name");
		$domProductNameValue = $dom->createTextNode($product->getName());
  		$domProductName->appendChild($domProductNameValue);
  		$domProduct->appendChild($domProductName);

  		/* Nom de la Categorie*/
  		$category = Mage::getModel ( 'catalog/category' )->load ( intval($category_id) );
		$domCategoryName = $dom->createElement("category_name");
		$domCategoryNameValue = $dom->createTextNode($category->getName());
  		$domCategoryName->appendChild($domCategoryNameValue);
  		$domProduct->appendChild($domCategoryName);

		/* Image de Base */
  		if($product->getImage()){
  			$domProductImageCover = $dom->createElement("image_cover");
			$domProductImageCoverValue = $dom->createTextNode( Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product->getImage() );
  			$domProductImageCover->appendChild($domProductImageCoverValue);
  			$domProduct->appendChild($domProductImageCover);
  		}
  		else{
  			$domProductImageCover = $dom->createElement("image_cover");
			$domProductImageCoverValue = $dom->createTextNode("");
  			$domProductImageCover->appendChild($domProductImageCoverValue);
  			$domProduct->appendChild($domProductImageCover);
  		}

		/* Images */
  		$images = $product->getMediaGallery('images');

   		foreach ($images AS $k => $image){
    	  	$domProductImage = $dom->createElement("image");
			$domProductImageValue = $dom->createTextNode( Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$image['file']);
	  		$domProductImage->appendChild($domProductImageValue);
	  		$domProduct->appendChild($domProductImage);

    	}

                /* Description du Produit */
		$domProductDescription = $dom->createElement("description");
		$domProductDescriptionValue = $dom->createTextNode($product->getDescription());
  		$domProductDescription->appendChild($domProductDescriptionValue);
  		$domProduct->appendChild($domProductDescription);

  		/* Courte Description du produit */
		$domProductDescriptionShort = $dom->createElement("description_short");
		$domProductDescriptionShortValue = $dom->createTextNode($product->getShortDescription());
  		$domProductDescriptionShort->appendChild($domProductDescriptionShortValue);
  		$domProduct->appendChild($domProductDescriptionShort);

  		/* Lien du Produit */
		$domProductLink = $dom->createElement("link");
		$domProductLinkValue = $dom->createTextNode($product->getUrlPath());
  		$domProductLink->appendChild($domProductLinkValue);
  		$domProduct->appendChild($domProductLink);

  		/* Tags du Produit */
  		$domProductTags = $dom->createElement("tags");
   		/*if (Tag::getProductTags(intval($id_product))){
    		foreach ($product->tags[intval(Configuration::get('PS_LANG_DEFAULT'))] AS $tagName){
				$domProductTag = $dom->createElement("link");
				$domProductTagValue = $dom->createTextNode(Tools::safeOutput($tagName));
		  		$domProductTag->appendChild($domProductTagValue);
		  		$domProductTags->appendChild($domProductTag);
    		}
   		}*/

		$domProduct->appendChild($domProductTags);

		/* Accessoires du Produit */
		if ($accessories = $product->getLinksExist()){
    		$domProductAcessories = $dom->createElement("accessories");
    		foreach ($accessories as $accessory){
				$domProductAccessory = $dom->createElement("accessory");
				$domProductAccessoryValue = $dom->createTextNode("");
		  		$domProductAccessory->appendChild($domProductAccessoryValue);
		  		$domProductAcessories->appendChild($domProductAccessory);
    		}
  			$domProduct->appendChild($domProductAcessories);
		}

		$document->appendChild($domProduct);
		$dom->appendChild($document);

		/* Sauvegarde du fichier xml */
		if($dom->save(dirname ( dirname ( __FILE__ ) ) . '/xml/product' . $id_product .'.xml' ))
			return true;
		return false;
	}

        public function getMinimumPriceOptionValue($product){
            
            $sum = 0;
            $firstTime = true;
            $products = $product->getTypeInstance()->getAssociatedProducts();
            foreach ($products as $product){
                $currentPrice = $product->getPrice();
                if ($product->getSpecialPrice() > 0) {
                    $currentPrice = $product->getSpecialPrice();
                }
                if($currentPrice < $sum || $firstTime){
                    $firstTime = false;
                    $sum = $currentPrice;
                }
            }
            return $sum;
	}


        public function generateCategory($id_category){
           
		/* Nombre de Produit par page */
		$nb_product = Mage::getStoreConfig('shopializable/shopializable/boutique_nbr')!=''?Mage::getStoreConfig('shopializable/shopializable/boutique_nbr'):'9';
                $storeId    = Mage::app()->getStore()->getId();

                $category = Mage::getModel('catalog/category')->load( intval($id_category) );

                $products = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect('*')
                    ->addCategoryFilter($category)
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('visibility',array('in' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH))
                    ->setStoreId( $storeId )
                    ->load();
                $collections = $products->toArray();
                
		/* Calcul du nombre de Pages */
		$categoryNbProducts = count ( $collections );
		$nbrPage = ceil ( $categoryNbProducts / $nb_product );
		$success = false;
		for($page = 0; $page < ($nbrPage == 0 ? 1 : $nbrPage); $page ++) {
                	/* Creation du fichier xml */
			$dom = new DomDocument ( );
			$document = $dom->createElement ( "document" );
			$domCategory = $dom->createElement ( "category" );
			$domCategory->setAttribute ( "nombre", $categoryNbProducts );
			$domCategory->setAttribute ( "img", $category->getImage () );

			/* Description de la Categorie*/
			$domCategoryDesc = $dom->createElement ( "description" );
			$domCategoryDescValue = $dom->createTextNode ( $category->getDescription () );
			$domCategoryDesc->appendChild ( $domCategoryDescValue );
			$domCategory->appendChild ( $domCategoryDesc );

			/* Nom de la Categorie */
			$domCategoryName = $dom->createElement ( "name" );
			$domCategoryNameValue = $dom->createTextNode ( $category->getName () );
			$domCategoryName->appendChild ( $domCategoryNameValue );
			$domCategory->appendChild ( $domCategoryName );

                        $productsPaginate = Mage::getResourceModel('catalog/product_collection')
                            ->addAttributeToSelect('*')
                            ->addCategoryFilter($category)
                            ->addAttributeToFilter('status', 1)
                            ->addAttributeToFilter('visibility',array('in' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH))
                            ->setStoreId( $storeId )
                            ->setPageSize($nb_product)
                            ->setCurPage($page+1)
                            ->load();
                                           
                        $collectionsPaginate = $productsPaginate->toArray();

			foreach ( $collectionsPaginate as $id_product => $collection ) {
				        /* Recuperation du Produit */
                            $product = Mage::getModel ( 'catalog/product' )->load ( $id_product );

                            /* Generate product xml */
                            $domCategoryProduct = $dom->createElement ( "product" );
                            $domCategoryProduct->setAttribute ( "id", $id_product );
                            $domCategoryProduct->setAttribute ( "quantity", intval($product->getStockItem('qty')));
                            if ($product->isGrouped()){
                                    $domCategoryProduct->setAttribute ( "reduction", 0 );
                                    $domCategoryProduct->setAttribute ( "price", Mage::helper('core')->currency( $this->getMinimumPriceOptionValue($product) ) );
                            } else if ($product->getSpecialPrice ()) {
                                    $domCategoryProduct->setAttribute ( "reduction", 1 );
                                    $domCategoryProduct->setAttribute ( "price", Mage::helper('core')->currency( $product->getSpecialPrice () ) );
                            } else {
                                    $domCategoryProduct->setAttribute ( "reduction", 0 );
                                    $domCategoryProduct->setAttribute ( "price", Mage::helper('core')->currency( $product->getPrice() ) );
                            }

                            /* Nom du Produit */
                            $domCategoryProductTitle = $dom->createElement ( "title" );
                            $domCategoryProductTitleValue = $dom->createTextNode ( $product->getName () );
                            $domCategoryProductTitle->appendChild ( $domCategoryProductTitleValue );
                            $domCategoryProduct->appendChild ( $domCategoryProductTitle );

                            /* Description du Produit */
                            $domCategoryProductDesc = $dom->createElement ( "description" );
                            $domCategoryProductDescValue = $dom->createTextNode ( $product->getDescription () );
                            $domCategoryProductDesc->appendChild ( $domCategoryProductDescValue );
                            $domCategoryProduct->appendChild ( $domCategoryProductDesc );

                            /* Couverture du produit */
                            if ($imageCover = $product->getImage ()) {
                                    $domCategoryProductImg = $dom->createElement ( "cover" );
                                    $domCategoryProductImgValue = $dom->createTextNode ( Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product->getImage() );
                                    $domCategoryProductImg->appendChild ( $domCategoryProductImgValue );
                                    $domCategoryProduct->appendChild ( $domCategoryProductImg );
                            } else {
                                    $domCategoryProductImg = $dom->createElement ( "cover" );
                                    $domCategoryProductImgValue = $dom->createTextNode ( Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product->getImage().'placeholder/small_image.jpg' );
                                    $domCategoryProductImg->appendChild ( $domCategoryProductImgValue );
                                    $domCategoryProduct->appendChild ( $domCategoryProductImg );
                            }
                            $domCategory->appendChild ( $domCategoryProduct );
                    }


			$document->appendChild ( $domCategory );
			$dom->appendChild ( $document );

			if($dom->save ( dirname ( dirname ( __FILE__ ) ) . '/xml/category' . intval ( $id_category ) . '-' . intval ( $page ) . '.xml' ))
				$success=true;
		}
		return $success;
	}

	
}