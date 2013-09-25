<?php
/**
 * @copyright   Copyright (c) 2009-2011 Amasty (http://www.amasty.com)
 */ 
class Amasty_Shopby_Model_Observer
{
    public function handleControllerFrontInitRouters($observer) 
    {
        $observer->getEvent()->getFront()
            ->addRouter('amshopby', new Amasty_Shopby_Controller_Router());
    }
    
    public function handleCatalogControllerCategoryInitAfter($observer)
    {
        if (!Mage::getStoreConfig('amshopby/seo/urls'))
            return;
            
        $request = $observer->getEvent()->getControllerAction()->getRequest();
        Mage::helper('amshopby/url')->saveParams($request);    
    }
}