<?php

class WSC_MageJam_Model_Cms_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Used for retrieving cms pages
     *
     * @param null $store
     * @return array
     */
    public function getPageList($store = null)
    {
        $cmsPages = null;
        if (is_null($store)) {
            $cmsPages = Mage::getModel('cms/page')->getCollection();
        }else{
            $cmsPages = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter($store);
        }

        $pagesResult = array();
		$helper = Mage::helper('cms');	
        $processor = $helper->getPageTemplateProcessor();
        foreach ($cmsPages as $page) {
            $pagesResult[] = $this->_prepareCmsPageData($page, $processor);
        }

        return $pagesResult;
    }

    /**
     * Prepares CMS Page Data for returning
     *
     * @param Mage_Cms_Model_Page $page
     * @param Varien_Filter_Template $processor
     * @return mixed
     */
    protected function _prepareCmsPageData(Mage_Cms_Model_Page $page, Varien_Filter_Template $processor) {
        $result['title'] = $page->getTitle();
        $result['urlKey'] = $page->getIdentifier();
        $result['active'] = $page->getIsActive();
        $result['created_at'] = $page->getCreationTime();
        $result['updated_at'] = $page->getUpdateTime();
		$result['content'] = $processor->filter($page->getContent());

        $pageStoreIds = array();

        $page1 = Mage::getModel('cms/page')->load($page->getId());
        $page_StoreIds = $page1['store_id'];
        foreach ($page_StoreIds as $storeId) {
            $pageStoreIds[] = $storeId;
        }
        $result['storeIds'] = $pageStoreIds;
        return $result;
    }
}