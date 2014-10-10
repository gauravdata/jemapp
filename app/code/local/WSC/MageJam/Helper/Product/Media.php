<?php

class WSC_MageJam_Helper_Product_Media extends Mage_Core_Helper_Abstract
{
    /**
     * Default image attribute code set
     *
     * @var array
     */
    protected $_imageDefaultAttributeCodes = array('image', 'small_image', 'thumbnail');

    /**
     * Adds media_gallery to product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addMediaGalleryAttributeToCollection(
        Mage_Catalog_Model_Resource_Product_Collection $productCollection)
    {
        $mediaGalleryAttributeId = Mage::getSingleton('eav/config')
            ->getAttribute('catalog_product', 'media_gallery')
            ->getAttributeId();


        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('catalog_read');

        $select = $connection->select()->reset();
        $select->from(
            array('main' => $resource->getTableName('catalog/product_attribute_media_gallery')),
            array('entity_id', 'value_id', 'file' => 'value')
        );
        $select->joinLeft(
            array('value' => $resource->getTableName('catalog/product_attribute_media_gallery_value')),
            'main.value_id=value.value_id AND value.store_id=' . Mage::app()->getStore()->getId(),
            array('label', 'position', 'disabled')
        );
        $select->joinLeft(
            array('default_value' => $resource->getTableName('catalog/product_attribute_media_gallery_value')),
            'main.value_id=default_value.value_id AND default_value.store_id=0',
            array(
                'label_default' => 'default_value.label',
                'position_default' => 'default_value.position',
                'disabled_default' => 'default_value.disabled'
            )
        );
        $select->where('main.attribute_id=?', $mediaGalleryAttributeId);
        $select->where('main.entity_id IN(?)', $this->_getAllIds($productCollection));
        $select->order('IF(value.position IS NULL, default_value.position, value.position) ASC');

        $mediaGalleryData = $connection->fetchAll($select);

        $mediaGalleryByProductId = array();
        foreach ($mediaGalleryData as $galleryImage) {
            $k = $galleryImage['entity_id'];
            unset($galleryImage['entity_id']);
            if (!isset($mediaGalleryByProductId[$k])) {
                $mediaGalleryByProductId[$k] = array();
            }
            $mediaGalleryByProductId[$k][] = $galleryImage;
        }
        unset($mediaGalleryData);
        foreach ($productCollection as $product) {
            $productId = $product->getEntityId();
            if (isset($mediaGalleryByProductId[$productId])) {
                $product->setData('media_gallery', array('images' => $mediaGalleryByProductId[$productId]));
            }
        }
        unset($mediaGalleryByProductId);
        $productCollection->addAttributeToSelect($this->_imageDefaultAttributeCodes);
        return $productCollection;
    }

    /**
     * Returns media info as array (needed for api)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getMediaInfo(Mage_Catalog_Model_Product $product)
    {
        $galleryData = $product->getData('media_gallery');

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            return array();
        }

        $result = array();

        foreach ($galleryData['images'] as &$image) {
            $result[] = $this->_imageToArray($image, $product);
        }

        return $result;
    }

    /**
     * Converts image to api array data
     *
     * @param array $image
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _imageToArray(&$image, $product)
    {
        $result = array(
            'file'      => $image['file'],
            'label'     => $image['label'] === null ? $image['label_default'] : $image['label'],
            'position'  => $image['position'] === null ? $image['position_default'] : $image['position'],
            'exclude'   => $image['disabled'] === null ? $image['disabled_default'] : $image['disabled'],
            'url'       => Mage::getSingleton('catalog/product_media_config')->getMediaUrl($image['file']),
            'types'     => array()
        );

        foreach ($product->getMediaAttributes() as $attribute) {
            if ($product->getData($attribute->getAttributeCode()) == $image['file']) {
                $result['types'][] = $attribute->getAttributeCode();
            }
        }

        return $result;
    }

    /**
     * Standard function doesn't suit our requirements because it doesn't have order by category position
     *
     * @param $productCollection
     * @return array
     */
    protected function _getAllIds($productCollection)
    {
        $ids = array();
        foreach($productCollection as $product) {
            $ids[] = $product->getId();
        }

        return $ids;
    }
}