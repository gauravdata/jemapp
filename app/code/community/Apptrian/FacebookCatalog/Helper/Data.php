<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
class Apptrian_FacebookCatalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Directory full path.
     *
     * @var null|string
     */
    public $directoryPath = null;
    
    /**
     * Store object
     *
     * @var null|Mage_Core_Model_Store
     */
    public $store = null;
    
    /**
     * Store ID
     *
     * @var null|int
     */
    public $storeId = null;
    
    /**
     * Store Locale
     *
     * @var null|string
     */
    public $storeLocale = null;
    
    /**
     * Store URL
     *
     * @var null|string
     */
    public $storeUrl = null;
    
    /**
     * Store media URL
     *
     * @var null|string
     */
    public $storeMediaUrl = null;
    
    /**
     * Store name
     *
     * @var null|string
     */
    public $storeName = null;
    
    /**
     * Feed format
     *
     * @var null|string
     */
    public $feedFormat = null;
    
    /**
     * Allow products that are not visible individually in product feed
     *
     * @var null|int
     */
    public $pnviAllowed = null;
    
    /**
     * Feed filename
     *
     * @var null|string
     */
    public $filename = null;
    
    /**
     * Base currency code
     *
     * @var null|string
     */
    public $baseCurrencyCode = null;
    
    /**
     * Current currency code
     *
     * @var null|string
     */
    public $defaultCurrencyCode = null;
    
    /**
     * Domain name
     *
     * @var null|string
     */
    public $domainName = null;
    
    /**
     * Store URL length
     *
     * @var null|int
     */
    public $storeUrlLength = null;
    
    /**
     * Product URL suffix
     *
     * @var null|string
     */
    public $productUrlSuffix = null;
    
    /**
     * Tax display flag
     *
     * @var null|int
     */
    public $taxDisplayFlag = null;
    
    /**
     * Tax catalog flag
     *
     * @var null|int
     */
    public $taxCatalogFlag = null;
    
    /**
     * Attribute code for id field
     *
     * @var null|string
     */
    public $idAttr = null;
    
    /**
     * Attribute code for availability field
     *
     * @var null|string
     */
    public $availabilityAttr = null;
    
    /**
     * Mapping of values for availability field
     *
     * @var null|string
     */
    public $availabilityMap = null;
    
    /**
     * Attribute code for condition field
     *
     * @var null|string
     */
    public $conditionAttr = null;
    
    /**
     * Mapping of values for condition field
     *
     * @var null|string
     */
    public $conditionMap = null;
    
    /**
     * Attribute code for description field
     *
     * @var null|string
     */
    public $descriptionAttr = null;
    
    /**
     * Attribute code for title field
     *
     * @var null|string
     */
    public $titleAttr = null;
    
    /**
     * Value of gtin field used in getEntryIdentifierExists() method
     *
     * @var string
     */
    public $gtin = '';
    
    /**
     * Attribute code for gtin field
     *
     * @var null|string
     */
    public $gtinAttr = null;
    
    /**
     * Value of mpn field used in getEntryIdentifierExists() method
     *
     * @var string
     */
    public $mpn = '';
    
    /**
     * Attribute code for mpn field
     *
     * @var null|string
     */
    public $mpnAttr = null;
    
    /**
     * Value of brand field used in getEntryIdentifierExists() method
     *
     * @var string
     */
    public $brand = '';
    
    /**
     * Attribute code for brand field
     *
     * @var null|string
     */
    public $brandAttr = null;
    
    /**
     * Default brand
     *
     * @var null|string
     */
    public $defaultBrand = null;
    
    /**
     * identifier_exists field flag
     *
     * @var null|int
     */
    public $isIdentifierExistsEnabled = null;
    
    /**
     * override field flag
     *
     * @var null|int
     */
    public $isOverrideEnabled = null;
    
    /**
     * Attribute code for additional_image_link field
     *
     * @var null|string
     */
    public $additionalImageLinkLimit = null;
    
    /**
     * Attribute code for age_group field
     *
     * @var null|string
     */
    public $ageGroupAttr = null;
    
    /**
     * Mapping of values for age_group field
     *
     * @var null|string
     */
    public $ageGroupMap = null;
    
    /**
     * Attribute code for expiration_date field
     *
     * @var null|string
     */
    public $expirationDateAttr = null;
    
    /**
     * Attribute code for gender field
     *
     * @var null|string
     */
    public $genderAttr = null;
    
    /**
     * Mapping of values for gender field
     *
     * @var null|string
     */
    public $genderMap = null;
    
    /**
     * Attribute code for item_group_id field
     *
     * @var null|string
     */
    public $itemGroupIdAttr = null;
    
    /**
     * Attribute code for google_product_category field
     *
     * @var null|string
     */
    public $gpcAttr = null;
    
    /**
     * Default google_product_category
     *
     * @var null|string
     */
    public $defaultGpc = null;
    
    /**
     * sale_price field flag
     *
     * @var null|int
     */
    public $isSalePriceEnabled = null;
    
    /**
     * sale_price_effective_date field flag
     *
     * @var null|int
     */
    public $isSalePriceEffectiveDateEnabled = null;
    
    /**
     * Map for the optional fields. Keys are fields values are attribute codes
     *
     * @var array
     */
    public $map = array();
    
    /**
     * Used in getEntryAdditionalImageLink() and set in getEntryImageLink()
     *
     * @var string
     */
    public $productImageUrl = '';
    
    /**
     * Used in getEntrySalesPriceEffectiveDate()
     *
     * @var string
     */
    public $productSalePrice = '';
    
    /**
     * Parent product object
     *
     * @var null|Mage_Catalog_Model_Product
     */
    public $parentProduct = null;
    
    /**
     * Parent product ID
     *
     * @var null|int
     */
    public $parentProductId = null;
    
    /**
     * Tax helper
     *
     * @var null|Mage_Tax_Helper_Data
     */
    public $taxHelper = null;

    /**
     * Facebook defined values for availability field
     *
     * @var array
     */
    public $availabilityValues = array(
        'in stock',
        'out of stock',
        'preorder',
        'available for order',
        'discontinued'
    );
    
    /**
     * Facebook defined values for condition field
     *
     * @var array
     */
    public $conditionValues = array(
        'new',
        'refurbished',
        'used'
    );
    
    /**
     * Facebook defined values for age_group field
     *
     * @var array
     */
    public $ageGroupValues = array(
        'newborn',
        'infant',
        'toddler',
        'kids',
        'adult'
    );
    
    /**
     * Facebook defined values for gender field
     *
     * @var array
     */
    public $genderValues = array(
        'male',
        'female',
        'unisex'
    );

    /**
     * Returns extension version.
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        return (string) Mage::getConfig()->getNode()
            ->modules->Apptrian_FacebookCatalog->version;
    }
    
    /**
     * Based on provided configuration path returns configuration value.
     *
     * @param string $path
     * @param string|int $scope
     * @return string
     */
    public function getConfig($path, $scope = null)
    {
        return Mage::getStoreConfig($path, $scope);
    }
    
    /**
     * Returns feed format from config.
     *
     * @param int $storeId
     * @return string
     */
    public function getFeedFormat($storeId)
    {
        return $this->getConfig(
            'apptrian_facebookcatalog/general/format',
            $storeId
        );
    }
    
    /**
     * Returns full directory path to where generated feeds will reside.
     *
     * @return string
     */
    public function buildDirectoryPath()
    {
        return Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS;
    }
    
    /**
     * Based on config creates filename for the feed.
     *
     * @param int $storeId
     * @param string $feedFormat
     * @return string
     */
    public function buildFilename($storeId, $feedFormat)
    {
        $customFilename = $this->getConfig(
            'apptrian_facebookcatalog/general/filename',
            $storeId
        );
        
        if ($feedFormat == 'xml-rss') {
            $ext = 'xml';
        } else {
            // It is csv or tsv string
            $ext = $feedFormat;
        }
        
        if ($customFilename) {
            return $customFilename . '.'  . $ext;
        } else {
            return 'store-' . $storeId . '.'  . $ext;
        }
    }
    
    /**
     * Returns store base url.
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function buildStoreUrl($store)
    {
        return $this->cleanUrl(
            $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
        );
    }
    
    /**
     * Returns store media url.
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function buildStoreMediaUrl($store)
    {
        return $this->cleanUrl(
            $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
        );
    }
    
    /**
     * Returns header part of the file.
     *
     * @return string
     */
    public function buildHeader()
    {
        $s = '';
        
        if ($this->feedFormat == 'xml-rss') {
            $s  = '<?xml version="1.0"?>';
            $s .= "\n";
            $s .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">';
            $s .= "\n<channel>\n";
            $s .= "<title>" . $this->storeName . "</title>\n";
            $s .= "<link>" . $this->storeUrl . "</link>\n";
            $s .= "<description>Facebook Catalog Product Feed of ";
            $s .= $this->storeName . " store.</description>\n";
        } else {
            $h = array(
                'id',
                'availability',
                'condition',
                'title',
                'description',
                'link',
                'image_link',
                'price'
            );
            
            if ($this->isSalePriceEnabled) {
                $h[] = 'sale_price';
                
                if ($this->isSalePriceEffectiveDateEnabled) {
                    $h[] = 'sale_price_effective_date';
                }
            }
            
            if ($this->gtinAttr) {
                $h[] = 'gtin';
            }
            
            if ($this->mpnAttr) {
                $h[] = 'mpn';
            }
            
            if ($this->brandAttr) {
                $h[] = 'brand';
            }
            
            if ($this->isIdentifierExistsEnabled) {
                $h[] = 'identifier_exists';
            }
            
            if ($this->isOverrideEnabled) {
                $h[] = 'override';
            }
            
            if ($this->additionalImageLinkLimit) {
                $h[] = 'additional_image_link';
            }
            
            if ($this->ageGroupAttr) {
                $h[] = 'age_group';
            }
            
            if ($this->expirationDateAttr) {
                $h[] = 'expiration_date';
            }
            
            if ($this->genderAttr) {
                $h[] = 'gender';
            }
            
            if ($this->itemGroupIdAttr) {
                $h[] = 'item_group_id';
            }
            
            if ($this->gpcAttr) {
                $h[] = 'google_product_category';
            }
            
            // Additional fields
            if (!empty($this->map)) {
                foreach ($this->map as $field => $attribute) {
                    $h[] = $field;
                }
            }
            
            if ($this->feedFormat == 'tsv') {
                // TSV format
                $s = implode("\t", $h);
            } else {
                // CSV format
                $s = implode(',', $h);
            }
        }
        
        return $s;
    }
    
    /**
     * Returns footer part of the file.
     *
     * @return string
     */
    public function buildFooter()
    {
        $s = '';
        
        if ($this->feedFormat == 'xml-rss') {
            $s .= "</channel>\n</rss>";
        }
        
        return $s;
    }
    
    /**
     * Returns product entry.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function buildProductEntry($product)
    {
        $entry = array();
        
        // Reset values
        $this->gtin = '';
        $this->mpn = '';
        $this->brand = '';
        
        // Find and set parent product id and object
        $this->parentProduct   = null;
        $this->parentProductId = null;
        
        $this->parentProductId = $this->getParentProductId($product->getId());
        
        if ($this->parentProductId) {
            $this->parentProduct = Mage::getModel('catalog/product')
                ->setStoreId($this->storeId)
                ->load($this->parentProductId);
        }
        
        // Order of keys is significant because of buildHeader() TSV format
        $entry['id']           = $this->getEntryId($product);
        $entry['availability'] = $this->getEntryAvailability($product);
        $entry['condition']    = $this->getEntryCondition($product);
        $entry['title']        = $this->getEntryTitle($product);
        $entry['description']  = $this->getEntryDescription($product);
        $entry['link']         = $this->getEntryLink($product);
        $entry['image_link']   = $this->getEntryImageLink($product);
        
        $price = $this->getEntryPrice($product);
        
        if ($price) {
            $entry['price'] = $price;
        } else {
            $entry['price'] = '0.00 ' . $this->defaultCurrencyCode;
        }
        
        if ($this->isSalePriceEnabled) {
            $entry['sale_price']    = $this->getEntryPrice($product, true);
            $this->productSalePrice = $entry['sale_price'];
            
            if ($this->isSalePriceEffectiveDateEnabled) {
                $entry['sale_price_effective_date'] = '';
                $salePriceEffective = $this
                    ->getEntrySalePriceEffectiveDate($product);
                if ($this->productSalePrice) {
                    $entry['sale_price_effective_date'] = $salePriceEffective;
                }
            }
            
            $this->productSalePrice = '';
        }
        
        if ($this->gtinAttr) {
            $this->gtin = $this->getEntryGtin($product);
            $entry['gtin'] = $this->gtin;
        }
        
        if ($this->mpnAttr) {
            $this->mpn = $this->getEntryMpn($product);
            $entry['mpn'] = $this->mpn;
        }
        
        if ($this->brandAttr) {
            $this->brand = $this->getEntryBrand($product);
            $entry['brand'] = $this->brand;
        }
        
        if ($this->isIdentifierExistsEnabled) {
            $entry['identifier_exists'] = $this->getEntryIdentifierExists();
        }
        
        if ($this->isOverrideEnabled) {
            $entry['override'] = $this->storeLocale;
        }
        
        if ($this->additionalImageLinkLimit) {
            $entry['additional_image_link'] = $this
                ->getEntryAdditionalImageLink($product);
        }
        
        if ($this->ageGroupAttr) {
            $entry['age_group'] = $this->getEntryAgeGroup($product);
        }
        
        if ($this->expirationDateAttr) {
            $entry['expiration_date'] = $this->getEntryExpirationDate($product);
        }
        
        if ($this->genderAttr) {
            $entry['gender'] = $this->getEntryGender($product);
        }
        
        if ($this->itemGroupIdAttr) {
            $entry['item_group_id'] = $this->getEntryItemGroupId();
        }
        
        if ($this->gpcAttr) {
            $entry['google_product_category'] = $this
                ->getEntryGoogleProductCategory($product);
        }
        
        // Additional fields
        if (!empty($this->map)) {
            foreach ($this->map as $field => $attribute) {
                $entry[$field] = $this->getAttributeValue($product, $attribute);
            }
        }
        
        return $this->convertEntryArrayToString($entry);
    }
    
    /**
     * Based on entry array and config returns string.
     *
     * @param array $entry
     * @return string
     */
    public function convertEntryArrayToString($entry)
    {
        $s = '';
        
        if ($this->feedFormat == 'xml-rss') {
            $s .= "<item>\n";
            
            foreach ($entry as $key => $value) {
                $s .= "<g:" . $key . ">" . $this->prepareForXml($value, $key)
                . "</g:" . $key . ">\n";
            }
            
            $s .= "</item>";
        } else {
            if ($this->feedFormat == 'tsv') {
                $delimiter = "\t";
            } else {
                $delimiter = ',';
            }
            
            foreach ($entry as $value) {
                $s .= '"' . str_replace('"', "'", $value) . '"' . $delimiter;
            }
            
            $s = trim($s, $delimiter);
        }
        
        return $s;
    }
    
    /**
     * Returns entry ID value from product object.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getEntryId($product)
    {
        $value = '';
        
        switch ($this->idAttr) {
            case 'sku':
                $value = $product->getSku();
                break;
            case 'id':
                $value = $product->getId();
                break;
            default:
                $value = $this->getAttributeValue($product, $this->idAttr);
        }
        
        // Facebook limit for id is 100 characters
        return $this->limitText($this->cleanText($value), 100);
    }
    
    /**
     * Returns entry availability value from product object.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryAvailability($product)
    {
        $availavility = '';
        
        if ($this->availabilityAttr) {
            $value = $this->getAttributeValue(
                $product,
                $this->availabilityAttr
            );
            
            $availavility = (string) array_search(
                $value,
                $this->availabilityMap
            );
        } else {
            $stock = Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($product);
            
            if ($stock->getIsInStock()) {
                $availavility = 'in stock';
            } else {
                $availavility = 'out of stock';
            }
        }
        
        return $availavility;
    }
    
    /**
     * Returns entry condition value from product object.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryCondition($product)
    {
        $condition = '';
        
        if ($this->conditionAttr) {
            $value = $this->getAttributeValue(
                $product,
                $this->conditionAttr
            );
            
            $condition = (string) array_search(
                $value,
                $this->conditionMap
            );
        }
        
        if (!$condition) {
            // Default condition is 'new'
            $condition = 'new';
        }
        
        return $condition;
    }
    
    /**
     * Returns entry title value from product object.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getEntryTitle($product)
    {
        $title = '';
        
        switch ($this->titleAttr) {
            case 'name':
                $title = $product->getName();
                break;
            default:
                $title = $this->getAttributeValue($product, $this->titleAttr);
        }
        
        if (!$title) {
            // If user selected attribute is empty alternative is product name
            $title = $product->getName();
        }
        
        // Facebook limit for title is 100 characters
        return $this->limitText($this->cleanText($title), 100);
    }
    
    /**
     * Returns entry description value from product object.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getEntryDescription($product)
    {
        $description = '';
        
        $description = $this->getDescriptionValue(
            $product,
            $this->descriptionAttr
        );
        
        // If user selected attribute is empty find alternative
        if (!$description) {
            // Meta Description > Short Description > Description > Product Name
            $priority = array(
                'meta_description',
                'short_description',
                'description',
                'name'
            );
            
            foreach ($priority as $attr) {
                $description = $this->getDescriptionValue($product, $attr);
                
                if ($description != '') {
                    break;
                }
            }
        }
        
        // Facebook limit for description is 5000 characters
        return $this->limitText($this->cleanText($description), 5000);
    }
    
    /**
     * Returns description value based on attribute.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $attribute
     * @return string
     */
    public function getDescriptionValue($product, $attribute)
    {
        $value = '';
        
        switch ($attribute) {
            case 'meta_description':
                $value = $product->getMetaDescription();
                break;
            case 'short_description':
                $value = $product->getShortDescription();
                break;
            case 'description':
                $value = $product->getDescription();
                break;
            case 'name':
                $value = $product->getName();
                break;
            default:
                $value = $this->getAttributeValue($product, $attribute);
        }
        
        return $value;
    }
    
    /**
     * Returns entry link value from product object.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getEntryLink($product)
    {
        $notVis = Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;
        
        $parentFlag = 0;
        
        if ($product->getVisibility() == $notVis) {
            if ($this->parentProductId && $this->parentProduct) {
                $parentFlag = 1;
                $url = $this->cleanUrl($this->parentProduct->getProductUrl());
            } else {
                $m = sprintf(
                    'Product ID: %d, SKU: %s, Name: %s ',
                    $product->getId(),
                    $product->getSku(),
                    $product->getName()
                );
                $m .= 'is not visible individually but has no parent product.';
                Mage::log($m);
                
                $url = $this->cleanUrl($product->getProductUrl());
            }
        } else {
            $url = $this->cleanUrl($product->getProductUrl());
        }
        
        if (!$this->isValidUrl($url)) {
            $url = $this->storeUrl . $url;
        }
        
        // Case when getProductUrl() does not return url for the specific store
        // This is a Magento bug not extension's.
        // Fix is to check if url starts with store url if it does then just
        // return the url.
        // If it does not start with store url then manually construct the
        // product url.
        if (substr($url, 0, $this->storeUrlLength) === $this->storeUrl) {
            return $url;
        } else {
            if ($parentFlag) {
                $productUrlKey = $this->parentProduct->getUrlKey();
            } else {
                $productUrlKey = $product->getUrlKey();
            }
            
            return $this->storeUrl . $productUrlKey . $this->productUrlSuffix;
        }
    }
    
    /**
     * Based on product ID returns parent product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentProductId($productId)
    {
        $parentId = null;
        
        // Configurable
        $parentIds = Mage::getModel('catalog/product_type_configurable')
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) > 0 && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        // Bundle
        $parentIds = Mage::getModel('bundle/product_type')
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) > 0 && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        // Grouped
        $parentIds = Mage::getModel('catalog/product_type_grouped')
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) > 0 && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Returns entry image link value from product object.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getEntryImageLink($product)
    {
        $imageUrl = null;
        $image = $product->getImage();
        // Used in getEntryAdditionalImageLink() method to exclude base image
        $this->productImageUrl = '';
        
        if (!$image || $image === '' || $image === 'no_selection') {
            $product->load('media_gallery');
            $gallery = $product->getMediaGalleryImages();
            if ($gallery) {
                foreach ($gallery as $galleryImage) {
                    if ($galleryImage['url'] && $galleryImage['url'] !== '') {
                        $imageUrl = $galleryImage['url'];
                        break;
                    }
                }
            }
        }
        
        if (!$imageUrl && $image != '' && $image !== 'no_selection') {
            $imageUrl = $this->storeMediaUrl . 'catalog/product' . $image;
        }
        
        if ($this->isValidUrl($imageUrl)) {
            $this->productImageUrl = $imageUrl;
            return $imageUrl;
        } else {
            // Placeholder image
            $this->productImageUrl = $this->storeUrl
                . 'skin/frontend/base/default/images/'
                . 'catalog/product/placeholder/image.jpg';
            return $this->productImageUrl;
        }
    }
    
    /**
     * Returns comma-separated URLs of additional images.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryAdditionalImageLink($product)
    {
        $additional = array();
        $images = array();
        
        $product->load('media_gallery');
        $gallery = $product->getMediaGalleryImages();
        if ($gallery) {
            foreach ($gallery as $galleryImage) {
                if ($galleryImage['url'] && $galleryImage['url'] !== '') {
                    $images[] = $galleryImage['url'];
                }
            }
        }
        
        if (!empty($images)) {
            $i = 0;
            $strLength = 0;
            foreach ($images as $image) {
                if ($this->additionalImageLinkLimit == $i) {
                    break;
                }
                
                if ($this->productImageUrl != $image) {
                    $urlLength = strlen($image);
                    // Limit for this field is 2000
                    if (($strLength + $urlLength + $i) <= 2000) {
                        $additional[] = $image;
                        $strLength += $urlLength;
                        $i++;
                    }
                }
            }
        }
        
        // Feed value cannot be empty
        if (empty($additional)) {
            return $this->productImageUrl;
        } else {
            return implode(',', $additional);
        }
    }
    
    /**
     * Returns entry price value from product object.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $salePrice
     * @return string
     */
    public function getEntryPrice($product, $salePrice = false)
    {
        return $this->formatPrice(
            $this->getProductPrice(
                $product,
                $salePrice
            ),
            $this->defaultCurrencyCode
        );
    }
    
    /**
     * Returns product price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $salePrice
     * @return string
     */
    public function getProductPrice($product, $salePrice = false)
    {
        $price = 0.0;
        
        $productType = $product->getTypeId();
        
        switch ($productType) {
            case 'bundle':
                $price =  $this->getBundleProductPrice($product, $salePrice);
                break;
            case 'configurable':
                $price = $this->getConfigurableProductPrice(
                    $product,
                    $salePrice
                );
                break;
            case 'grouped':
                $price = $this->getGroupedProductPrice($product, $salePrice);
                break;
            default:
                $price = $this->getFinalPrice($product, $salePrice);
        }
        
        return $price;
    }
    
    /**
     * Returns bundle product price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $salePrice
     * @return string
     */
    public function getBundleProductPrice($product, $salePrice = false)
    {
        
        $includeTax = (bool) $this->taxDisplayFlag;
        
        if ($this->isSalePriceEnabled) {
            if ($salePrice) {
                $price = $product->getPriceModel()
                    ->getTotalPrices($product, 'min', $includeTax, 1);
            } else {
                $price = $product->getPriceModel()
                    ->getTotalPrices($product, 'max', $includeTax, 1);
            }
        } else {
            $price = $product->getPriceModel()
                ->getTotalPrices($product, 'min', $includeTax, 1);
        }
        
        return $this->getFinalPrice($product, $salePrice, $price);
    }
    
    /**
     * Returns configurable product price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $salePrice
     * @return string
     */
    public function getConfigurableProductPrice($product, $salePrice)
    {
        if ($product->getFinalPrice() === 0) {
            $configurable = Mage::getModel('catalog/product_type_configurable')
                ->setProduct($product);
            $subCollection = $configurable->getUsedProductCollection()
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('regular_price')
                ->addAttributeToSelect('special_price')
                ->addAttributeToSelect('tax_class_id')
                ->addAttributeToSelect('tax_percent')
                ->addFilterByRequiredOptions();
            
            foreach ($subCollection as $subProduct) {
                if ($subProduct->getPrice() > 0) {
                    return $this->getFinalPrice($subProduct, $salePrice);
                }
            }
        }
        
        return $this->getFinalPrice($product, $salePrice);
    }
    
    /**
     * Returns grouped product price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $salePrice
     * @return string
     */
    public function getGroupedProductPrice($product, $salePrice = false)
    {
        $price = 0;
        
        $assocProducts = $product->getTypeInstance(true)
            ->getAssociatedProductCollection($product)
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('regular_price')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('tax_class_id')
            ->addAttributeToSelect('tax_percent');
        
        $prices = array();
        
        foreach ($assocProducts as $assocProduct) {
            $assocPrice = $this->getFinalPrice($assocProduct, $salePrice);
            
            if ($assocPrice) {
                $prices[] = $assocPrice;
            }
        }
        
        if (!empty($prices)) {
            $price = min($prices);
        }
        
        return $price;
    }
    
    /**
     * Returns product price with Catalog Price Rules applied
     * (if there are any).
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float|string
     */
    public function getFinalPriceWithCatalogPriceRules($product)
    {
        $price = 0;
        
        $discountedPrice = Mage::getResourceModel('catalogrule/rule')
            ->getRulePrice(
                Mage::app()->getLocale()->storeTimeStamp($this->storeId),
                Mage::app()->getStore($this->storeId)->getWebsiteId(),
                0, // This is Customer Group Id, 0 means NOT LOGGED IN
                $product->getId()
            );
        
        if ($discountedPrice === false) {
            $price = $product->getFinalPrice();
        } else {
            $price = $discountedPrice;
        }
        
        return $price;
    }
    
    /**
     * Returns final price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $salePrice
     * @param string $price
     * @return string
     */
    public function getFinalPrice($product, $salePrice = false, $price = null)
    {
        if ($price === null) {
            if ($this->isSalePriceEnabled) {
                if ($salePrice) {
                    $price = $this->getFinalPriceWithCatalogPriceRules(
                        $product
                    );
                    if ($price == 0) {
                        $price = $product->getSpecialPrice();
                    }
                    
                    $regularPrice = $product->getPrice();
                    
                    if ($price == $regularPrice) {
                        $price = '';
                    }
                } else {
                    $price = $product->getPrice();
                }
            } else {
                $price = $this->getFinalPriceWithCatalogPriceRules($product);
            }
        }
        
        // 1. Convert to current currency if needed
        $price = $this->convertCurrency($price);
        
        // 2. Apply tax if needed
        $price = $this->applyTax($price, $product);
        
        return $price;
    }
    
    /**
     * Converts price into default currency if needed.
     *
     * @param string|float $price
     * @return string|float
     */
    public function convertCurrency($price)
    {
        // If there is no price
        if ($price === '') {
            return '';
        }
        
        // Convert price if base and default currency are not the same
        if (($this->baseCurrencyCode !== $this->defaultCurrencyCode)) {
            // Convert to from base currency to default currency
            $price = $this->store->getBaseCurrency()
                ->convert($price, $this->defaultCurrencyCode);
        }
                
        return $price;
    }
    
    /**
     * Returns price with applied tax if needed.
     *
     * @param string|float $price
     * @param Mage_Catalog_Model_Product $product
     * @return string|float
     */
    public function applyTax($price, $product)
    {
        // If there is no price
        if ($price === '') {
            return '';
        }
        
        $productType = $product->getTypeId();
        
        if ($productType != 'bundle') {
            // If display tax flag is on and catalog tax flag is off
            if ($this->taxDisplayFlag && !$this->taxCatalogFlag) {
                $price = $this->taxHelper->getPrice(
                    $product,
                    $price,
                    true,
                    null,
                    null,
                    null,
                    $this->storeId,
                    false,
                    false
                );
            }
        
            // Case when catalog prices are with tax but display tax is set to
            // to exclude tax. Applies for all products
            // If display tax flag is off and catalog tax flag is on
            if (!$this->taxDisplayFlag && $this->taxCatalogFlag) {
                $price = $this->taxHelper->getPrice(
                    $product,
                    $price,
                    false,
                    null,
                    null,
                    null,
                    $this->storeId,
                    true,
                    false
                );
            }
        }
        
        return $price;
    }
    
    /**
     * Returns formated price.
     *
     * @param string $price
     * @param string $currencyCode
     * @return string
     */
    public function formatPrice($price, $currencyCode = '')
    {
        if ($price == '' || $price == 0) {
            return '';
        }
        
        $formatedPrice = number_format($price, 2, '.', '');
        
        if ($currencyCode) {
            return $formatedPrice . ' ' . $currencyCode;
        } else {
            return $formatedPrice;
        }
    }
    
    /**
     * Returns domain name from the string.
     *
     * @param string $url
     * @return string
     */
    public function getDomainNameFromUrl($url)
    {
        // Remove http and https part from $url
        if (substr($url, 0, strlen('http://')) == 'http://') {
            $url = substr($url, strlen('http://'));
        }
        
        if (substr($url, 0, strlen('https://')) == 'https://') {
            $url = substr($url, strlen('https://'));
        }
        
        // Remove '/' sign
        return trim($url, '/');
    }
    
    /**
     * Returns entry GTIN value from product object.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryGtin($product)
    {
        $gtin = '';
        
        $gtin = $this->getAttributeValue($product, $this->gtinAttr);
        
        // If selected attribute is empty provide alternative
        if (!$gtin) {
            $priority = array(
                'gtin',
                'upc',
                'ean',
                'isbn',
                'jan'
            );
            
            foreach ($priority as $attr) {
                $gtin = $this->getAttributeValue($product, $attr);
                if ($gtin) {
                    break;
                }
            }
        }
        
        // Facebook limit for gtin is 70 characters
        return $this->limitText($this->cleanText($gtin), 70);
    }
    
    /**
     * Returns entry MPN value from product object.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryMpn($product)
    {
        $mpn = '';
        
        $mpn = $this->getAttributeValue($product, $this->mpnAttr);
        
        // Facebook limit for MPN is 70 characters
        return $this->limitText($this->cleanText($mpn), 70);
    }
    
    /**
     * Returns entry brand value from product object.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryBrand($product)
    {
        $brand = '';
        
        $brand = $this->getAttributeValue($product, $this->brandAttr);
        
        // If it is empty use default brand
        if ($brand == '') {
            $brand = $this->defaultBrand;
        }
        
        // If it is still empty use store name
        if ($brand == '') {
            $brand = $this->storeName;
        }
        
        // If it is still empty use domain name
        if ($brand == '') {
            $brand = $this->domainName;
        }
        
        // Facebook limit for brand/mpn/gtin is 70 characters
        return $this->limitText($this->cleanText($brand), 70);
    }
    
    /**
     * Returns entry identifier_exists value.
     *
     * @return string
     */
    public function getEntryIdentifierExists()
    {
        $identifierExists = 'yes';
        
        if ($this->brand == '' && ($this->gtin == '' || $this->mpn == '')) {
            $identifierExists = 'no';
        }
        
        return $identifierExists;
    }
    
    /**
     * Returns age_group value.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryAgeGroup($product)
    {
        $ageGroup = '';
        
        $value = $this->getAttributeValue(
            $product,
            $this->ageGroupAttr,
            false
        );
        
        if (is_array($value)) {
            foreach ($value as $v) {
                $ageGroup = (string) array_search($v, $this->ageGroupMap);
                
                if ($ageGroup) {
                    break;
                }
            }
        } else {
            $ageGroup = (string) array_search(
                $value,
                $this->ageGroupMap
            );
        }
        
        return $ageGroup;
    }
    
    /**
     * Returns expiration_date value as ISO-8601 date.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryExpirationDate($product)
    {
        $expDate = '';
        
        $value = $this->getAttributeValue($product, $this->expirationDateAttr);
        
        if ($value) {
            $expDate = $this->datetimeToIso8601($value);
        }
        
        return $expDate;
    }
    
    /**
     * Returns gender value.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryGender($product)
    {
        $gender = '';
        
        $value = $this->getAttributeValue(
            $product,
            $this->genderAttr,
            false
        );
        
        if (is_array($value)) {
            foreach ($value as $v) {
                $gender = (string) array_search($v, $this->genderMap);
                
                if ($gender) {
                    break;
                }
            }
        } else {
            $gender = (string) array_search(
                $value,
                $this->genderMap
            );
        }
        
        return $gender;
    }
    
    /**
     * Returns item group id based on desired attribute (by default parent SKU).
     *
     * @return string
     */
    public function getEntryItemGroupId()
    {
        $itemGroupId = '';
        
        if ($this->parentProductId && $this->parentProduct) {
            switch ($this->itemGroupIdAttr) {
                case 'sku':
                    $itemGroupId = $this->parentProduct->getSku();
                    break;
                case 'id':
                    $itemGroupId = $this->parentProduct->getId();
                    break;
                default:
                    $itemGroupId = $this->getAttributeValue(
                        $this->parentProduct,
                        $this->itemGroupIdAttr
                    );
            }
            
            // Facebook limit for id is 100 characters
            $itemGroupId = $this->limitText(
                $this->cleanText($itemGroupId),
                100
            );
        }
        
        return $itemGroupId;
    }
    
    /**
     * Returns Google Product Category
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntryGoogleProductCategory($product)
    {
        $gpc = '';
        
        $gpc = $this->getAttributeValue($product, $this->gpcAttr);
        
        if ($gpc == '') {
            $gpc = $this->defaultGpc;
        }
        
        // Facebook limit for Google Product Category is 250 characters
        // Set to 350 due to encoding of > and & for xml format
        // and future deeper categories
        return $this->limitText($this->cleanText($gpc), 350);
    }
    
    /**
     * Returns sale_price_effective_date field value.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEntrySalePriceEffectiveDate($product)
    {
        $sped     = '';
        $spedFrom = '';
        $spedTo   = '';
        
        $spedFrom = $this->getAttributeValue($product, 'special_from_date');
        $spedTo   = $this->getAttributeValue($product, 'special_to_date');
        
        if ($spedFrom) {
            $sped = $this->datetimeToIso8601($spedFrom);
        }
        
        // FB specs allow one date in the feed but then / is needed.
        // That is why "or" is here
        if ($spedFrom || $spedTo) {
            $sped .= '/';
        }
        
        if ($spedTo) {
            $sped .= $this->datetimeToIso8601($spedTo);
        }
        
        return $sped;
    }
    
    /**
     * Returns cleaned string.
     *
     * @param string $str
     * @return string
     */
    public function cleanText($str)
    {
        // Decode HTML entities
        // Strip tags
        // Remove tabs, new lines and replace them with one space
        // Truncate multiple spaces to one space
        // Remove white space chars on both sides
        return trim(
            preg_replace(
                "/ {2,}/",
                " ",
                str_replace(
                    array("\r\n", "\r", "\n", "\t"),
                    " ",
                    strip_tags(
                        html_entity_decode(
                            $str,
                            ENT_HTML5 | ENT_QUOTES,
                            'UTF-8'
                        )
                    )
                )
            )
        );
    }
    
    /**
     * Limits text if text is longer than $limit.
     *
     * @param string $string
     * @param integer $limit
     * @return string
     */
    public function limitText($string, $limit)
    {
        if (function_exists('mb_substr')) {
            $str = mb_substr($string, 0, $limit, 'UTF-8');
        } else {
            $str = substr($string, 0, $limit);
        }
        
        return $str;
    }
    
    /**
     * Returns cleaned url.
     *
     * @param string $url
     * @return string
     */
    public function cleanUrl($url)
    {
        $queryPosition = strpos($url, '?');
        
        // If there is a query string remove it
        if ($queryPosition !== false) {
            $url = substr($url, 0, $queryPosition);
        }
        
        return $url;
    }
    
    /**
     * Checks if URL is valid.
     *
     * @param unknown $url
     * @return boolean
     */
    public function isValidUrl($url)
    {
        if (substr($url, 0, 4) === 'http') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns prepared text for XML.
     * ENT_HTML5 and ENT_XML1 only in PHP 5.4 and above.
     *
     * @param string $str
     * @param string $field
     * @return string
     */
    public function prepareForXml($str, $field)
    {
        $prepared = htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        
        // Some fields need to be limited agin because above converts some chars
        // to entities &quot; &apos; etc.
        switch ($field) {
            case 'id':
                $prepared = $this->limitText($prepared, 100);
                break;
            case 'description':
                $prepared = $this->limitText($prepared, 5000);
                break;
            case 'title':
                $prepared = $this->limitText($prepared, 100);
                break;
            case 'brand':
                $prepared = $this->limitText($prepared, 70);
                break;
            case 'mpn':
                $prepared = $this->limitText($prepared, 70);
                break;
            case 'gtin':
                $prepared = $this->limitText($prepared, 70);
                break;
            default:
                // Already prepared before switch statement
        }
        
        return $prepared;
    }

    /**
     * Main method for generating product feed
     * generate() -> saveToFile() -> writeProducts()
     */
    public function generate()
    {
        $this->directoryPath = $this->buildDirectoryPath();
        
        $stores = Mage::app()->getStores();
        
        foreach ($stores as $store) {
            $this->store       = $store;
            $this->storeId     = $store->getId();
            $this->storeLocale = $this->getConfig(
                'general/locale/code',
                $this->storeId
            );
            
            $this->baseCurrencyCode = strtoupper(
                $store->getBaseCurrencyCode()
            );
            
            $this->defaultCurrencyCode = strtoupper(
                $store->getDefaultCurrencyCode()
            );
            
            $feedEnabled = $this->getConfig(
                'apptrian_facebookcatalog/general/feed_enabled',
                $this->storeId
            );
            
            if (!$feedEnabled) {
                continue;
            }
            
            $this->storeUrl      = $this->buildStoreUrl($store);
            $this->storeMediaUrl = $this->buildStoreMediaUrl($store);
            $this->storeName     = $store->getFrontendName();
            $this->feedFormat    = $this->getFeedFormat($this->storeId);
            $this->filename      = $this->buildFilename(
                $this->storeId,
                $this->feedFormat
            );
            
            $this->pnviAllowed = $this->getConfig(
                'apptrian_facebookcatalog/general/pnvi_allowed',
                $this->storeId
            );
            
            $this->domainName = $this->getDomainNameFromUrl($this->storeUrl);
            
            $this->storeUrlLength = strlen($this->storeUrl);
            $this->productUrlSuffix =  $this->getConfig(
                'catalog/seo/product_url_suffix',
                $this->storeId
            );
            
            $this->taxHelper = Mage::helper('tax');
            
            // "Stores > Cofiguration > Sales > Tax > Calculation Settings
            // > Catalog Prices" configuration value
            $this->taxCatalogFlag = (int) $this->getConfig(
                'tax/calculation/price_includes_tax',
                $this->storeId
            );
            
            // "Stores > Cofiguration > Sales > Tax > Price Display Settings
            // > Display Product Prices In Catalog" configuration value
            // Tax Display values
            // 1 - excluding tax
            // 2 - including tax
            // 3 - including and excluding tax
            $flag = (int) $this->getConfig(
                'tax/display/type',
                $this->storeId
            );
            
            // 0 means price excluding tax, 1 means price including tax
            if ($flag == 1) {
                $this->taxDisplayFlag = 0;
            } else {
                $this->taxDisplayFlag = 1;
            }
            
            $this->idAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/id_attr',
                $this->storeId
            );
            
            $this->availabilityAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/availability_attr',
                $this->storeId
            );
            
            $this->availabilityMap = $this->getFieldValuesMap(
                'availability',
                $this->availabilityValues
            );
            
            $this->conditionAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/condition_attr',
                $this->storeId
            );
            
            $this->conditionMap = $this->getFieldValuesMap(
                'condition',
                $this->conditionValues
            );
            
            $this->descriptionAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/description_attr',
                $this->storeId
            );
            
            $this->titleAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/title_attr',
                $this->storeId
            );
            
            $this->gtinAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/gtin_attr',
                $this->storeId
            );
            
            $this->mpnAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/mpn_attr',
                $this->storeId
            );
            
            $this->brandAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/brand_attr',
                $this->storeId
            );
            
            $this->defaultBrand = $this->getConfig(
                'apptrian_facebookcatalog/field_options/default_brand',
                $this->storeId
            );
            
            $this->isIdentifierExistsEnabled = $this->getConfig(
                'apptrian_facebookcatalog/field_options/identifier_exists',
                $this->storeId
            );
            
            $this->isOverrideEnabled = $this->getConfig(
                'apptrian_facebookcatalog/field_options/override',
                $this->storeId
            );
            
            $this->additionalImageLinkLimit = (int) $this->getConfig(
                'apptrian_facebookcatalog/field_options/additional_image_link',
                $this->storeId
            );
            
            $this->ageGroupAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/age_group_attr',
                $this->storeId
            );
            
            $this->ageGroupMap = $this->getFieldValuesMap(
                'age_group',
                $this->ageGroupValues
            );
            
            $this->expirationDateAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/expiration_date_attr',
                $this->storeId
            );
            
            $this->genderAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/gender_attr',
                $this->storeId
            );
            
            $this->genderMap = $this->getFieldValuesMap(
                'gender',
                $this->genderValues
            );
            
            $this->itemGroupIdAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/item_group_id_attr',
                $this->storeId
            );
            
            $this->gpcAttr = $this->getConfig(
                'apptrian_facebookcatalog/field_options/gpc_attr',
                $this->storeId
            );
            
            $this->defaultGpc = $this->getConfig(
                'apptrian_facebookcatalog/field_options/default_gpc',
                $this->storeId
            );
            
            $this->isSalePriceEnabled = $this->getConfig(
                'apptrian_facebookcatalog/field_options/sale_price',
                $this->storeId
            );
            
            $this->isSalePriceEffectiveDateEnabled = $this->getConfig(
                'apptrian_facebookcatalog/field_options/sale_price_effect_date',
                $this->storeId
            );
            
            $this->map = $this->getFieldToAttributeMap();
            
            $this->saveToFile();
        }
    }

    /**
     * Save feed to a file.
     */
    public function saveToFile()
    {
        $io = new Varien_Io_File();
        
        // Check that path is writable
        $io->open(array('path' => $this->directoryPath));
        if ($io->fileExists($this->directoryPath)
            && !$io->isWriteable($this->directoryPath)
        ) {
            Mage::log('Feed file is not writable.');
            Mage::throwException(
                $this->__(
                    'File "%s" cannot be saved. Please make sure '
                    . 'the path "%s" is writable by web server.',
                    $this->directoryPath
                )
            );
        }
        
        // Delete previous feed file even ones with changed extension
        $formats = array('csv', 'tsv', 'xml-rss');
        
        foreach ($formats as $format) {
            $filename = $this->buildFilename(
                $this->storeId,
                $format
            );
            
            if ($io->fileExists($this->directoryPath . $filename)) {
                $io->rm($this->directoryPath . $filename);
            }
        }
        
        // Open stream
        $io->streamOpen($this->filename);
        
        // Write header
        $io->streamWrite($this->buildHeader() . "\n");
        
        // Check how many products there will be
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($this->storeId);
        $totalNumberOfProducts = $collection->getSize();
        unset($collection);
        
        // Write products
        $this->writeProducts($io, $totalNumberOfProducts, true);
        
        // Write footer
        $footer = $this->buildFooter();
        if ($footer) {
            $io->streamWrite($footer . "\n");
        }
    }
    
    /**
     * Write products to file.
     *
     * @param Varien_Io_File $io
     * @param int $totalNumberOfProducts
     * @param bool $log
     * @throws Exception
     */
    public function writeProducts($io, $totalNumberOfProducts, $log)
    {
        $count = 0;
        $batch = 100;
        $skipCount = 0;
        $exceptionCount = 0;
        $p = null;
        
        $timeLimit = (int) ini_get('max_execution_time');
        if ($timeLimit !== 0 && $timeLimit < 3600) {
            set_time_limit(3600);
        }
        
        while ($count < $totalNumberOfProducts) {
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addStoreFilter($this->storeId)
                ->setPageSize($batch)
                ->setCurPage($count / $batch + 1)
                ->addUrlRewrite();
            
            foreach ($products as $product) {
                try {
                    $p = $this->checkProduct($product);
                    
                    if ($p === 1) {
                        $skipCount++;
                        continue;
                    } elseif ($p === 2) {
                        $skipCount++;
                    } else {
                        $entry = $this->buildProductEntry($p);
                        
                        $io->streamWrite($entry . "\n");
                    }
                } catch (\Exception $e) {
                    $exceptionCount++;
                    // Don't overload the logs, log the first 3 exceptions.
                    if ($exceptionCount <= 3) {
                        Mage::logException($e);
                    }
                    
                    // If it looks like a systemic failure stop feed generation
                    if ($exceptionCount > 100) {
                        throw $e;
                    }
                }
                
                // Reset
                $p = null;
            }
            
            unset($products);
            $count += $batch;
        }
        
        if ($skipCount != 0 && $log) {
            Mage::log(sprintf('Skipped %d products', $skipCount));
        }
    }
    
    /**
     * Check if product should be included in the feed.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int|Mage_Catalog_Model_Product
     */
    public function checkProduct($product)
    {
        $notVis = Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;
        $statusDisabled = Mage_Catalog_Model_Product_Status::STATUS_DISABLED;
        
        // Must be loaded for multistore setups so product data is
        // available for particular store. If it is not loaded it
        // will always give default scope data.
        $product->setStoreId($this->storeId)->load($product->getId());
        
        $productName = $product->getName();
        
        if ($product->getStatus() != $statusDisabled && $productName) {
            if (!$this->pnviAllowed && $product->getVisibility() == $notVis) {
                return 1;
            }
            
            return $product;
        } else {
            return 2;
        }
    }
    
    /**
     * Returns array of store product feed links data.
     *
     * @return array
     */
    public function getProductFeedLinks()
    {
        $data = array();
        
        $stores = Mage::app()->getStores();
        
        foreach ($stores as $store) {
            $storeId       = $store->getId();
            $feedFormat    = $this->getFeedFormat($storeId);
            $feedFilename  = $this->buildFilename($storeId, $feedFormat);
            $storeMediaUrl = $this->buildStoreMediaUrl($store);
            
            $data[$storeId]['filename'] = $feedFilename;
            $data[$storeId]['name']     = $store->getName();
            $data[$storeId]['url']      = $storeMediaUrl . $feedFilename;
        }
        
        return $data;
    }
    
    /**
     * Returns product attribute value or values. Third param is optional, if
     * set to false it will return array of values for multiselect attributes.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attrCode
     * @param bool $toString
     * @return string
     */
    public function getAttributeValue($product, $attrCode, $toString = true)
    {
        $attrValue = '';
        
        if ($product->getData($attrCode)) {
            $attrValue = $product->getAttributeText($attrCode);
            
            if (!$attrValue) {
                $attrValue = $product->getData($attrCode);
            }
        }
        
        if ($toString && is_array($attrValue)) {
            $attrValue = implode(', ', $attrValue);
        }
        
        return $attrValue;
    }
    
    /**
     * Returns array of field values from configuration.
     *
     * @param string $field
     * @param string $values
     * @return array
     */
    public function getFieldValuesMap($field, $values)
    {
        $map = array();
        
        $data = $this->getConfig(
            'apptrian_facebookcatalog/field_options/'. $field . '_map',
            $this->storeId
        );
        
        if (!$data) {
            return $map;
        }
        
        $textValues = explode(',', $data);
        
        if (count($values) == count($textValues)) {
            $i = 0;
            foreach ($values as $value) {
                $map[$value] = trim($textValues[$i]);
                $i++;
            }
        } else {
            Mage::log(
                'The ' . $field . ' map values are incorrect.'
            );
        }
        
        return $map;
    }
    
    /**
     * Returns array map from additional mapping configuration.
     *
     * @return array
     */
    public function getFieldToAttributeMap()
    {
        $map = array();
        
        $data = $this->getConfig(
            'apptrian_facebookcatalog/field_options/additional_mapping',
            $this->storeId
        );
        
        if (!$data) {
            return $map;
        }
        
        $pairs = explode('|', $data);
        
        foreach ($pairs as $pair) {
            $pairArray = explode('=', $pair);
            
            if (isset($pairArray[0]) && isset($pairArray[1])) {
                $cleanedField     = trim($pairArray[0]);
                $cleanedAttribute = trim($pairArray[1]);
                
                if ($cleanedField && $cleanedAttribute) {
                    $map[$cleanedField] = $cleanedAttribute;
                }
            }
        }
        
        return $map;
    }
    
    /**
     * Converts datetime string to ISO 8601 datetime string used for:
     * expiration_date and sale_price_effective_date
     *
     * @param string $datetimeString
     * @return string
     */
    public function datetimeToIso8601($datetimeString)
    {
        return Mage::getSingleton('core/date')->gmtDate('c', $datetimeString);
    }
}
