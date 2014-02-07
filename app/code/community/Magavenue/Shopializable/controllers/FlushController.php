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
class Magavenue_Shopializable_FlushController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		/* deleting all xml files */
		foreach(glob(dirname ( dirname ( __FILE__ ) ) . '/xml/*.*') as $file){
			unlink($file);
		}
		/* generating configuration xml file */
		$this->getConfiguration();
		$this->generateCategoriesTree();
		$this->generateCMS();
		$this->generateBestSales();
		$this->generateNewProducts();
		$this->generatePricesDrop();
		$this->generateDefaultCategory();
                
		/* redirecting user */
		header('Location:'.$_SERVER["HTTP_REFERER"]);
		echo 'Location:'.$_SERVER["HTTP_REFERER"];
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

        public function generateBestSales(){
                $products = Mage::getResourceModel('reports/product_collection')
                    ->addAttributeToSelect('entity_id')
                    ->addOrderedQty()
                    ->addAttributeToSort('ordered_qty','desc')
                    ->addAttributeToFilter('type_id', 'simple')
                    ->setPageSize(Mage::getStoreConfig('shopializable/shopializable/boutique_nbr')!=''?Mage::getStoreConfig('shopializable/shopializable/boutique_nbr'):'9')
                    ->load();
                $collections = $products->toArray();

		return $this->generateSpecialsFile($collections, 'bestSales');
	}

        public function generateDefaultCategory(){
                $id_category = Mage::getStoreConfig('shopializable/shopializable/category_home')!=''?Mage::getStoreConfig('shopializable/shopializable/category_home'):'9';
                $storeId    = Mage::app()->getStore()->getId();
                $category = Mage::getModel('catalog/category')->load( intval($id_category) );
                $products = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect('*')
                    ->addCategoryFilter($category)
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('visibility',array('in' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH))
                    ->setStoreId( $storeId )
                    ->setPageSize(Mage::getStoreConfig('shopializable/shopializable/boutique_nbr')!=''?Mage::getStoreConfig('shopializable/shopializable/boutique_nbr'):'9')
                    ->load();

		$collections = $products->toArray();
		return $this->generateSpecialsFile($collections, 'category1');
	}


        public function generatePricesDrop(){
            $now = Mage::getModel('core/date')->timestamp(time());
            $date = date('Y-m-d h:i:s', $now);

            $products = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect('entity_id')
                        ->addAttributeToFilter('special_price',array('neq' => 0))
                        ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $date))
                        ->setPageSize(Mage::getStoreConfig('shopializable/shopializable/boutique_nbr')!=''?Mage::getStoreConfig('shopializable/shopializable/boutique_nbr'):'9')
                        ->addAttributeToFilter(array(
                                    array('attribute' => 'special_to_date', 'date' => true, 'from' => $date),
                                    array('attribute' => 'special_to_date', 'is' => new Zend_Db_Expr('null'))
                        ))
                        ->load();
            $collections = $products->toArray();

            return $this->generateSpecialsFile($collections, 'pricesdrop');
	}


        public function generateNewProducts(){
               $now = Mage::getModel('core/date')->timestamp(time());
                $date = date('Y-m-d h:i:s', $now);

                $products = Mage::getResourceModel('catalog/product_collection')
                            ->addAttributeToSelect('entity_id')
                            ->addAttributeToFilter('type_id', 'simple')
                            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $date))
                            ->setPageSize(Mage::getStoreConfig('shopializable/shopializable/boutique_nbr')!=''?Mage::getStoreConfig('shopializable/shopializable/boutique_nbr'):'9')
                            ->addAttributeToFilter(array(
                                        array('attribute' => 'news_to_date', 'date' => true, 'from' => $date),
                                        array('attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('null'))
                            ))
                            ->load();
               $collections = $products->toArray();
	       return $this->generateSpecialsFile($collections, 'newproducts');
	}

        public function generateSpecialsFile($collections, $name){
            /* Nombre de Produit par page */
            $nb_product = Mage::getStoreConfig('shopializable/shopializable/boutique_nbr')!=''?Mage::getStoreConfig('shopializable/shopializable/boutique_nbr'):'9';

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

                    foreach ( $collections as $id_product => $collection ) {
                            /* Recuperation du Produit */
                            $product = Mage::getModel ( 'catalog/product' )->load ( $id_product );

                            /* Generate product xml */
                            $domCategoryProduct = $dom->createElement ( "product" );
                            $domCategoryProduct->setAttribute ( "id", $id_product );
                            $domCategoryProduct->setAttribute ( "quantity", intval($product->getStockItem('qty')));

                            if ($product->isGrouped()){
                                    $domCategoryProduct->setAttribute ( "reduction", 0 );
                                    $domCategoryProduct->setAttribute ( "price", Mage::helper('core')->currency( $this->getMinimumPriceOptionValue($product) ) );
                            }else if ($product->getSpecialPrice ()) {
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

                    if($dom->save ( dirname ( dirname ( __FILE__ ) ) . '/xml/' . $name . '-' . intval ( $page ) . '.xml' ))
                            $success=true;
            }
            return $success;
        }


        /* Generate CMS links */
	public function generateCMS(){
		$list = Mage::getModel('cms/page')->getCollection();

		$dom = new DomDocument();
		$document = $dom->createElement("document");
		$domCMS = $dom->createElement("listCMS");

                if(isset($list)){
                    foreach ($list AS $cms){
                        if(!$cms->is_active || $cms->identifier == 'no-route' || $cms->identifier == 'home')
                            continue;
                        
                            $domCMSLink = $dom->createElement("cms");
                            $domCMSLinkName = $dom->createTextNode( $cms->title );
                            $domCMSLink->appendChild($domCMSLinkName);
                            $domCMSLink->setAttribute("id_cms", intval( $cms->page_id ));
                            $domCMSLink->setAttribute("url", $cms->identifier);
                            $domCMS->appendChild($domCMSLink);
                    }
                }

		$document->appendChild($domCMS);
		$dom->appendChild($document);

		/* Generate the xml file */
		if($dom->save ( dirname ( dirname ( __FILE__ ) ) . '/xml/CMS.xml' ))
			return true;
		return false;

	}

	public function generateCategoriesTree() {
		
		/* Recuperation des categories root */
		$categories = Mage::getModel ( 'catalog/category' )->getCollection ()->addAttributeToFilter ( 'level', 1 );
		
		/* Creation du fichier xml */
		$dom = new DOMDocument ( );
		$document = $dom->createElement ( "document" );
		$domArbre = $dom->createElement ( "arbre" );
		
		foreach ( $categories as $category ) {
			$this->getChild ( $category, $dom, $domArbre );
		}
		
		$document->appendChild ( $domArbre );
		
		$dom->appendChild ( $document );
		if($dom->save ( dirname ( dirname ( __FILE__ ) ) . '/xml/categories.xml' ))
			return true;
		return false;
	}

	public function getChild($category_parent, $dom, $domArbre, $childrens = NULL) {
		/* On recupere les sous-categories*/
                if($childrens)
                    $children_categories = $childrens;
                else
                    $children_categories = Mage::getModel ( 'catalog/category' )->getCategories ( $category_parent->getEntityId () );
		
		/* Getting sub categories */
		foreach ( $children_categories as $children_category ) {
                    if($childrens)
                        $category = Mage::getModel ( 'catalog/category' )->load ( (int)$children_category );
                    else
                        $category = Mage::getModel ( 'catalog/category' )->load ( $children_category->getEntityId () );
                        
                    if ($category->getIsActive ()) {
                            $domCategory = $dom->createElement ( "categorie" );

                            /* Description de la Categorie */
                            $domDesc = $dom->createElement ( "description" );
                            $domDescValue = $dom->createTextNode ( $category->getDescription () );
                            $domDesc->appendChild ( $domDescValue );
                            $domCategory->appendChild ( $domDesc );

                            /* Lien de la Categorie */
                            $domLink = $dom->createElement ( "lien" );
                            $domLinkValue = $dom->createTextNode ( 'categorie=' . intval ( $category->getEntityId () ) );
                            $domLink->appendChild ( $domLinkValue );
                            $domCategory->appendChild ( $domLink );

                            /* Nom de la Categorie */
                            $domName = $dom->createElement ( "nom" );
                            $domNameValue = $dom->createTextNode ( $category->getName () );
                            $domName->appendChild ( $domNameValue );
                            $domCategory->appendChild ( $domName );

                            /* Niveau de la Categorie */
                            $domDepth = $dom->createElement ( "depth" );
                            $domDepthValue = $dom->createTextNode ( $category->getLevel () - 2 );
                            $domDepth->appendChild ( $domDepthValue );
                            $domCategory->appendChild ( $domDepth );

                            $domArbre->appendChild ( $domCategory );

                            /* Si la categories a des sous-categories on appelle GetChild() */
                            if ($category->getChildren ())
                                    $this->getChild ( $category, $dom, $domArbre, explode(',', $category->getChildren ()) );
                    }
		}
	}
	
	private function getConfiguration(){
		$dom = new DomDocument();
		$document = $dom->createElement("document");
	
		$domConfiguration = $dom->createElement("configuration");			
		
  		/* Name */
		if(Mage::getStoreConfig('shopializable/shopializable/boutique_name')!=''){
			$domName = $dom->createElement("name");
			$domNameValue = $dom->createTextNode(Mage::getStoreConfig('shopializable/shopializable/boutique_name'));
	  		$domName->appendChild($domNameValue); 
	  		$domConfiguration->appendChild($domName);
		}
		/* Logo */
		if(Mage::getStoreConfig('shopializable/shopializable/boutique_logo')!=''){
			$domLogo = $dom->createElement("logo");
			$domLogoValue = $dom->createTextNode(Mage::getStoreConfig('shopializable/shopializable/boutique_logo'));
	  		$domLogo->appendChild($domLogoValue); 
	  		$domConfiguration->appendChild($domLogo);
		}
		
  		/* image */
		if(Mage::getStoreConfig('shopializable/shopializable/boutique_image')!=''){
			$domPhoto = $dom->createElement("homeImage");
			$domPhotoValue = $dom->createTextNode(Mage::getStoreConfig('shopializable/shopializable/boutique_image'));
	  		$domPhoto->appendChild($domPhotoValue); 
	  		$domConfiguration->appendChild($domPhoto);
		}
			
	  	/* description */
		if(Mage::getStoreConfig('shopializable/shopializable/boutique_description')!=''){
			$domPhoto = $dom->createElement("description");
			$domPhotoValue = $dom->createTextNode(Mage::getStoreConfig('shopializable/shopializable/boutique_description'));
	  		$domPhoto->appendChild($domPhotoValue); 
	  		$domConfiguration->appendChild($domPhoto);
		}
		
	  	/* Template */
		$domTemplate = $dom->createElement("template");
		$domTemplateValue = $dom->createTextNode((Mage::getStoreConfig('shopializable/shopializable/boutique_template')!=''?Mage::getStoreConfig('shopializable/shopializable/boutique_template'):'default'));
  		$domTemplate->appendChild($domTemplateValue); 
  		$domConfiguration->appendChild($domTemplate);
	
	  	/* Number of product */
		$domNumber = $dom->createElement("number_product");
		$domNumberValue = $dom->createTextNode((Mage::getStoreConfig('shopializable/shopializable/boutique_nbr')!=''?Mage::getStoreConfig('shopializable/shopializable/boutique_nbr'):'9'));
  		$domNumber->appendChild($domNumberValue); 
  		$domConfiguration->appendChild($domNumber);

  		/* You have a shop running on Magento ! */
		$domMagento = $dom->createElement("running_system");
		$domMagentoValue = $dom->createTextNode('Magento');
  		$domMagento->appendChild($domMagentoValue); 
  		$domConfiguration->appendChild($domMagento);
  		
		$document->appendChild($domConfiguration);
		
		$dom->appendChild($document);
		$dom->save ( dirname ( dirname ( __FILE__ ) ) . '/xml/configuration.xml');
	}
}
