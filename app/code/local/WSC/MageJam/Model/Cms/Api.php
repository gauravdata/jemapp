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

        foreach ($cmsPages as $page) {
            $pagesResult[] = $this->_prepareCmsPageData($page);
        }

        return $pagesResult;
    }

    /**
     * Prepares CMS Page Data for returning
     *
     * @param Mage_Cms_Model_Page $page
     * @return mixed
     */
    protected function _prepareCmsPageData(Mage_Cms_Model_Page $page) {
        $result['title'] = $page->getTitle();
        $result['urlKey'] = $page->getIdentifier();
        $result['active'] = $page->getIsActive();
        $result['created_at'] = $page->getCreationTime();
        $result['updated_at'] = $page->getUpdateTime();
        $result['content'] = $page->getContent();
        return $result;
    }
}