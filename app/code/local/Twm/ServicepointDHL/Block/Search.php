<?php

class Twm_ServicepointDHL_Block_Search extends Mage_Catalog_Block_Product_Abstract
{

	public function _prepareLayout()
    {		
		return parent::_prepareLayout();
    }
	
	public function getPostcode()
    {
        return Mage::app()->getRequest()->getParam('servicepointdhl_postcode');
	}

    public function getCity()
    {
        return Mage::app()->getRequest()->getParam('servicepointdhl_city');
    }
	
}
